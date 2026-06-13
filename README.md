# TechCorp Solutions — Corporate Website

A complete, production-ready Flask corporate website with 11 pages, admin panel, Razorpay payments, blog CMS, and more.

## Features

| Category | What's Included |
|----------|----------------|
| **Pages** | Home, About, Services, Team, Careers, News/Blog, Contact, Downloads, Sign Up, Login, Pricing/Payment |
| **Admin Panel** | Dashboard, Blog CMS, File Uploads, Enquiry Manager, Job Applications, User Management, Payment Records, Newsletter Subscribers |
| **Payments** | Razorpay integration with order creation, signature verification, and webhook support |
| **Blog** | Markdown-based posts with categories, tags, related posts, view counter, social sharing |
| **Auth** | User registration, login, session management with Flask-Login |
| **Email** | Contact form → email via Flask-Mail (falls back to console in dev) |
| **Security** | Security headers, password hashing, CSRF-ready, admin-only routes |
| **SEO** | Meta tags, Open Graph, sitemap.xml, clean URLs |
| **Design** | Bootstrap 5, custom CSS, Inter font, responsive (desktop/tablet/mobile) |

---

## Quick Start (Local)

```bash
# 1. Clone and enter project
cd ClientWebsite

# 2. Create virtual environment
python3 -m venv venv
source venv/bin/activate        # Windows: venv\Scripts\activate

# 3. Install dependencies
pip install -r requirements.txt

# 4. Set environment variables
cp .env.example .env
# Edit .env with your values

# 5. Run the app
flask run
# OR
python app.py
```

Open **http://localhost:5000** in your browser.

### Default Admin Login
- Email: `admin@example.com` (or whatever `ADMIN_EMAIL` is set to in `.env`)
- Password: `changeme123` (or `ADMIN_PASSWORD`)
- Admin panel: **http://localhost:5000/admin**

---

## Environment Variables (`.env`)

| Variable | Description | Required |
|----------|-------------|----------|
| `SECRET_KEY` | Flask secret key (generate a random one) | Yes |
| `ADMIN_EMAIL` | Initial admin account email | Yes |
| `ADMIN_PASSWORD` | Initial admin account password | Yes |
| `MAIL_SERVER` | SMTP server (e.g. smtp.gmail.com) | For email |
| `MAIL_USERNAME` | SMTP username | For email |
| `MAIL_PASSWORD` | SMTP app password | For email |
| `CONTACT_RECIPIENT` | Where enquiry emails are sent | For email |
| `RAZORPAY_KEY_ID` | Razorpay public key (`rzp_test_...`) | For payments |
| `RAZORPAY_KEY_SECRET` | Razorpay secret key | For payments |
| `RAZORPAY_WEBHOOK_SECRET` | Razorpay webhook signing secret | For webhooks |

---

## Razorpay Setup

