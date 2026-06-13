import re
from datetime import datetime
from flask import Blueprint, render_template, abort, request
from models.blog import BlogPost

blog = Blueprint('blog', __name__, url_prefix='/news')

CATEGORIES = ['General', 'Technology', 'Security', 'Cloud', 'AI/ML', 'Company News', 'Case Studies']


def slugify(text):
    text = text.lower().strip()
    text = re.sub(r'[^\w\s-]', '', text)
    return re.sub(r'[\s_-]+', '-', text)


@blog.route('/')
def index():
    page = request.args.get('page', 1, type=int)
    category = request.args.get('category', '')
    query = BlogPost.query.filter_by(is_published=True)
    if category:
        query = query.filter_by(category=category)
    posts = query.order_by(BlogPost.published_at.desc()).paginate(page=page, per_page=9)
    return render_template('blog/index.html', posts=posts, categories=CATEGORIES, active_category=category)


@blog.route('/<slug>')
def detail(slug):
    post = BlogPost.query.filter_by(slug=slug, is_published=True).first_or_404()
    post.views += 1
    from models import db
    db.session.commit()
    related = (
        BlogPost.query.filter(
            BlogPost.is_published == True,
            BlogPost.id != post.id,
            BlogPost.category == post.category,
        )
        .order_by(BlogPost.published_at.desc())
        .limit(3)
        .all()
    )
    import markdown
    content_html = markdown.markdown(post.content, extensions=['fenced_code', 'tables', 'nl2br'])
    return render_template('blog/detail.html', post=post, related=related, content_html=content_html)
