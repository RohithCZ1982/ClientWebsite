from datetime import datetime
from models import db


class BlogPost(db.Model):
    __tablename__ = 'blog_posts'

    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(200), nullable=False)
    slug = db.Column(db.String(200), unique=True, nullable=False)
    summary = db.Column(db.Text)
    content = db.Column(db.Text, nullable=False)
    category = db.Column(db.String(80), default='General')
    tags = db.Column(db.String(200))
    image_url = db.Column(db.String(300))
    author = db.Column(db.String(120), default='Admin')
    is_published = db.Column(db.Boolean, default=False)
    views = db.Column(db.Integer, default=0)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    published_at = db.Column(db.DateTime)

    def __repr__(self):
        return f'<BlogPost {self.title}>'
