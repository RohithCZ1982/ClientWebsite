/* TechCorp Corporate Theme — main JavaScript */
(function () {
  'use strict';

  /* ── Navbar scroll effect ── */
  const nav = document.getElementById('mainNav');
  if (nav) {
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 50);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ── Back-to-top ── */
  const btn = document.getElementById('backToTop');
  if (btn) {
    window.addEventListener('scroll', () => btn.classList.toggle('show', window.scrollY > 400), { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ── Auto-dismiss alerts ── */
  document.querySelectorAll('.alert-dismissible').forEach(el => {
    setTimeout(() => {
      const a = bootstrap.Alert.getOrCreateInstance(el);
      if (a) a.close();
    }, 6000);
  });

  /* ── IntersectionObserver fade-in ── */
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.fade-in').forEach(el => io.observe(el));
  } else {
    document.querySelectorAll('.fade-in').forEach(el => el.classList.add('visible'));
  }

  /* ── Active nav link ── */
  const current = window.location.pathname;
  document.querySelectorAll('#mainNav .nav-link').forEach(link => {
    try {
      const lp = new URL(link.href).pathname;
      if (lp !== '/' && current.startsWith(lp)) link.classList.add('active');
      else if (lp === '/' && current === '/') link.classList.add('active');
    } catch (_) {}
  });

  /* ── Smooth scroll for anchor links ── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });

  /* ── Footer newsletter ── */
  const footerBtn = document.getElementById('footerNewsletterBtn');
  if (footerBtn) {
    footerBtn.addEventListener('click', async () => {
      const input = document.getElementById('footerNewsletterEmail');
      const msg   = document.getElementById('footerNewsletterMsg');
      if (!input || !msg) return;
      const email = input.value.trim();
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMsg(msg, 'Please enter a valid email address.', false);
        return;
      }
      footerBtn.disabled = true;
      footerBtn.textContent = '...';
      try {
        const fd = new FormData();
        fd.append('action', 'tc_newsletter');
        fd.append('nonce', TechCorpAjax.nonce);
        fd.append('email', email);
        const res  = await fetch(TechCorpAjax.ajax_url, { method: 'POST', body: fd });
        const data = await res.json();
        showMsg(msg, data.data?.message || (data.success ? 'Subscribed!' : 'Error. Try again.'), data.success);
        if (data.success) input.value = '';
      } catch (_) {
        showMsg(msg, 'Network error. Please try again.', false);
      }
      footerBtn.disabled = false;
      footerBtn.textContent = 'Subscribe';
    });
  }

  function showMsg(el, text, ok) {
    el.textContent = text;
    el.className = 'mt-2 small ' + (ok ? 'text-success' : 'text-danger');
    el.classList.remove('d-none');
  }

  /* ── Contact form ── */
  window.tcSubmitContact = async function (btn) {
    const msg = document.getElementById('contactFormMsg');
    if (!btn) btn = document.querySelector('[onclick*="tcSubmitContact"]');
    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    const name    = document.getElementById('cName')?.value.trim();
    const email   = document.getElementById('cEmail')?.value.trim();
    const message = document.getElementById('cMessage')?.value.trim();

    if (!name || !email || !message) {
      setFormMsg(msg, 'Please fill in all required fields.', false);
      reset(btn, origHTML); return;
    }

    const fd = new FormData();
    fd.append('action',  'tc_contact');
    fd.append('nonce',   TechCorpAjax.nonce);
    fd.append('name',    name);
    fd.append('email',   email);
    fd.append('phone',   document.getElementById('cPhone')?.value || '');
    fd.append('company', document.getElementById('cCompany')?.value || '');
    fd.append('subject', document.getElementById('cSubject')?.value || '');
    fd.append('message', message);

    try {
      const res  = await fetch(TechCorpAjax.ajax_url, { method: 'POST', body: fd });
      const data = await res.json();
      setFormMsg(msg, data.data?.message || (data.success ? 'Message sent!' : 'Error. Please try again.'), data.success);
      if (data.success) ['cName','cEmail','cPhone','cCompany','cMessage'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    } catch (_) {
      setFormMsg(msg, 'Network error. Please try again.', false);
    }
    reset(btn, origHTML);
  };

  function setFormMsg(el, text, ok) {
    if (!el) return;
    el.textContent = text;
    el.className = 'mb-3 alert alert-' + (ok ? 'success' : 'danger');
    el.classList.remove('d-none');
  }
  function reset(btn, html) { btn.disabled = false; btn.innerHTML = html; }

  /* ── Job application ── */
  window.tcSubmitApplication = async function () {
    const btn = document.getElementById('applySubmitBtn');
    const msg = document.getElementById('applyMsg');
    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    const fd = new FormData();
    fd.append('action',  'tc_apply');
    fd.append('nonce',   TechCorpAjax.nonce);
    fd.append('job_id',  document.getElementById('applyJobId')?.value || '');
    fd.append('job_title', document.getElementById('applyJobTitle')?.value || '');
    fd.append('name',    document.getElementById('aName')?.value || '');
    fd.append('email',   document.getElementById('aEmail')?.value || '');
    fd.append('phone',   document.getElementById('aPhone')?.value || '');
    fd.append('cover',   document.getElementById('aCover')?.value || '');

    try {
      const res  = await fetch(TechCorpAjax.ajax_url, { method: 'POST', body: fd });
      const data = await res.json();
      if (msg) {
        msg.textContent = data.data?.message || (data.success ? 'Application submitted!' : 'Error. Try again.');
        msg.className   = 'alert alert-' + (data.success ? 'success' : 'danger');
        msg.classList.remove('d-none');
      }
      if (data.success) {
        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
          if (modal) modal.hide();
        }, 2000);
      }
    } catch (_) {
      if (msg) { msg.textContent = 'Network error.'; msg.className = 'alert alert-danger'; msg.classList.remove('d-none'); }
    }
    btn.disabled = false; btn.innerHTML = origHTML;
  };

  /* ── Razorpay checkout ── */
  window.tcRazorpayCheckout = async function (planIndex) {
    if (typeof TechCorpAjax === 'undefined' || !TechCorpAjax.razorpay_key) {
      alert('Payment gateway is not configured. Please contact us directly.');
      return;
    }

    const btn = document.querySelector('[data-plan="' + planIndex + '"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Processing...'; }

    const fd = new FormData();
    fd.append('action',     'tc_razorpay_order');
    fd.append('nonce',      TechCorpAjax.nonce);
    fd.append('plan_index', planIndex);

    try {
      const res  = await fetch(TechCorpAjax.ajax_url, { method: 'POST', body: fd });
      const data = await res.json();
      if (!data.success) { alert(data.data?.message || 'Could not create order.'); if (btn) { btn.disabled = false; btn.textContent = 'Get Started'; } return; }

      const options = {
        key:         TechCorpAjax.razorpay_key,
        amount:      data.data.amount,
        currency:    data.data.currency,
        name:        data.data.company_name,
        description: data.data.plan_name,
        order_id:    data.data.order_id,
        handler: async function (response) {
          const vfd = new FormData();
          vfd.append('action',                   'tc_razorpay_verify');
          vfd.append('nonce',                    TechCorpAjax.nonce);
          vfd.append('razorpay_order_id',        response.razorpay_order_id);
          vfd.append('razorpay_payment_id',      response.razorpay_payment_id);
          vfd.append('razorpay_signature',       response.razorpay_signature);
          const vres  = await fetch(TechCorpAjax.ajax_url, { method: 'POST', body: vfd });
          const vdata = await vres.json();
          const dest  = vdata.success ? data.data.success_url + '?pid=' + response.razorpay_payment_id : data.data.failed_url;
          window.location.href = dest;
        },
        prefill:  { name: '', email: '' },
        theme:    { color: '#0d6efd' },
        modal:    { ondismiss: function () { if (btn) { btn.disabled = false; btn.textContent = 'Get Started'; } } }
      };
      const rzp = new Razorpay(options);
      rzp.open();
    } catch (err) {
      alert('Something went wrong. Please try again.');
      if (btn) { btn.disabled = false; btn.textContent = 'Get Started'; }
    }
  };

})();
