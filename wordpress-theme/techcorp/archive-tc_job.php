<?php get_header(); ?>
<?php tc_page_header('Careers at <span class="text-primary">TechCorp</span>', 'Join a team building the future of enterprise technology.'); ?>

<section class="py-section bg-light">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">Join Our Team</span>
      <h2 class="section-title">Open <span class="text-primary">Positions</span></h2>
    </div>
    <div class="row g-4">
      <?php
      $jobs = get_posts(['post_type'=>'tc_job','numberposts'=>-1,'meta_query'=>[['key'=>'_tc_job_active','value'=>'1']]]);
      foreach($jobs as $j):
        $location   = get_post_meta($j->ID,'_tc_job_location',true);
        $type       = get_post_meta($j->ID,'_tc_job_type',true);
        $experience = get_post_meta($j->ID,'_tc_job_experience',true);
        $depts = get_the_terms($j->ID,'job_department');
        $dept  = $depts ? $depts[0]->name : 'General';
      ?>
      <div class="col-md-6">
        <div class="card job-card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div><h5 class="fw-700 mb-1"><?php echo get_the_title($j->ID); ?></h5><p class="text-muted small mb-0"><i class="bi bi-building me-1"></i><?php echo esc_html($dept); ?></p></div>
              <?php if($type): ?><span class="badge bg-primary-subtle text-primary"><?php echo esc_html($type); ?></span><?php endif; ?>
            </div>
            <p class="text-muted small mb-3"><?php echo get_the_excerpt($j->ID); ?></p>
            <div class="d-flex align-items-center gap-3 mb-3 text-muted small">
              <?php if($location): ?><span><i class="bi bi-geo-alt me-1"></i><?php echo esc_html($location); ?></span><?php endif; ?>
              <?php if($experience): ?><span><i class="bi bi-briefcase me-1"></i><?php echo esc_html($experience); ?></span><?php endif; ?>
            </div>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#applyModal" data-position="<?php echo esc_attr(get_the_title($j->ID)); ?>">
              Apply Now <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(!$jobs): ?>
      <div class="col-12 text-center py-5 text-muted"><i class="bi bi-briefcase fs-1 d-block mb-3"></i><h5>No open positions right now.</h5><p>Check back soon or <a href="<?php echo home_url('/contact/'); ?>">send us your resume</a>.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Benefits -->
<section class="py-section bg-white">
  <div class="container">
    <div class="section-header text-center mb-5"><h2 class="section-title">Why Work at <span class="text-primary">TechCorp?</span></h2></div>
    <div class="row g-4">
      <?php foreach([['bi-mortarboard','Continuous Learning','Annual ₹3,000 learning budget, certifications covered.'],['bi-cash-stack','Competitive Pay','Market-leading salaries, performance bonuses.'],['bi-globe','Global Exposure','Work with clients across 30+ countries.'],['bi-heart-pulse','Health & Wellness','Comprehensive medical, dental, and vision benefits.'],['bi-house-heart','Flexible Work','Remote-first culture with flexible hours.'],['bi-people','Inclusive Culture','Diverse, welcoming environment.']] as [$icon,$title,$desc]): ?>
      <div class="col-md-4">
        <div class="benefit-card text-center p-4 h-100">
          <div class="benefit-icon mx-auto mb-3"><i class="bi <?php echo $icon; ?>"></i></div>
          <h6 class="fw-700 mb-2"><?php echo $title; ?></h6>
          <p class="text-muted small mb-0"><?php echo $desc; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0"><h5 class="modal-title fw-700" id="applyModalLabel">Apply for Position</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-4">
        <div id="applyFormMsg" class="mb-3 d-none"></div>
        <div class="row g-3" id="applyFormFields">
          <div class="col-md-6"><label class="form-label fw-600">Full Name *</label><input type="text" id="applyName" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label fw-600">Email *</label><input type="email" id="applyEmail" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="tel" id="applyPhone" class="form-control" /></div>
          <div class="col-md-6"><label class="form-label fw-600">Years of Experience</label><select id="applyExp" class="form-select"><option value="">Select...</option><?php foreach(['0-2','3-5','5-8','8-12','12+'] as $e): ?><option><?php echo $e; ?></option><?php endforeach; ?></select></div>
          <div class="col-12"><label class="form-label fw-600">Cover Letter</label><textarea id="applyCover" class="form-control" rows="4" placeholder="Tell us about yourself..."></textarea></div>
          <div class="col-12"><button class="btn btn-primary px-5" id="applySubmitBtn" onclick="tcSubmitApplication()"><i class="bi bi-send me-2"></i>Submit Application</button></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let applyPosition = '';
document.getElementById('applyModal').addEventListener('show.bs.modal', function(e) {
  applyPosition = e.relatedTarget?.dataset?.position || '';
  document.getElementById('applyModalLabel').textContent = applyPosition ? 'Apply: ' + applyPosition : 'Submit Application';
});
async function tcSubmitApplication() {
  const btn = document.getElementById('applySubmitBtn');
  const msg = document.getElementById('applyFormMsg');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
  const body = new FormData();
  body.append('action', 'tc_apply');
  body.append('nonce', TechCorpAjax.nonce);
  body.append('name',     document.getElementById('applyName').value);
  body.append('email',    document.getElementById('applyEmail').value);
  body.append('phone',    document.getElementById('applyPhone').value);
  body.append('experience', document.getElementById('applyExp').value);
  body.append('cover',    document.getElementById('applyCover').value);
  body.append('position', applyPosition);
  const res  = await fetch(TechCorpAjax.ajax_url, {method:'POST',body});
  const data = await res.json();
  msg.className = 'mb-3 alert alert-' + (data.success ? 'success' : 'danger');
  msg.textContent = data.data?.message || (data.success ? 'Submitted!' : 'Error. Try again.');
  if(data.success) document.getElementById('applyFormFields').querySelectorAll('input,textarea,select').forEach(el=>el.value='');
  btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-2"></i>Submit Application';
}
</script>

<?php get_footer(); ?>
