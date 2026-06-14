<?php /* Template Name: Contact */ get_header(); ?>
<?php tc_page_header('Get in <span class="text-primary">Touch</span>', "We'd love to hear from you. Let's start a conversation."); ?>

<section class="py-section bg-light">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <h4 class="fw-700 mb-4">Contact Information</h4>
        <div class="d-flex flex-column gap-4">
          <?php if($e = tc_opt('company_email')): ?>
          <div class="contact-info-item d-flex gap-3 align-items-start"><div class="contact-icon"><i class="bi bi-envelope-fill"></i></div><div><div class="fw-600">Email</div><a href="mailto:<?php echo esc_attr($e); ?>" class="text-muted text-decoration-none"><?php echo esc_html($e); ?></a></div></div>
          <?php endif; ?>
          <?php if($p = tc_opt('company_phone')): ?>
          <div class="contact-info-item d-flex gap-3 align-items-start"><div class="contact-icon"><i class="bi bi-telephone-fill"></i></div><div><div class="fw-600">Phone</div><a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/','',$p)); ?>" class="text-muted text-decoration-none"><?php echo esc_html($p); ?></a></div></div>
          <?php endif; ?>
          <?php if($a = tc_opt('company_address')): ?>
          <div class="contact-info-item d-flex gap-3 align-items-start"><div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div><div><div class="fw-600">Address</div><p class="text-muted mb-0"><?php echo esc_html($a); ?></p></div></div>
          <?php endif; ?>
          <div class="contact-info-item d-flex gap-3 align-items-start"><div class="contact-icon"><i class="bi bi-clock-fill"></i></div><div><div class="fw-600">Business Hours</div><p class="text-muted mb-0">Mon–Fri: 9 AM – 6 PM IST</p><p class="text-muted mb-0">Support: 24/7</p></div></div>
        </div>
        <div class="mt-4">
          <h6 class="fw-700 mb-3">Follow Us</h6>
          <div class="d-flex gap-2">
            <?php if($l = tc_opt('company_linkedin')): ?><a href="<?php echo esc_url($l); ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-linkedin"></i></a><?php endif; ?>
            <?php if($t = tc_opt('company_twitter')): ?><a href="<?php echo esc_url($t); ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
            <?php if($f = tc_opt('company_facebook')): ?><a href="<?php echo esc_url($f); ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-facebook"></i></a><?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 p-lg-5">
          <h4 class="fw-700 mb-1">Send Us a Message</h4>
          <p class="text-muted mb-4">Fill in the form and we'll get back to you within 24 hours.</p>
          <div id="contactFormMsg" class="mb-3 d-none"></div>
          <div class="row g-3" id="contactFormFields">
            <div class="col-md-6"><label class="form-label fw-600">Full Name *</label><input type="text" id="cName" class="form-control" placeholder="John Doe" /></div>
            <div class="col-md-6"><label class="form-label fw-600">Email Address *</label><input type="email" id="cEmail" class="form-control" placeholder="john@example.com" /></div>
            <div class="col-md-6"><label class="form-label fw-600">Phone Number</label><input type="tel" id="cPhone" class="form-control" placeholder="+91 98765 43210" /></div>
            <div class="col-md-6"><label class="form-label fw-600">Company</label><input type="text" id="cCompany" class="form-control" placeholder="Your Company" /></div>
            <div class="col-12"><label class="form-label fw-600">Subject</label>
              <select id="cSubject" class="form-select">
                <option value="">Select a topic...</option>
                <?php foreach(['General Inquiry','Service Information','Partnership','Technical Support','Pricing & Quotes','Other'] as $s): ?><option><?php echo $s; ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12"><label class="form-label fw-600">Message *</label><textarea id="cMessage" class="form-control" rows="5" placeholder="Tell us about your project..."></textarea></div>
            <div class="col-12"><button type="button" class="btn btn-primary btn-lg px-5" onclick="tcSubmitContact()"><i class="bi bi-send me-2"></i>Send Message</button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if($maps_key = get_option('techcorp_google_maps_key')): ?>
<iframe width="100%" height="300" frameborder="0" style="border:0" loading="lazy" allowfullscreen
  src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr($maps_key); ?>&q=<?php echo urlencode(tc_opt('company_address','Mysore,Karnataka,India')); ?>">
</iframe>
<?php else: ?>
<div class="bg-light border-top" style="height:250px;display:flex;align-items:center;justify-content:center;">
  <div class="text-center text-muted"><i class="bi bi-map fs-1 mb-2 d-block"></i><p class="mb-0">Add your Google Maps API key in TechCorp Settings to display the map here.</p></div>
</div>
<?php endif; ?>

<script>
async function tcSubmitContact() {
  const btn = event.target;
  const msg = document.getElementById('contactFormMsg');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
  const body = new FormData();
  body.append('action', 'tc_contact');
  body.append('nonce',   TechCorpAjax.nonce);
  body.append('name',    document.getElementById('cName').value);
  body.append('email',   document.getElementById('cEmail').value);
  body.append('phone',   document.getElementById('cPhone').value);
  body.append('company', document.getElementById('cCompany').value);
  body.append('subject', document.getElementById('cSubject').value);
  body.append('message', document.getElementById('cMessage').value);
  const res  = await fetch(TechCorpAjax.ajax_url, {method:'POST',body});
  const data = await res.json();
  msg.className = 'mb-3 alert alert-' + (data.success ? 'success' : 'danger');
  msg.textContent = data.data?.message || (data.success ? 'Message sent!' : 'Error. Please try again.');
  if(data.success) ['cName','cEmail','cPhone','cCompany','cMessage'].forEach(id => document.getElementById(id).value = '');
  btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-2"></i>Send Message';
}
</script>
<?php get_footer(); ?>
