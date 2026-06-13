from flask import Blueprint, render_template, request, flash, redirect, url_for, current_app
from models import db
from models.contact import ContactEnquiry, JobApplication, NewsletterSubscriber

contact = Blueprint('contact', __name__)


def try_send_email(app, recipient, subject, body):
    """Send email if mail is configured, otherwise log to console."""
    try:
        from flask_mail import Message
        mail = app.extensions.get('mail')
        if mail and app.config.get('MAIL_USERNAME'):
            msg = Message(subject, recipients=[recipient], body=body)
            mail.send(msg)
    except Exception as e:
        app.logger.warning(f'Email send failed (check MAIL_* config): {e}')


@contact.route('/contact', methods=['GET', 'POST'])
def contact_page():
    if request.method == 'POST':
        name = request.form.get('name', '').strip()
        email = request.form.get('email', '').strip()
        phone = request.form.get('phone', '').strip()
        company = request.form.get('company', '').strip()
        subject = request.form.get('subject', '').strip()
        message = request.form.get('message', '').strip()

        errors = []
        if not name:
            errors.append('Name is required.')
        if not email or '@' not in email:
            errors.append('Valid email is required.')
        if not message:
            errors.append('Message is required.')

        if errors:
            for e in errors:
                flash(e, 'danger')
            return render_template('contact.html', form_data=request.form)

        enquiry = ContactEnquiry(
            name=name, email=email, phone=phone,
            company=company, subject=subject, message=message,
        )
        db.session.add(enquiry)
        db.session.commit()

        body = (
            f"New Enquiry from {name}\n"
            f"Email: {email}\nPhone: {phone}\nCompany: {company}\n"
            f"Subject: {subject}\n\nMessage:\n{message}"
        )
        try_send_email(
            current_app._get_current_object(),
            current_app.config['CONTACT_RECIPIENT'],
            f'New Enquiry: {subject or name}',
            body,
        )

        flash('Thank you! Your message has been received. We\'ll be in touch within 24 hours.', 'success')
        return redirect(url_for('contact.contact_page'))

    return render_template('contact.html', form_data={})


@contact.route('/careers/apply', methods=['POST'])
def apply():
    name = request.form.get('name', '').strip()
    email = request.form.get('email', '').strip()
    phone = request.form.get('phone', '').strip()
    position = request.form.get('position', '').strip()
    experience = request.form.get('experience', '').strip()
    message = request.form.get('message', '').strip()

    if not name or not email:
        flash('Name and email are required.', 'danger')
        return redirect(url_for('main.careers'))

    app_obj = JobApplication(
        name=name, email=email, phone=phone,
        position=position, experience=experience, message=message,
    )
    db.session.add(app_obj)
    db.session.commit()
    flash('Application submitted! We\'ll review and contact you soon.', 'success')
    return redirect(url_for('main.careers'))


@contact.route('/newsletter/subscribe', methods=['POST'])
def newsletter_subscribe():
    email = request.form.get('email', '').strip().lower()
    if not email or '@' not in email:
        flash('Please enter a valid email address.', 'danger')
        return redirect(request.referrer or url_for('main.index'))

    existing = NewsletterSubscriber.query.filter_by(email=email).first()
    if existing:
        if not existing.is_active:
            existing.is_active = True
            db.session.commit()
            flash('You\'ve been re-subscribed to our newsletter!', 'success')
        else:
            flash('You\'re already subscribed!', 'info')
    else:
        sub = NewsletterSubscriber(email=email)
        db.session.add(sub)
        db.session.commit()
        flash('Thank you for subscribing to our newsletter!', 'success')

    return redirect(request.referrer or url_for('main.index'))
