from flask import Blueprint, render_template, current_app
from models.blog import BlogPost
from models.download import Download

main = Blueprint('main', __name__)

SERVICES = [
    {
        'icon': 'bi-cpu',
        'title': 'Digital Transformation',
        'desc': 'End-to-end digital transformation services that modernize your business processes and drive innovation.',
        'slug': 'digital-transformation',
    },
    {
        'icon': 'bi-cloud-arrow-up',
        'title': 'Cloud Solutions',
        'desc': 'Scalable cloud infrastructure, migration, and managed services on AWS, Azure, and Google Cloud.',
        'slug': 'cloud-solutions',
    },
    {
        'icon': 'bi-shield-lock',
        'title': 'Cybersecurity',
        'desc': 'Comprehensive security assessments, threat monitoring, and compliance services to protect your assets.',
        'slug': 'cybersecurity',
    },
    {
        'icon': 'bi-robot',
        'title': 'AI & Machine Learning',
        'desc': 'Custom AI/ML solutions, predictive analytics, and intelligent automation to accelerate growth.',
        'slug': 'ai-ml',
    },
    {
        'icon': 'bi-phone',
        'title': 'Mobile & Web Development',
        'desc': 'Modern, responsive web and mobile applications built with the latest technologies.',
        'slug': 'mobile-web',
    },
    {
        'icon': 'bi-people',
        'title': 'IT Staffing & Consulting',
        'desc': 'On-demand access to top-tier technology talent for project-based or permanent roles.',
        'slug': 'it-staffing',
    },
]

TESTIMONIALS = [
    {
        'name': 'Sarah Johnson',
        'title': 'CTO, RetailMax Inc.',
        'avatar': 'SJ',
        'text': 'TechCorp transformed our legacy infrastructure into a modern cloud-native platform. The team delivered on time and exceeded our performance targets.',
        'rating': 5,
    },
    {
        'name': 'Michael Chen',
        'title': 'VP Engineering, FinTech Partners',
        'avatar': 'MC',
        'text': 'Exceptional AI/ML implementation that reduced our fraud detection time by 70%. Truly a world-class engineering team.',
        'rating': 5,
    },
    {
        'name': 'Priya Sharma',
        'title': 'CEO, HealthBridge Solutions',
        'avatar': 'PS',
        'text': 'Their cybersecurity audit and remediation saved us from a potential breach. Professional, thorough, and highly responsive.',
        'rating': 5,
    },
]

TEAM = [
    {
        'name': 'James Mitchell',
        'title': 'Chief Executive Officer',
        'bio': 'With 20+ years in enterprise technology, James leads TechCorp\'s vision of making cutting-edge IT accessible to businesses of all sizes.',
        'linkedin': '#',
        'twitter': '#',
        'initials': 'JM',
        'color': 'primary',
    },
    {
        'name': 'Ananya Reddy',
        'title': 'Chief Technology Officer',
        'bio': 'Ananya drives innovation and architecture decisions, with deep expertise in cloud-native systems, AI, and cybersecurity frameworks.',
        'linkedin': '#',
        'twitter': '#',
        'initials': 'AR',
        'color': 'success',
    },
    {
        'name': 'Robert Kim',
        'title': 'VP, Delivery & Operations',
        'bio': 'Robert oversees global delivery across 12 time zones, ensuring projects are delivered on time, on scope, and on budget.',
        'linkedin': '#',
        'twitter': '#',
        'initials': 'RK',
        'color': 'warning',
    },
    {
        'name': 'Lisa Torres',
        'title': 'Head of Customer Success',
        'bio': 'Lisa champions client partnerships, maintaining a 98% satisfaction rate through proactive communication and strategic guidance.',
        'linkedin': '#',
        'twitter': '#',
        'initials': 'LT',
        'color': 'info',
    },
]

JOBS = [
    {
        'id': 1,
        'title': 'Senior Cloud Architect',
        'location': 'Remote / San Francisco, CA',
        'type': 'Full-time',
        'department': 'Engineering',
        'desc': 'Design and implement scalable cloud solutions on AWS/Azure. 8+ years experience required.',
    },
    {
        'id': 2,
        'title': 'AI/ML Engineer',
        'location': 'Remote',
        'type': 'Full-time',
        'department': 'Engineering',
        'desc': 'Build and deploy machine learning models for enterprise clients. Python, TensorFlow/PyTorch required.',
    },
    {
        'id': 3,
        'title': 'Cybersecurity Analyst',
        'location': 'New York, NY',
        'type': 'Full-time',
        'department': 'Security',
        'desc': 'Conduct security assessments, threat modeling, and incident response. CISSP preferred.',
    },
    {
        'id': 4,
        'title': 'Project Manager',
        'location': 'Chicago, IL / Remote',
        'type': 'Full-time',
        'department': 'Operations',
        'desc': 'Lead multi-disciplinary technology projects. PMP certified, Agile/Scrum experience required.',
    },
    {
        'id': 5,
        'title': 'Full Stack Developer',
        'location': 'Remote',
        'type': 'Contract',
        'department': 'Engineering',
        'desc': 'React/Vue frontend + Python/Node backend. 5+ years experience. Portfolio required.',
    },
]

STATS = [
    {'value': '500+', 'label': 'Projects Delivered'},
    {'value': '200+', 'label': 'Enterprise Clients'},
    {'value': '15+', 'label': 'Years Experience'},
    {'value': '98%', 'label': 'Client Satisfaction'},
]


@main.route('/')
def index():
    posts = BlogPost.query.filter_by(is_published=True).order_by(BlogPost.published_at.desc()).limit(3).all()
    return render_template(
        'index.html',
        services=SERVICES[:6],
        testimonials=TESTIMONIALS,
        stats=STATS,
        posts=posts,
    )


@main.route('/about')
def about():
    return render_template('about.html', team=TEAM, stats=STATS)


@main.route('/services')
def services():
    return render_template('services.html', services=SERVICES)


@main.route('/services/<slug>')
def service_detail(slug):
    service = next((s for s in SERVICES if s['slug'] == slug), None)
    if not service:
        return render_template('404.html'), 404
    return render_template('service_detail.html', service=service, services=SERVICES)


@main.route('/team')
def team():
    return render_template('team.html', team=TEAM)


@main.route('/careers')
def careers():
    return render_template('careers.html', jobs=JOBS)


@main.route('/downloads')
def downloads():
    items = Download.query.filter_by(is_public=True).order_by(Download.created_at.desc()).all()
    return render_template('downloads.html', downloads=items)


@main.app_errorhandler(404)
def page_not_found(e):
    return render_template('404.html'), 404


@main.app_errorhandler(500)
def internal_error(e):
    return render_template('500.html'), 500
