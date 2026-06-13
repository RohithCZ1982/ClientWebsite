import os
from dotenv import load_dotenv

load_dotenv()

BASE_DIR = os.path.abspath(os.path.dirname(__file__))


class Config:
    SECRET_KEY = os.environ.get('SECRET_KEY', 'dev-secret-key-change-in-prod')
    SQLALCHEMY_DATABASE_URI = os.environ.get(
        'DATABASE_URL', f'sqlite:///{os.path.join(BASE_DIR, "instance", "site.db")}'
    )
    SQLALCHEMY_TRACK_MODIFICATIONS = False

    MAIL_SERVER = os.environ.get('MAIL_SERVER', 'smtp.gmail.com')
    MAIL_PORT = int(os.environ.get('MAIL_PORT', 587))
    MAIL_USE_TLS = os.environ.get('MAIL_USE_TLS', 'True') == 'True'
    MAIL_USERNAME = os.environ.get('MAIL_USERNAME')
    MAIL_PASSWORD = os.environ.get('MAIL_PASSWORD')
    MAIL_DEFAULT_SENDER = os.environ.get('MAIL_DEFAULT_SENDER')
    CONTACT_RECIPIENT = os.environ.get('CONTACT_RECIPIENT', 'admin@example.com')

    RAZORPAY_KEY_ID = os.environ.get('RAZORPAY_KEY_ID', 'rzp_test_demo')
    RAZORPAY_KEY_SECRET = os.environ.get('RAZORPAY_KEY_SECRET', 'demo_secret')
    RAZORPAY_WEBHOOK_SECRET = os.environ.get('RAZORPAY_WEBHOOK_SECRET', 'webhook_secret')

    ADMIN_EMAIL = os.environ.get('ADMIN_EMAIL', 'admin@example.com')
    ADMIN_PASSWORD = os.environ.get('ADMIN_PASSWORD', 'admin123')

    UPLOAD_FOLDER = os.path.join(BASE_DIR, 'static', 'downloads')
    MAX_CONTENT_LENGTH = 16 * 1024 * 1024  # 16 MB

    COMPANY_NAME = 'TechCorp Solutions'
    COMPANY_TAGLINE = 'Innovating the Future, Today'
    COMPANY_EMAIL = 'info@techcorpsolutions.com'
    COMPANY_PHONE = '+1 (800) 123-4567'
    COMPANY_ADDRESS = '123 Tech Avenue, Silicon Valley, CA 94025'
    COMPANY_LINKEDIN = 'https://linkedin.com/company/techcorp'
    COMPANY_TWITTER = 'https://twitter.com/techcorp'
    COMPANY_FACEBOOK = 'https://facebook.com/techcorp'
    COMPANY_YOUTUBE = 'https://youtube.com/techcorp'


class DevelopmentConfig(Config):
    DEBUG = True
    FLASK_ENV = 'development'


class ProductionConfig(Config):
    DEBUG = False
    FLASK_ENV = 'production'


config = {
    'development': DevelopmentConfig,
    'production': ProductionConfig,
    'default': DevelopmentConfig,
}
