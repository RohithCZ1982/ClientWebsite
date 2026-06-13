import hmac
import hashlib
import json
from flask import Blueprint, render_template, request, jsonify, redirect, url_for, flash, current_app
from flask_login import login_required, current_user
from models import db
from models.download import Payment

payment = Blueprint('payment', __name__, url_prefix='/payment')

PLANS = [
    {
        'id': 'starter',
        'name': 'Starter',
        'price': 4999,      # INR in paise * 100 = ₹4,999 -> 499900 paise
        'price_display': '₹4,999',
        'period': '/month',
        'features': [
            '5 Users', 'Basic Support', '10 GB Storage',
            'Core Analytics', 'Email Support',
        ],
        'highlight': False,
    },
    {
        'id': 'professional',
        'name': 'Professional',
        'price': 14999,
        'price_display': '₹14,999',
        'period': '/month',
        'features': [
            '25 Users', 'Priority Support', '100 GB Storage',
            'Advanced Analytics', 'Phone & Email Support', 'API Access',
        ],
        'highlight': True,
    },
    {
        'id': 'enterprise',
        'name': 'Enterprise',
        'price': 39999,
        'price_display': '₹39,999',
        'period': '/month',
        'features': [
            'Unlimited Users', '24/7 Dedicated Support', '1 TB Storage',
            'Full Analytics Suite', 'Dedicated Account Manager',
            'Custom Integrations', 'SLA Guarantee',
        ],
        'highlight': False,
    },
]


@payment.route('/')
def index():
    return render_template('payment.html', plans=PLANS, razorpay_key=current_app.config['RAZORPAY_KEY_ID'])


@payment.route('/create-order', methods=['POST'])
def create_order():
    """Create a Razorpay order."""
    data = request.get_json()
    plan_id = data.get('plan_id')
    plan = next((p for p in PLANS if p['id'] == plan_id), None)
    if not plan:
        return jsonify({'error': 'Invalid plan'}), 400

    amount_paise = plan['price'] * 100  # convert to paise

    try:
        import razorpay
        client = razorpay.Client(
            auth=(current_app.config['RAZORPAY_KEY_ID'], current_app.config['RAZORPAY_KEY_SECRET'])
        )
        order_data = {
            'amount': amount_paise,
            'currency': 'INR',
            'receipt': f"order_{plan_id}",
            'notes': {'plan': plan['name']},
        }
        rzp_order = client.order.create(data=order_data)

        # Save pending payment to DB
        pmt = Payment(
            razorpay_order_id=rzp_order['id'],
            amount=amount_paise,
            currency='INR',
            description=f"{plan['name']} Plan",
            customer_name=data.get('name', ''),
            customer_email=data.get('email', ''),
            customer_phone=data.get('phone', ''),
            status='pending',
        )
        db.session.add(pmt)
        db.session.commit()

        return jsonify({
            'order_id': rzp_order['id'],
            'amount': amount_paise,
            'currency': 'INR',
            'key': current_app.config['RAZORPAY_KEY_ID'],
        })

    except Exception as e:
        current_app.logger.error(f'Razorpay order creation failed: {e}')
        # Demo mode: return a fake order for testing
        return jsonify({
            'order_id': 'order_demo_123',
            'amount': amount_paise,
            'currency': 'INR',
            'key': current_app.config['RAZORPAY_KEY_ID'],
            'demo': True,
        })


@payment.route('/verify', methods=['POST'])
def verify():
    """Verify Razorpay payment signature and mark order as successful."""
    data = request.get_json()
    razorpay_order_id = data.get('razorpay_order_id', '')
    razorpay_payment_id = data.get('razorpay_payment_id', '')
    razorpay_signature = data.get('razorpay_signature', '')

    secret = current_app.config['RAZORPAY_KEY_SECRET']
    msg = f"{razorpay_order_id}|{razorpay_payment_id}"
    expected = hmac.new(secret.encode(), msg.encode(), hashlib.sha256).hexdigest()

    if hmac.compare_digest(expected, razorpay_signature):
        pmt = Payment.query.filter_by(razorpay_order_id=razorpay_order_id).first()
        if pmt:
            pmt.razorpay_payment_id = razorpay_payment_id
            pmt.razorpay_signature = razorpay_signature
            pmt.status = 'success'
            db.session.commit()
        return jsonify({'status': 'success'})

    return jsonify({'status': 'failed', 'error': 'Signature mismatch'}), 400


@payment.route('/webhook', methods=['POST'])
def webhook():
    """Handle Razorpay webhook events."""
    webhook_secret = current_app.config['RAZORPAY_WEBHOOK_SECRET']
    signature = request.headers.get('X-Razorpay-Signature', '')
    body = request.get_data()

    expected = hmac.new(webhook_secret.encode(), body, hashlib.sha256).hexdigest()
    if not hmac.compare_digest(expected, signature):
        return jsonify({'error': 'Invalid signature'}), 400

    event = request.get_json()
    event_type = event.get('event')

    if event_type == 'payment.captured':
        payment_entity = event['payload']['payment']['entity']
        order_id = payment_entity.get('order_id')
        payment_id = payment_entity.get('id')
        pmt = Payment.query.filter_by(razorpay_order_id=order_id).first()
        if pmt:
            pmt.razorpay_payment_id = payment_id
            pmt.status = 'success'
            db.session.commit()

    elif event_type == 'payment.failed':
        payment_entity = event['payload']['payment']['entity']
        order_id = payment_entity.get('order_id')
        pmt = Payment.query.filter_by(razorpay_order_id=order_id).first()
        if pmt:
            pmt.status = 'failed'
            db.session.commit()

    return jsonify({'status': 'ok'})


@payment.route('/success')
def success():
    return render_template('payment_success.html')


@payment.route('/failed')
def failed():
    return render_template('payment_failed.html')
