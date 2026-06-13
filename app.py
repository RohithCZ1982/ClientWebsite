import os
from flask import Flask, send_from_directory, abort
from flask_mail import Mail
from config import config
from models import db, login_manager

mail = Mail()


def create_app(env=None):
    app = Flask(__name__, instance_relative_config=True)

    env = env or os.environ.get('FLASK_ENV', 'default')
    app.config.from_object(config.get(env, config['default']))

    os.makedirs(app.instance_path, exist_ok=True)
    os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)

    # Security headers
    @app.after_request
    def set_security_headers(response):
        response.headers['X-Content-Type-Options'] = 'nosniff'
        response.headers['X-Frame-Options'] = 'SAMEORIGIN'
        response.headers['X-XSS-Protection'] = '1; mode=block'
        response.headers['Referrer-Policy'] = 'strict-origin-when-cross-origin'
        return response

    db.init_app(app)
    login_manager.init_app(app)
    mail.init_app(app)

    from routes.main import main
    from routes.auth import auth
    from routes.contact import contact
    from routes.blog import blog
    from routes.payment import payment
    from routes.admin import admin_bp

    app.register_blueprint(main)
    app.register_blueprint(auth)
    app.register_blueprint(contact)
    app.register_blueprint(blog)
    app.register_blueprint(payment)
    app.register_blueprint(admin_bp)

    # Download file serving route
    @app.route('/downloads/file/<filename>')
    def serve_download(filename):
        dl_folder = app.config['UPLOAD_FOLDER']
        filepath = os.path.join(dl_folder, filename)
        if not os.path.exists(filepath):
            abort(404)
        from models.download import Download
        record = Download.query.filter_by(filename=filename).first()
        if record:
            record.download_count += 1
            db.session.commit()
        return send_from_directory(dl_folder, filename, as_attachment=True)

    # Sitemap
    @app.route('/sitemap.xml')
    def sitemap():
        from flask import Response
        from models.blog import BlogPost
        base = 'https://yoursite.com'
        pages = [
            ('/', '1.0', 'weekly'),
            ('/about', '0.8', 'monthly'),
            ('/services', '0.8', 'monthly'),
            ('/team', '0.7', 'monthly'),
            ('/careers', '0.7', 'weekly'),
            ('/news/', '0.9', 'daily'),
            ('/contact', '0.6', 'monthly'),
            ('/downloads', '0.6', 'weekly'),
        ]
        posts = BlogPost.query.filter_by(is_published=True).all()
        xml = ['<?xml version="1.0" encoding="UTF-8"?>',
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">']
        for loc, pri, freq in pages:
            xml.append(f'<url><loc>{base}{loc}</loc><priority>{pri}</priority>'
                       f'<changefreq>{freq}</changefreq></url>')
        for p in posts:
            xml.append(f'<url><loc>{base}/news/{p.slug}</loc>'
                       f'<priority>0.7</priority><changefreq>monthly</changefreq></url>')
        xml.append('</urlset>')
        return Response('\n'.join(xml), mimetype='application/xml')

    with app.app_context():
        db.create_all()
        _seed_data(app)

    return app


def _seed_data(app):
    """Seed initial admin user and sample data if DB is empty."""
    from models.user import User
    from models.blog import BlogPost
    from models.download import Download
    from datetime import datetime

    if not User.query.filter_by(is_admin=True).first():
        admin = User(
            name='Admin User',
            email=app.config['ADMIN_EMAIL'],
            is_admin=True,
        )
        admin.set_password(app.config['ADMIN_PASSWORD'])
        db.session.add(admin)
        db.session.commit()
        app.logger.info(f'Admin user created: {app.config["ADMIN_EMAIL"]}')

    if BlogPost.query.count() == 0:
        sample_posts = [
            BlogPost(
                title='TechCorp Launches Next-Gen Cloud Platform',
                slug='techcorp-launches-next-gen-cloud-platform',
                summary='Our new cloud platform offers 40% better performance at reduced cost.',
                content="""## Introducing CloudPilot 2.0

We are thrilled to announce the launch of **CloudPilot 2.0**, our next-generation cloud management platform designed to help enterprises scale faster and smarter.

### Key Features

- **Auto-scaling** based on real-time demand
- **Cost optimization** engine that reduces spend by up to 40%
- **Unified dashboard** for multi-cloud environments (AWS, Azure, GCP)
- **Built-in security** compliance checks for SOC 2, ISO 27001

### What Our Clients Say

> "CloudPilot transformed how we manage our infrastructure. We cut costs by 35% in the first quarter." — CTO, RetailMax

[Get in touch](/contact) to schedule a demo today!
""",
                category='Company News',
                author='Admin User',
                is_published=True,
                published_at=datetime(2026, 5, 15),
                image_url='https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80',
                tags='cloud, platform, launch',
            ),
            BlogPost(
                title='5 Cybersecurity Trends Every CTO Should Know in 2026',
                slug='cybersecurity-trends-2026',
                summary='From AI-powered threats to zero-trust architecture—stay ahead of the curve.',
                content="""## The Threat Landscape Is Evolving

Cybersecurity is no longer just an IT concern—it's a boardroom priority. Here are five trends reshaping the field in 2026.

### 1. AI-Powered Attacks

Threat actors are increasingly using generative AI to craft convincing phishing campaigns and automate exploit development. Organizations must respond with AI-driven defenses.

### 2. Zero-Trust Architecture

The perimeter is dead. Zero-trust mandates verification of every user, device, and request—inside and outside the network.

### 3. Supply Chain Security

High-profile software supply chain attacks have made vendor risk management a top priority. Continuous monitoring of third-party dependencies is essential.

### 4. Quantum-Resistant Cryptography

NIST has finalized post-quantum cryptographic standards. Organizations should begin migration planning now to avoid future exposure.

### 5. Regulatory Expansion

From the EU's NIS2 to India's DPDP Act, compliance requirements are multiplying. Proactive compliance frameworks save time and costly penalties.

---

**Need a cybersecurity assessment?** [Contact our security team](/contact) for a free consultation.
""",
                category='Security',
                author='Admin User',
                is_published=True,
                published_at=datetime(2026, 5, 28),
                image_url='https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=800&q=80',
                tags='cybersecurity, trends, zero-trust',
            ),
            BlogPost(
                title='How AI is Transforming Enterprise Software Development',
                slug='ai-transforming-enterprise-software-development',
                summary='From code generation to automated testing—AI is rewriting the rules of software delivery.',
                content="""## AI in the Developer Toolchain

The software development lifecycle is undergoing its most significant transformation in decades. AI-powered tools are accelerating every phase—from requirements to deployment.

### Code Generation & Completion

Tools like GitHub Copilot and Amazon CodeWhisperer now generate entire functions from natural language prompts, reducing boilerplate effort by up to 50%.

### Automated Testing

AI-driven test generation tools analyze code paths and produce comprehensive unit and integration tests, dramatically improving coverage without manual effort.

### Intelligent Code Review

Machine learning models trained on millions of code repositories can now identify security vulnerabilities, performance bottlenecks, and design anti-patterns in real time.

### The Road Ahead

As large language models become more capable, we anticipate AI handling full feature development from specification to deployment—with humans in the review loop.

---

Ready to modernize your development process? [Talk to our AI/ML team](/contact).
""",
                category='AI/ML',
                author='Admin User',
                is_published=True,
                published_at=datetime(2026, 6, 5),
                image_url='https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=800&q=80',
                tags='AI, ML, development, automation',
            ),
        ]
        for p in sample_posts:
            db.session.add(p)

    if Download.query.count() == 0:
        sample_downloads = [
            Download(
                title='Company Brochure 2026',
                description='Overview of TechCorp services, capabilities, and client success stories.',
                filename='techcorp-brochure-2026.pdf',
                category='Brochures',
                file_size='2.4 MB',
                is_public=True,
            ),
            Download(
                title='Cloud Migration Checklist',
                description='A step-by-step guide for planning and executing enterprise cloud migrations.',
                filename='cloud-migration-checklist.pdf',
                category='Guides',
                file_size='1.1 MB',
                is_public=True,
            ),
            Download(
                title='Cybersecurity Assessment Template',
                description='Use this template to evaluate your organization\'s security posture.',
                filename='security-assessment-template.xlsx',
                category='Templates',
                file_size='340 KB',
                is_public=True,
            ),
        ]
        for d in sample_downloads:
            db.session.add(d)

    db.session.commit()


app = create_app()

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
