<?php get_header(); ?>
<?php tc_page_header('Build the Future <span class="text-primary">with Us</span>', 'Join a team of engineers, designers, and strategists redefining enterprise technology.'); ?>

<?php
$jobs = get_posts(['post_type'=>'tc_job','numberposts'=>-1,'meta_query'=>[['key'=>'_tc_job_active','value'=>'1']],'orderby'=>'menu_order','order'=>'ASC']);

// Build department color map
$dept_colors = ['Engineering'=>'#3b82f6','Security'=>'#f59e0b','Operations'=>'#10b981','General'=>'#8b5cf6'];
$dept_icons  = ['Engineering'=>'bi-cpu','Security'=>'bi-shield-lock','Operations'=>'bi-diagram-3','General'=>'bi-grid'];

// Collect all departments from actual jobs
$all_depts = [];
foreach($jobs as $j) {
  $depts = get_the_terms($j->ID,'job_department');
  $dept  = $depts ? $depts[0]->name : 'General';
  if(!in_array($dept,$all_depts)) $all_depts[] = $dept;
}
?>

<!-- Stats bar -->
<section class="careers-stats py-4 bg-white border-bottom">
  <div class="container">
    <div class="row g-4 justify-content-center text-center">
      <div class="col-6 col-md-3">
        <div class="careers-stat">
          <span class="careers-stat-num"><?php echo count($jobs); ?></span>
          <span class="careers-stat-label">Open Roles</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="careers-stat">
          <span class="careers-stat-num"><?php echo count($all_depts); ?></span>
          <span class="careers-stat-label">Departments</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="careers-stat">
          <span class="careers-stat-num">30+</span>
          <span class="careers-stat-label">Countries</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="careers-stat">
          <span class="careers-stat-num">98%</span>
          <span class="careers-stat-label">Satisfaction</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Jobs section -->
<section class="py-section" style="background: #f8fafc">
  <div class="container">

    <!-- Filter tabs -->
    <div class="job-filter-bar mb-5">
      <button class="job-filter-btn active" data-dept="all">
        <i class="bi bi-grid-3x3-gap me-2"></i>All Positions
        <span class="job-filter-count"><?php echo count($jobs); ?></span>
      </button>
      <?php foreach($all_depts as $d):
        $count = count(array_filter($jobs, function($j) use($d) {
          $depts = get_the_terms($j->ID,'job_department');
          return $depts && $depts[0]->name === $d;
        }));
        $icon = $dept_icons[$d] ?? 'bi-folder';
        $color = $dept_colors[$d] ?? '#6366f1';
      ?>
      <button class="job-filter-btn" data-dept="<?php echo esc_attr($d); ?>"
              style="--dept-color: <?php echo $color; ?>">
        <i class="bi <?php echo $icon; ?> me-2"></i><?php echo esc_html($d); ?>
        <span class="job-filter-count"><?php echo $count; ?></span>
      </button>
      <?php endforeach; ?>
    </div>

    <?php if($jobs): ?>
    <!-- Jobs list -->
    <div class="jobs-list" id="jobsList">
      <?php foreach($jobs as $j):
        $location   = get_post_meta($j->ID,'_tc_job_location',true);
        $type       = get_post_meta($j->ID,'_tc_job_type',true);
        $experience = get_post_meta($j->ID,'_tc_job_experience',true);
        $depts      = get_the_terms($j->ID,'job_department');
        $dept       = $depts ? $depts[0]->name : 'General';
        $color      = $dept_colors[$dept] ?? '#6366f1';
        $icon       = $dept_icons[$dept] ?? 'bi-folder';
        $excerpt    = get_the_excerpt($j->ID);
      ?>
      <div class="job-row" data-dept="<?php echo esc_attr($dept); ?>" style="--dept-color: <?php echo $color; ?>">
        <div class="job-row-accent"></div>
        <div class="job-row-inner">
          <div class="job-row-dept">
            <span class="job-dept-pill" style="background: <?php echo $color; ?>18; color: <?php echo $color; ?>; border-color: <?php echo $color; ?>30">
              <i class="bi <?php echo $icon; ?> me-1"></i><?php echo esc_html($dept); ?>
            </span>
          </div>
          <div class="job-row-body">
            <h4 class="job-row-title"><?php echo get_the_title($j->ID); ?></h4>
            <p class="job-row-excerpt"><?php echo esc_html($excerpt); ?></p>
            <div class="job-row-tags">
              <?php if($type): ?>
              <span class="job-tag job-tag-type"><i class="bi bi-clock me-1"></i><?php echo esc_html($type); ?></span>
              <?php endif; ?>
              <?php if($location): ?>
              <span class="job-tag job-tag-location"><i class="bi bi-geo-alt me-1"></i><?php echo esc_html($location); ?></span>
              <?php endif; ?>
              <?php if($experience): ?>
              <span class="job-tag job-tag-exp"><i class="bi bi-briefcase me-1"></i><?php echo esc_html($experience); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="job-row-cta">
            <button class="btn job-apply-btn" style="--dept-color: <?php echo $color; ?>"
              data-bs-toggle="modal" data-bs-target="#applyModal"
              data-position="<?php echo esc_attr(get_the_title($j->ID)); ?>">
              Apply Now <i class="bi bi-arrow-right ms-2"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5 text-muted">
      <i class="bi bi-briefcase fs-1 d-block mb-3 text-primary opacity-50"></i>
      <h5>No open positions right now.</h5>
      <p>Check back soon or <a href="<?php echo home_url('/contact/'); ?>">send us your resume</a>.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Benefits -->
