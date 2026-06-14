<?php get_header(); ?>

<!-- ═══ HERO ════════════════════════════════════════════════════ -->
<section class="hero-section">
  <div class="hero-bg"></div>
  <div class="container position-relative">
    <div class="row align-items-center min-vh-hero">
      <div class="col-lg-6">
        <span class="badge badge-pill-outline mb-3"><i class="bi bi-stars me-1"></i>Trusted by 200+ Enterprises</span>
        <h1 class="hero-title">Transforming Business Through <span class="text-gradient">Technology</span></h1>
        <p class="hero-sub"><?php echo esc_html( tc_opt('company_tagline','End-to-end digital solutions — cloud, AI, cybersecurity, and beyond.') ); ?> We help enterprises modernize, scale, and lead in the digital era.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-primary btn-lg px-4"><i class="bi bi-chat-dots me-2"></i>Get Free Consultation</a>
          <a href="<?php echo get_post_type_archive_link('tc_service'); ?>" class="btn btn-outline-light btn-lg px-4">Explore Services <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="d-flex flex-wrap gap-4 mt-5">
          <?php foreach([['500+','Projects Delivered'],['200+','Enterprise Clients'],['15+','Years Experience'],['98%','Client Satisfaction']] as [$v,$l]): ?>
          <div class="hero-stat"><div class="hero-stat-value"><?php echo $v; ?></div><div class="hero-stat-label"><?php echo $l; ?></div></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-flex justify-content-center">
        <div class="hero-graphic">
          <div class="hero-card floating"><i class="bi bi-cloud-check-fill text-primary fs-2"></i><div><div class="fw-600">Cloud Migration</div><div class="text-success small"><i class="bi bi-arrow-up-short"></i>40% performance boost</div></div></div>
          <div class="hero-card floating delay-1"><i class="bi bi-shield-check-fill text-success fs-2"></i><div><div class="fw-600">Security Score</div><div class="text-success small">98/100 ✓</div></div></div>
          <div class="hero-card floating delay-2"><i class="bi bi-graph-up-arrow text-warning fs-2"></i><div><div class="fw-600">AI Analytics</div><div class="text-info small">Live dashboard active</div></div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="hero-wave"><svg viewBox="0 0 1440 80" preserveAspectRatio="none"><path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="var(--bs-body-bg)"/></svg></div>
</section>

<!-- Trusted By -->
<section class="py-4 bg-white border-bottom">
  <div class="container">
    <p class="text-center text-muted small fw-500 mb-3">TRUSTED BY INDUSTRY LEADERS</p>
    <div class="d-flex flex-wrap justify-content-center align-items-center gap-5 opacity-50">
      <?php foreach(['RetailMax','FinTech Partners','HealthBridge','ManuCore','LogiSmart','EduVista'] as $c): ?>
      <span class="fw-700 fs-5 text-dark"><?php echo $c; ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ SERVICES ════════════════════════════════════════════════ -->
<section class="py-section bg-light">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">What We Do</span>
      <h2 class="section-title">Our <span class="text-primary">Services</span></h2>
      <p class="section-sub">Comprehensive technology solutions tailored to your business goals.</p>
    </div>
    <div class="row g-4">
      <?php
      $services = get_posts(['post_type'=>'tc_service','numberposts'=>6,'orderby'=>'menu_order','order'=>'ASC']);
      foreach($services as $s):
        $icon = get_post_meta($s->ID,'_tc_service_icon',true) ?: 'bi-gear';
      ?>
      <div class="col-md-6 col-lg-4">
        <a href="<?php echo get_permalink($s->ID); ?>" class="card service-card h-100 text-decoration-none">
          <div class="card-body p-4">
            <div class="service-icon mb-3"><i class="bi <?php echo esc_attr($icon); ?>"></i></div>
            <h5 class="fw-700 text-dark"><?php echo get_the_title($s->ID); ?></h5>
            <p class="text-muted mb-3"><?php echo get_the_excerpt($s->ID); ?></p>
            <span class="text-primary fw-600 small">Learn more <i class="bi bi-arrow-right"></i></span>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?php echo get_post_type_archive_link('tc_service'); ?>" class="btn btn-primary btn-lg px-5">View All Services</a>
    </div>
  </div>
</section>

