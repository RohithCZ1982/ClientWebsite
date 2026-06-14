<?php /* Template Name: Team */ get_header(); ?>
<?php tc_page_header('Our <span class="text-primary">Leadership Team</span>', 'Experienced technology leaders committed to your success.'); ?>

<section class="py-section bg-light">
  <div class="container">
    <div class="section-header text-center mb-5">
      <span class="section-tag">Executive Leadership</span>
      <h2 class="section-title">The Minds Behind <span class="text-primary">TechCorp</span></h2>
    </div>
    <div class="row g-4">
      <?php
      $team   = get_posts(['post_type'=>'tc_team','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC']);
      $colors = ['primary','success','warning','info','danger','secondary'];
      foreach($team as $i=>$m):
        $title = get_post_meta($m->ID,'_tc_team_title',true);
        $li    = get_post_meta($m->ID,'_tc_team_linkedin',true);
        $tw    = get_post_meta($m->ID,'_tc_team_twitter',true);
        $email = get_post_meta($m->ID,'_tc_team_email',true);
        $color = $colors[$i % count($colors)];
        $words = explode(' ', $m->post_title);
        $initials = strtoupper( ( $words[0][0] ?? '' ) . ( $words[1][0] ?? '' ) );
        $thumb = has_post_thumbnail($m->ID) ? get_the_post_thumbnail_url($m->ID,'techcorp-team') : null;
      ?>
      <div class="col-md-6 col-lg-3">
        <div class="card team-card-full h-100 border-0 shadow-sm text-center">
          <?php if($thumb): ?>
          <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($m->post_title); ?>" class="card-img-top" style="height:220px;object-fit:cover;object-position:top;" loading="lazy" />
          <?php else: ?>
          <div class="team-avatar-lg bg-<?php echo $color; ?>-subtle text-<?php echo $color; ?> mx-auto mt-4"><?php echo $initials; ?></div>
          <?php endif; ?>
          <div class="card-body p-4">
            <h5 class="fw-700 mb-1"><?php echo get_the_title($m->ID); ?></h5>
            <p class="text-primary fw-600 small mb-3"><?php echo esc_html($title); ?></p>
            <p class="text-muted small"><?php echo get_the_excerpt($m->ID) ?: wp_trim_words(get_the_content(null,false,$m->ID),30); ?></p>
          </div>
          <div class="card-footer bg-transparent border-0 pb-4">
            <div class="d-flex justify-content-center gap-2">
              <?php if($li): ?><a href="<?php echo esc_url($li); ?>" class="btn btn-sm btn-primary" target="_blank"><i class="bi bi-linkedin"></i></a><?php endif; ?>
              <?php if($tw): ?><a href="<?php echo esc_url($tw); ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
              <?php if($email): ?><a href="mailto:<?php echo esc_attr($email); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-envelope"></i></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="py-section bg-white">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="section-tag">Our Culture</span>
        <h2 class="section-title">Where Talent <span class="text-primary">Thrives</span></h2>
        <p class="text-muted mb-4">At TechCorp, great people build great products. We foster continuous learning, collaboration, and innovation.</p>
        <div class="row g-3">
          <?php foreach([['bi-book','Learning & Development','₹3,000/yr education budget'],['bi-house-heart','Flexible Work','Remote-first culture'],['bi-heart-pulse','Health & Wellness','Comprehensive benefits'],['bi-trophy','Recognition','Quarterly awards']] as [$icon,$title,$sub]): ?>
          <div class="col-6"><div class="perk-card p-3 border rounded-3"><i class="bi <?php echo $icon; ?> text-primary fs-4 mb-2 d-block"></i><div class="fw-600 small"><?php echo $title; ?></div><div class="text-muted" style="font-size:.78rem"><?php echo $sub; ?></div></div></div>
          <?php endforeach; ?>
        </div>
        <a href="<?php echo get_post_type_archive_link('tc_job'); ?>" class="btn btn-primary mt-4">View Open Positions</a>
      </div>
      <div class="col-lg-6"><img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=700&q=80" alt="Team culture" class="img-fluid rounded-4 shadow-lg" loading="lazy" /></div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
