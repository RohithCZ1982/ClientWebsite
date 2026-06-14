<?php /* Template Name: About Us */ get_header(); ?>
<?php tc_page_header('About <span class="text-primary">TechCorp Solutions</span>', 'Empowering enterprises through transformative technology since 2010.'); ?>

<section class="py-section bg-white">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <span class="section-tag">Our Story</span>
        <h2 class="section-title">Technology that <span class="text-primary">Means Business</span></h2>
        <div class="page-content"><?php the_content(); ?></div>
        <?php if(!get_the_content()): ?>
        <p class="text-muted mb-4">Founded in 2010, TechCorp Solutions began with a simple idea: enterprise technology should be accessible, agile, and impactful. From a small team of five engineers, we've grown into a global technology partner serving 200+ enterprises across 30 countries.</p>
        <p class="text-muted">Today, our 1,200+ professionals combine deep industry expertise with cutting-edge technology across cloud, AI, cybersecurity, and digital transformation.</p>
        <?php endif; ?>
        <div class="row g-3 mt-3">
          <?php foreach([['500+','Projects Delivered'],['30+','Countries Served'],['1,200+','Professionals'],['98%','Satisfaction Rate']] as [$v,$l]): ?>
          <div class="col-6"><div class="stat-box text-center p-3"><div class="stat-value text-primary"><?php echo $v; ?></div><div class="stat-label"><?php echo $l; ?></div></div></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=700&q=80" alt="TechCorp office" class="img-fluid rounded-4 shadow-lg" loading="lazy" />
      </div>
    </div>
  </div>
</section>

<section class="py-section bg-light">
  <div class="container">
    <div class="section-header text-center mb-5"><h2 class="section-title">Our <span class="text-primary">Core</span> Principles</h2></div>
    <div class="row g-4">
      <?php foreach([['bi-bullseye','Mission','primary','To empower enterprises with innovative, reliable, and scalable technology solutions that drive sustainable growth.'],['bi-eye','Vision','success','To be the most trusted technology partner for enterprises worldwide, recognized for innovation and integrity.'],['bi-heart','Values','warning','Client-first mindset · Innovation with purpose · Integrity · Excellence in delivery · Inclusive culture.']] as [$icon,$title,$color,$text]): ?>
      <div class="col-md-4"><div class="card mvv-card h-100 border-0 shadow-sm p-4"><div class="mvv-icon text-<?php echo $color; ?> mb-3"><i class="bi <?php echo $icon; ?>"></i></div><h4 class="fw-700"><?php echo $title; ?></h4><p class="text-muted"><?php echo $text; ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="py-section bg-white">
  <div class="container">
    <div class="section-header text-center mb-5"><span class="section-tag">Our People</span><h2 class="section-title">Meet the <span class="text-primary">Leadership</span></h2></div>
    <div class="row g-4">
      <?php
      $team = get_posts(['post_type'=>'tc_team','numberposts'=>4,'orderby'=>'menu_order','order'=>'ASC']);
      $colors = ['primary','success','warning','info'];
      foreach($team as $i=>$m):
        $title = get_post_meta($m->ID,'_tc_team_title',true);
        $li    = get_post_meta($m->ID,'_tc_team_linkedin',true);
        $tw    = get_post_meta($m->ID,'_tc_team_twitter',true);
        $color = $colors[$i % count($colors)];
        $initials = implode('',array_map(fn($w)=>strtoupper($w[0]),array_slice(explode(' ',$m->post_title),0,2)));
      ?>
      <div class="col-md-6 col-lg-3">
        <div class="card team-card h-100 border-0 shadow-sm text-center p-4">
          <div class="team-avatar bg-<?php echo $color; ?>-subtle text-<?php echo $color; ?> mx-auto mb-3"><?php echo $initials; ?></div>
          <h6 class="fw-700 mb-1"><?php echo get_the_title($m->ID); ?></h6>
          <p class="text-primary small fw-600 mb-2"><?php echo esc_html($title); ?></p>
          <p class="text-muted small"><?php echo get_the_excerpt($m->ID) ?: wp_trim_words(get_the_content(null,false,$m->ID),20); ?></p>
          <div class="d-flex justify-content-center gap-2 mt-2">
            <?php if($li): ?><a href="<?php echo esc_url($li); ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-linkedin"></i></a><?php endif; ?>
            <?php if($tw): ?><a href="<?php echo esc_url($tw); ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4"><a href="<?php echo home_url('/team/'); ?>" class="btn btn-primary">Full Leadership Team</a></div>
  </div>
</section>

<section class="py-5 bg-primary text-white">
  <div class="container">
    <div class="text-center mb-4"><h4 class="fw-700">Certifications & Partnerships</h4></div>
    <div class="d-flex flex-wrap justify-content-center gap-4 align-items-center">
      <?php foreach(['AWS Partner','Microsoft Gold','Google Cloud Partner','ISO 27001','SOC 2 Type II','CMMI Level 3'] as $cert): ?>
      <div class="cert-badge"><i class="bi bi-patch-check-fill me-2"></i><?php echo $cert; ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php get_footer(); ?>
