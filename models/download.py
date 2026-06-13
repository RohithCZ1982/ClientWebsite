from datetime import datetime
from models import db


class Download(db.Model):
    __tablename__ = 'downloads'

    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(200), nullable=False)
    description = db.Column(db.Text)
    filename = db.Column(db.String(200), nullable=False)
    category = db.Column(db.String(80), default='General')
    file_size = db.Column(db.String(20))
    download_count = db.Column(db.Integer, default=0)
    is_public = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def __repr__(self):
        return f'<Download {self.title}>'


class Payment(db.Model):
    __tablename__ = 'payments'

    id = db.Column(db.Integer, primary_key=True)
    razorpay_order_id = db.Column(db.String(100), unique=True)
    razorpay_payment_id = db.Column(db.String(100))
    razorpay_signature = db.Column(db.String(200))
    amount = db.Column(db.Integer)  # amount in paise
    currency = db.Column(db.String(10), default='INR')
    description = db.Column(db.String(200))
    customer_name = db.Column(db.String(120))
    customer_email = db.Column(db.String(120))
    customer_phone = db.Column(db.String(20))
    status = db.Column(db.String(20), default='pending')  # pending, success, failed
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    def __repr__(self):
        return f'<Payment {self.razorpay_order_id}: {self.status}>'