<!-- ═══ WHY CHOOSE US ════════════════════════════════════════════ -->
<section class="py-section bg-white">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="section-tag">Why TechCorp</span>
        <h2 class="section-title">Built for <span class="text-primary">Enterprise</span>, Designed for <span class="text-primary">Results</span></h2>
        <p class="text-muted mb-4">Deep domain expertise combined with cutting-edge technology to deliver measurable business impact.</p>
        <?php foreach([
          ['bi-award','Certified Experts','300+ active certifications across AWS, Azure, GCP, CISSP, and PMP.'],
          ['bi-clock-history','Proven Delivery','500+ projects delivered on time with 98% client satisfaction.'],
          ['bi-headset','24/7 Support','Round-the-clock dedicated support with guaranteed SLA response times.'],
          ['bi-globe','Global Reach','Serving clients across 30+ countries from our delivery centers.'],
        ] as [$icon,$title,$desc]): ?>
        <div class="d-flex gap-3 align-items-start mb-3">
          <div class="why-icon flex-shrink-0"><i class="bi <?php echo $icon; ?>"></i></div>
          <div><h6 class="fw-700 mb-1"><?php echo $title; ?></h6><p class="text-muted small mb-0"><?php echo $desc; ?></p></div>
        </div>
        <?php endforeach; ?>
        <a href="<?php echo home_url('/about/'); ?>" class="btn btn-primary mt-4">Learn About Us</a>
      </div>
      <div class="col-lg-6">
        <div class="why-visual position-relative">
          <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=700&q=80" alt="Team collaboration" class="img-fluid rounded-4 shadow-lg" loading="lazy" />
          <div class="floating-badge top-badge shadow"><i class="bi bi-trophy-fill text-warning me-2"></i><span class="fw-600">ISO 27001 Certified</span></div>
          <div class="floating-badge bottom-badge shadow"><i class="bi bi-people-fill text-primary me-2"></i><span class="fw-600">1,200+ Professionals</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ════════════════════════════════════════════ -->
<section class="py-section bg-primary-subtle">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">Client Stories</span>
      <h2 class="section-title">What Our <span class="text-primary">Clients Say</span></h2>
    </div>
    <div class="row g-4">
      <?php
      $testimonials = get_posts(['post_type'=>'tc_testimonial','numberposts'=>3]);
      foreach($testimonials as $t):
        $company = get_post_meta($t->ID,'_tc_testimonial_company',true);
        $rating  = get_post_meta($t->ID,'_tc_testimonial_rating',true) ?: 5;
      ?>
      <div class="col-md-4">
        <div class="testimonial-card h-100">
          <div class="mb-3"><?php echo tc_stars($rating); ?></div>
          <p class="testimonial-text">"<?php echo get_the_content(null,false,$t->ID); ?>"</p>
          <div class="d-flex align-items-center gap-3 mt-4">
            <div class="avatar-circle"><?php echo strtoupper(substr($t->post_title,0,2)); ?></div>
            <div><div class="fw-700"><?php echo get_the_title($t->ID); ?></div><div class="text-muted small"><?php echo esc_html($company); ?></div></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ LATEST NEWS ══════════════════════════════════════════════ -->
<?php
$latest_posts = get_posts(['numberposts'=>3,'post_status'=>'publish']);
if($latest_posts):
?>
<section class="py-section bg-white">
  <div class="container">
    <div class="section-header d-flex align-items-end justify-content-between mb-5">
      <div><span class="section-tag">Latest Updates</span><h2 class="section-title mb-0">News & <span class="text-primary">Insights</span></h2></div>
      <a href="<?php echo get_permalink(get_option('page_for_posts')) ?: home_url('/news/'); ?>" class="btn btn-outline-primary">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
      <?php foreach($latest_posts as $p): $thumb = get_the_post_thumbnail_url($p->ID,'techcorp-card'); ?>
      <div class="col-md-4">
        <a href="<?php echo get_permalink($p->ID); ?>" class="card blog-card h-100 text-decoration-none">
          <?php if($thumb): ?><img src="<?php echo esc_url($thumb); ?>" class="card-img-top" alt="<?php echo get_the_title($p->ID); ?>" loading="lazy" style="height:200px;object-fit:cover;"><?php endif; ?>
          <div class="card-body p-4">
            <?php $cats = get_the_category($p->ID); if($cats): ?><span class="badge bg-primary-subtle text-primary mb-2"><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?>
            <h5 class="card-title fw-700 text-dark"><?php echo get_the_title($p->ID); ?></h5>
            <p class="card-text text-muted small"><?php echo get_the_excerpt($p->ID); ?></p>
          </div>
          <div class="card-footer bg-transparent border-0 px-4 pb-4">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo get_the_date('M d, Y',$p->ID); ?></small>
              <small class="text-primary fw-600">Read more <i class="bi bi-arrow-right"></i></small>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══ CTA ══════════════════════════════════════════════════════ -->
<section class="cta-section py-section">
  <div class="container text-center">
    <h2 class="display-5 fw-800 text-white mb-3">Ready to Transform Your Business?</h2>
    <p class="lead text-white-75 mb-5">Schedule a free 30-minute consultation with our experts today.</p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-light btn-lg px-5 fw-600"><i class="bi bi-calendar-check me-2"></i>Book a Consultation</a>
      <a href="<?php echo home_url('/pricing/'); ?>" class="btn btn-outline-light btn-lg px-5">View Pricing</a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
