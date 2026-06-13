/* TechCorp — Main JavaScript */

document.addEventListener('DOMContentLoaded', function () {

  // ── Back to top button ────────────────────────
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      backToTop.classList.toggle('visible', window.scrollY > 400);
    });
    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ── Navbar scroll effect ──────────────────────
  const nav = document.getElementById('mainNav');
  if (nav) {
    window.addEventListener('scroll', function () {
      nav.style.boxShadow = window.scrollY > 50
        ? '0 4px 24px rgba(0,0,0,.25)'
        : '0 2px 20px rgba(0,0,0,.15)';
    });
  }

  // ── Auto-dismiss flash alerts ─────────────────
  const alerts = document.querySelectorAll('.alert.alert-dismissible');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      if (bsAlert) bsAlert.close();
    }, 6000);
  });

  // ── Intersection Observer — fade in sections ──
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.card, .hero-stat, .stat-box, .cert-badge').forEach(function (el) {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });

  // Add visible class styles
  const style = document.createElement('style');
  style.textContent = '.card.visible, .hero-stat.visible, .stat-box.visible, .cert-badge.visible { opacity: 1 !important; transform: translateY(0) !important; }';
  document.head.appendChild(style);

  // ── Contact form client-side validation ───────
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      const name = contactForm.querySelector('[name=name]').value.trim();
      const email = contactForm.querySelector('[name=email]').value.trim();
      const message = contactForm.querySelector('[name=message]').value.trim();
      if (!name || !email || !message) {
        e.preventDefault();
        showAlert('Please fill in all required fields.', 'danger');
      }
    });
  }

  // ── Newsletter form ───────────────────────────
  document.querySelectorAll('.newsletter-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      const email = form.querySelector('[name=email]').value.trim();
      if (!email || !email.includes('@')) {
        e.preventDefault();
        showAlert('Please enter a valid email address.', 'danger');
      }
    });
  });

  // ── Utility: show alert ───────────────────────
  function showAlert(message, type) {
    const existing = document.querySelector('.js-alert');
    if (existing) existing.remove();
    const div = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show js-alert`;
    div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    const container = document.querySelector('.container') || document.body;
    container.prepend(div);
    setTimeout(function () {
      const a = bootstrap.Alert.getOrCreateInstance(div);
      if (a) a.close();
    }, 5000);
  }

  // ── Smooth scroll anchor links ────────────────
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ── Active nav link highlight ─────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('#mainNav .nav-link').forEach(function (link) {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

});