1. Sign up at [razorpay.com](https://razorpay.com)
2. Go to **Settings → API Keys** → Generate test keys
3. Add to `.env`:
   ```
   RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxx
   RAZORPAY_KEY_SECRET=your_secret_here
   ```
4. For webhooks: Settings → Webhooks → Add URL `https://yourdomain.com/payment/webhook`
5. Copy the webhook secret to `RAZORPAY_WEBHOOK_SECRET`
6. Switch to live keys (`rzp_live_...`) for production

---

## Customization

### Change Company Info
Edit `config.py`:
```python
COMPANY_NAME = 'Your Company Name'
COMPANY_TAGLINE = 'Your Tagline'
COMPANY_EMAIL = 'info@yourcompany.com'
# etc.
```

### Change Services / Team / Jobs
Edit the lists in `routes/main.py` — `SERVICES`, `TEAM`, `JOBS`.

### Add Blog Posts
- Via admin panel: `/admin/blog/new`
- Posts support full Markdown including headers, code blocks, images, tables

### Upload Resources/Downloads
- Via admin panel: `/admin/downloads/new`
- Files stored in `static/downloads/`

### Change Colors / Fonts
Edit `static/css/style.css` — CSS variables at the top:
```css
:root {
  --primary: #0d6efd;  /* Change to your brand color */
  --dark-navy: #0d1b2a;
}
```

---

## Project Structure

```
ClientWebsite/
├── app.py                  # Main Flask app factory + seeding
├── config.py               # Configuration classes
├── requirements.txt        # Python dependencies
├── .env.example            # Environment template
├── Procfile                # Gunicorn for deployment
├── nginx.conf.example      # Nginx reverse proxy config
├── models/
│   ├── __init__.py         # SQLAlchemy + LoginManager setup
│   ├── user.py             # User model
│   ├── blog.py             # BlogPost model
│   ├── contact.py          # Enquiry, JobApplication, Newsletter models
│   └── download.py         # Download, Payment models
├── routes/
│   ├── main.py             # Public pages (home, about, services, team, etc.)
│   ├── auth.py             # Sign up, login, logout
│   ├── contact.py          # Contact form, job applications, newsletter
│   ├── blog.py             # Blog listing and detail
│   ├── payment.py          # Razorpay integration
│   └── admin.py            # Admin panel (protected)
├── templates/
│   ├── base.html           # Base layout with navbar + footer
│   ├── index.html          # Home page
│   ├── about.html
│   ├── services.html
│   ├── service_detail.html
│   ├── team.html
│   ├── careers.html
│   ├── contact.html
│   ├── downloads.html
│   ├── signup.html
│   ├── login.html
│   ├── payment.html
│   ├── payment_success.html
│   ├── payment_failed.html
│   ├── 404.html
│   ├── 500.html
│   ├── blog/
│   │   ├── index.html
│   │   └── detail.html
│   └── admin/
│       ├── base.html
│       ├── dashboard.html
│       ├── blog_list.html / blog_form.html
│       ├── enquiries.html
│       ├── downloads.html / download_form.html
│       ├── users.html
│       ├── payments.html
│       ├── subscribers.html
│       └── applications.html
└── static/
    ├── css/style.css
    ├── js/main.js
    └── downloads/          # Uploaded resource files
```

---

## Deployment on Hostinger VPS

### Step 1: VPS Setup
```bash
# SSH into your VPS
ssh root@your-vps-ip

# Update system
apt update && apt upgrade -y

# Install Python, Nginx, and tools
apt install python3 python3-pip python3-venv nginx certbot python3-certbot-nginx -y
```

### Step 2: Upload Files
```bash
# Option A: Git
git clone https://github.com/yourusername/ClientWebsite.git /var/www/clientwebsite

# Option B: SCP from local machine
scp -r ./ClientWebsite root@your-vps-ip:/var/www/clientwebsite
```

### Step 3: Install Dependencies
```bash
cd /var/www/clientwebsite
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### Step 4: Configure Environment
```bash
cp .env.example .env
nano .env  # fill in production values
```

### Step 5: Test with Gunicorn
```bash
source venv/bin/activate
gunicorn app:app --workers 2 --bind 0.0.0.0:5000
# Visit http://your-vps-ip:5000 to verify
```

### Step 6: Systemd Service
```bash
nano /etc/systemd/system/clientwebsite.service
```
```ini
[Unit]
Description=TechCorp Flask App
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/clientwebsite
Environment="PATH=/var/www/clientwebsite/venv/bin"
ExecStart=/var/www/clientwebsite/venv/bin/gunicorn app:app --workers 2 --bind 127.0.0.1:5000
Restart=always

[Install]
WantedBy=multi-user.target
```
```bash
systemctl enable clientwebsite
systemctl start clientwebsite
systemctl status clientwebsite
```

### Step 7: Nginx + SSL
```bash
cp nginx.conf.example /etc/nginx/sites-available/clientwebsite
# Edit: replace yourdomain.com with your actual domain
nano /etc/nginx/sites-available/clientwebsite
ln -s /etc/nginx/sites-available/clientwebsite /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# SSL with Let's Encrypt
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### File Permissions
```bash
chown -R www-data:www-data /var/www/clientwebsite
chmod -R 755 /var/www/clientwebsite
chmod 600 /var/www/clientwebsite/.env
```

---

## Security Checklist for Production

- [ ] Change `SECRET_KEY` to a random 32+ character string
- [ ] Change default admin email/password
- [ ] Set `FLASK_ENV=production` and `FLASK_DEBUG=0`
- [ ] Enable SSL (Let's Encrypt via certbot)
- [ ] Set `RAZORPAY_KEY_ID` to live keys (`rzp_live_...`)
- [ ] Configure `MAIL_*` variables for real email sending
- [ ] Restrict `/admin` to VPN or specific IPs via Nginx if needed
- [ ] Regular SQLite backups: `sqlite3 instance/site.db .dump > backup.sql`

---

## Next Steps / Enhancements

- Add Google Maps embed in contact page
- Connect Google Analytics / Meta Pixel
- Add CAPTCHA (reCAPTCHA v3) to contact form
- Implement password reset via email token
- Add image upload for blog posts (Pillow + local storage)
- Multi-language support (Flask-Babel)
- CDN integration for static files (CloudFlare)
- Redis session caching for production scale
