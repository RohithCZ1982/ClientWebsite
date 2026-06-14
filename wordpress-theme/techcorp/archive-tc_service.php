<?php get_header(); ?>
<?php tc_page_header('Our <span class="text-primary">Services</span>', 'End-to-end technology solutions for the modern enterprise.'); ?>

<section class="py-section bg-light">
  <div class="container">
    <div class="row g-4">
      <?php
      $services = get_posts(['post_type'=>'tc_service','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC']);
      foreach($services as $s):
        $icon = get_post_meta($s->ID,'_tc_service_icon',true) ?: 'bi-gear';
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="card service-card-full h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="service-icon-lg mb-4"><i class="bi <?php echo esc_attr($icon); ?>"></i></div>
            <h4 class="fw-700 mb-3"><?php echo get_the_title($s->ID); ?></h4>
            <p class="text-muted mb-4"><?php echo get_the_excerpt($s->ID); ?></p>
            <a href="<?php echo get_permalink($s->ID); ?>" class="btn btn-primary">Learn More <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Process -->
<section class="py-section bg-white">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">How We Work</span>
      <h2 class="section-title">Our <span class="text-primary">Delivery Process</span></h2>
    </div>
    <div class="row g-4">
      <?php foreach([['bi-search','Discover','Deep-dive workshops to understand your goals.'],['bi-diagram-3','Design','Architecture aligned with your needs and budget.'],['bi-code-slash','Build','Agile development with sprint reviews.'],['bi-rocket-takeoff','Launch','Phased rollout and smooth go-live.'],['bi-headset','Support','Ongoing managed services and improvement.']] as [$icon,$title,$desc]): ?>
      <div class="col-md-4 col-lg">
        <div class="process-step text-center">
          <div class="process-icon mx-auto mb-3"><i class="bi <?php echo $icon; ?>"></i></div>
          <h6 class="fw-700"><?php echo $title; ?></h6>
          <p class="text-muted small"><?php echo $desc; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section py-section">
  <div class="container text-center">
    <h2 class="display-6 fw-800 text-white mb-3">Not sure which service is right for you?</h2>
    <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-light btn-lg px-5 fw-600">Contact Us</a>
  </div>
</section>
<?php get_footer(); ?>