<section class="py-section bg-white">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">Perks & Benefits</span>
      <h2 class="section-title">Why Work at <span class="text-primary">TechCorp?</span></h2>
    </div>
    <div class="row g-4">
      <?php foreach([
        ['bi-mortarboard','Continuous Learning','Annual ₹3,000 learning budget, certifications fully covered.','#3b82f6'],
        ['bi-cash-stack','Competitive Pay','Market-leading salaries with performance bonuses.','#10b981'],
        ['bi-globe','Global Exposure','Work with clients across 30+ countries worldwide.','#f59e0b'],
        ['bi-heart-pulse','Health & Wellness','Comprehensive medical, dental, and vision benefits.','#ef4444'],
        ['bi-house-heart','Flexible Work','Remote-first culture with flexible hours.','#8b5cf6'],
        ['bi-people','Inclusive Culture','A diverse, welcoming environment where everyone belongs.','#06b6d4'],
      ] as [$icon,$title,$desc,$color]): ?>
      <div class="col-md-4">
        <div class="benefit-card-v2 p-4 h-100" style="--benefit-color: <?php echo $color; ?>">
          <div class="benefit-icon-v2 mb-3" style="background: <?php echo $color; ?>15; color: <?php echo $color; ?>">
            <i class="bi <?php echo $icon; ?>"></i>
          </div>
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
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 pb-0 px-4 pt-4">
        <div>
          <h5 class="modal-title fw-700 fs-4" id="applyModalLabel">Apply for Position</h5>
          <p class="text-muted small mb-0">Fill in the details below and we'll get back to you within 48 hours.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="applyFormMsg" class="mb-3 d-none"></div>
        <div class="row g-3" id="applyFormFields">
          <div class="col-md-6"><label class="form-label fw-600">Full Name *</label><input type="text" id="applyName" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label fw-600">Email *</label><input type="email" id="applyEmail" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="tel" id="applyPhone" class="form-control" /></div>
          <div class="col-md-6"><label class="form-label fw-600">Years of Experience</label>
            <select id="applyExp" class="form-select"><option value="">Select...</option><?php foreach(['0-2','3-5','5-8','8-12','12+'] as $e): ?><option><?php echo $e; ?></option><?php endforeach; ?></select>
          </div>
          <div class="col-12"><label class="form-label fw-600">Cover Letter</label><textarea id="applyCover" class="form-control" rows="4" placeholder="Tell us about yourself and why you're a great fit..."></textarea></div>
          <div class="col-12"><button class="btn btn-primary px-5 py-2" id="applySubmitBtn" onclick="tcSubmitApplication()"><i class="bi bi-send me-2"></i>Submit Application</button></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Filter logic
document.querySelectorAll('.job-filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.job-filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const dept = this.dataset.dept;
    document.querySelectorAll('.job-row').forEach(row => {
      const show = dept === 'all' || row.dataset.dept === dept;
      row.style.display = show ? '' : 'none';
      if(show) { row.style.animation = 'none'; row.offsetHeight; row.style.animation = 'jobFadeIn .3s ease'; }
    });
  });
});

// Modal title
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
  body.append('action','tc_apply'); body.append('nonce',TechCorpAjax.nonce);
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
  msg.classList.remove('d-none');
  if(data.success) document.getElementById('applyFormFields').querySelectorAll('input,textarea,select').forEach(el=>el.value='');
  btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-2"></i>Submit Application';
}
</script>

<?php get_footer(); ?>
