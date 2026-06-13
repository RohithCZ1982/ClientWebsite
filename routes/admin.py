import os
import re
from datetime import datetime
from functools import wraps
from flask import Blueprint, render_template, redirect, url_for, flash, request, abort, current_app
from flask_login import login_required, current_user
from werkzeug.utils import secure_filename
from models import db
from models.user import User
from models.blog import BlogPost
from models.contact import ContactEnquiry, JobApplication, NewsletterSubscriber
from models.download import Download, Payment

admin_bp = Blueprint('admin', __name__, url_prefix='/admin')

ALLOWED_EXTENSIONS = {'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'png', 'jpg', 'jpeg'}


def admin_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if not current_user.is_authenticated or not current_user.is_admin:
            abort(403)
        return f(*args, **kwargs)
    return login_required(decorated)


def slugify(text):
    text = text.lower().strip()
    text = re.sub(r'[^\w\s-]', '', text)
    return re.sub(r'[\s_-]+', '-', text)


def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


# ── Dashboard ──────────────────────────────────────────────────────────────────

@admin_bp.route('/')
@admin_required
def dashboard():
    stats = {
        'users': User.query.count(),
        'posts': BlogPost.query.count(),
        'enquiries': ContactEnquiry.query.filter_by(status='new').count(),
        'applications': JobApplication.query.filter_by(status='new').count(),
        'downloads': Download.query.count(),
        'payments': Payment.query.filter_by(status='success').count(),
        'subscribers': NewsletterSubscriber.query.filter_by(is_active=True).count(),
    }
    recent_enquiries = ContactEnquiry.query.order_by(ContactEnquiry.created_at.desc()).limit(5).all()
    recent_payments = Payment.query.order_by(Payment.created_at.desc()).limit(5).all()
    return render_template('admin/dashboard.html', stats=stats,
                           recent_enquiries=recent_enquiries, recent_payments=recent_payments)


# ── Blog ───────────────────────────────────────────────────────────────────────

@admin_bp.route('/blog')
@admin_required
def blog_list():
    posts = BlogPost.query.order_by(BlogPost.created_at.desc()).all()
    return render_template('admin/blog_list.html', posts=posts)


@admin_bp.route('/blog/new', methods=['GET', 'POST'])
@admin_required
def blog_new():
    if request.method == 'POST':
        title = request.form.get('title', '').strip()
        content = request.form.get('content', '').strip()
        summary = request.form.get('summary', '').strip()
        category = request.form.get('category', 'General')
        tags = request.form.get('tags', '').strip()
        image_url = request.form.get('image_url', '').strip()
        publish = request.form.get('publish') == 'on'

        if not title or not content:
            flash('Title and content are required.', 'danger')
            return render_template('admin/blog_form.html', post=None, form_data=request.form)

        base_slug = slugify(title)
        slug = base_slug
        counter = 1
        while BlogPost.query.filter_by(slug=slug).first():
            slug = f'{base_slug}-{counter}'
            counter += 1

        post = BlogPost(
            title=title, slug=slug, content=content, summary=summary,
            category=category, tags=tags, image_url=image_url,
            author=current_user.name,
            is_published=publish,
            published_at=datetime.utcnow() if publish else None,
        )
        db.session.add(post)
        db.session.commit()
        flash('Post created successfully.', 'success')
        return redirect(url_for('admin.blog_list'))

    return render_template('admin/blog_form.html', post=None, form_data={})


@admin_bp.route('/blog/<int:post_id>/edit', methods=['GET', 'POST'])
@admin_required
def blog_edit(post_id):
    post = BlogPost.query.get_or_404(post_id)
    if request.method == 'POST':
        post.title = request.form.get('title', '').strip()
        post.content = request.form.get('content', '').strip()
        post.summary = request.form.get('summary', '').strip()
        post.category = request.form.get('category', 'General')
        post.tags = request.form.get('tags', '').strip()
        post.image_url = request.form.get('image_url', '').strip()
        publish = request.form.get('publish') == 'on'
        if publish and not post.is_published:
            post.published_at = datetime.utcnow()
        post.is_published = publish
        post.updated_at = datetime.utcnow()
        db.session.commit()
        flash('Post updated successfully.', 'success')
        return redirect(url_for('admin.blog_list'))
    return render_template('admin/blog_form.html', post=post, form_data={})


@admin_bp.route('/blog/<int:post_id>/delete', methods=['POST'])
@admin_required
def blog_delete(post_id):
    post = BlogPost.query.get_or_404(post_id)
    db.session.delete(post)
    db.session.commit()
    flash('Post deleted.', 'info')
    return redirect(url_for('admin.blog_list'))


# ── Enquiries ─────────────────────────────────────────────────────────────────

@admin_bp.route('/enquiries')
@admin_required
def enquiries():
    items = ContactEnquiry.query.order_by(ContactEnquiry.created_at.desc()).all()
    return render_template('admin/enquiries.html', enquiries=items)


@admin_bp.route('/enquiries/<int:eid>/read', methods=['POST'])
@admin_required
def mark_enquiry_read(eid):
    enq = ContactEnquiry.query.get_or_404(eid)
    enq.status = 'read'
    db.session.commit()
    return redirect(url_for('admin.enquiries'))


# ── Downloads ─────────────────────────────────────────────────────────────────

@admin_bp.route('/downloads')
@admin_required
def downloads_list():
    items = Download.query.order_by(Download.created_at.desc()).all()
    return render_template('admin/downloads.html', downloads=items)


@admin_bp.route('/downloads/new', methods=['GET', 'POST'])
@admin_required
def download_new():
    if request.method == 'POST':
        title = request.form.get('title', '').strip()
        description = request.form.get('description', '').strip()
        category = request.form.get('category', 'General')
        is_public = request.form.get('is_public') == 'on'
        file = request.files.get('file')

        if not title or not file or file.filename == '':
            flash('Title and file are required.', 'danger')
            return render_template('admin/download_form.html', item=None)

        if not allowed_file(file.filename):
            flash('File type not allowed.', 'danger')
            return render_template('admin/download_form.html', item=None)

        filename = secure_filename(file.filename)
        upload_path = os.path.join(current_app.config['UPLOAD_FOLDER'], filename)
        file.save(upload_path)
        size_bytes = os.path.getsize(upload_path)
        size_str = f'{size_bytes // 1024} KB' if size_bytes < 1048576 else f'{size_bytes // 1048576} MB'

        dl = Download(
            title=title, description=description, filename=filename,
            category=category, file_size=size_str, is_public=is_public,
        )
        db.session.add(dl)
        db.session.commit()
        flash('File uploaded successfully.', 'success')
        return redirect(url_for('admin.downloads_list'))

    return render_template('admin/download_form.html', item=None)


@admin_bp.route('/downloads/<int:did>/delete', methods=['POST'])
@admin_required
def download_delete(did):
    dl = Download.query.get_or_404(did)
    try:
        path = os.path.join(current_app.config['UPLOAD_FOLDER'], dl.filename)
        if os.path.exists(path):
            os.remove(path)
    except Exception:
        pass
    db.session.delete(dl)
    db.session.commit()
    flash('File deleted.', 'info')
    return redirect(url_for('admin.downloads_list'))


# ── Users ─────────────────────────────────────────────────────────────────────

@admin_bp.route('/users')
@admin_required
def users():
    all_users = User.query.order_by(User.created_at.desc()).all()
    return render_template('admin/users.html', users=all_users)


@admin_bp.route('/users/<int:uid>/toggle', methods=['POST'])
@admin_required
def toggle_user(uid):
    user = User.query.get_or_404(uid)
    if user.id == current_user.id:
        flash('Cannot deactivate yourself.', 'warning')
    else:
        user.is_active = not user.is_active
        db.session.commit()
        flash(f'User {"activated" if user.is_active else "deactivated"}.', 'info')
    return redirect(url_for('admin.users'))


# ── Payments ──────────────────────────────────────────────────────────────────

@admin_bp.route('/payments')
@admin_required
def payments():
    items = Payment.query.order_by(Payment.created_at.desc()).all()
    return render_template('admin/payments.html', payments=items)


# ── Subscribers ───────────────────────────────────────────────────────────────

@admin_bp.route('/subscribers')
@admin_required
def subscribers():
    items = NewsletterSubscriber.query.order_by(NewsletterSubscriber.subscribed_at.desc()).all()
    return render_template('admin/subscribers.html', subscribers=items)


# ── Job Applications ──────────────────────────────────────────────────────────

@admin_bp.route('/applications')
@admin_required
def applications():
    items = JobApplication.query.order_by(JobApplication.created_at.desc()).all()
    return render_template('admin/applications.html', applications=items)
