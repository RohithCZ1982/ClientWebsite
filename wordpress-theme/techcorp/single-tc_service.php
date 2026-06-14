<?php get_header(); the_post();
$icon = get_post_meta(get_the_ID(),'_tc_service_icon',true) ?: 'bi-gear';
?>
<section class="page-header">
  <div class="container">
    <div class="d-flex justify-content-center mb-3"><div class="service-header-icon"><i class="bi <?php echo esc_attr($icon); ?>"></i></div></div>
    <h1><?php the_title(); ?></h1>
    <p class="lead text-white-75"><?php the_excerpt(); ?></p>
    <?php tc_breadcrumbs(); ?>
  </div>
</section>

<section class="py-section bg-white">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <h3 class="fw-700 mb-4">What We Deliver</h3>
        <div class="service-content"><?php the_content(); ?></div>

        <h5 class="fw-700 mt-5 mb-3">Key Capabilities</h5>
        <div class="row g-3">
          <?php foreach(['Strategic consulting & roadmap development','Architecture design & review','Implementation & integration','Migration & modernization','Training & knowledge transfer','Ongoing managed services & support'] as $cap): ?>
          <div class="col-md-6"><div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><span><?php echo $cap; ?></span></div></div>
          <?php endforeach; ?>
        </div>

        <h5 class="fw-700 mt-5 mb-3">Business Outcomes</h5>
        <div class="row g-3">
          <?php foreach([['bi-speedometer2','Faster time-to-market'],['bi-piggy-bank','Reduced operational costs'],['bi-shield-check','Enhanced security posture'],['bi-graph-up','Improved scalability']] as [$oi,$ot]): ?>
          <div class="col-md-6"><div class="outcome-card p-3 border rounded-3 d-flex gap-3 align-items-center"><i class="bi <?php echo $oi; ?> fs-4 text-primary"></i><span class="fw-600"><?php echo $ot; ?></span></div></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card border-0 bg-light p-4 mb-4">
          <h6 class="fw-700 mb-3">Other Services</h6>
          <ul class="list-unstyled mb-0">
            <?php
            $others = get_posts(['post_type'=>'tc_service','numberposts'=>-1,'post__not_in'=>[get_the_ID()],'orderby'=>'menu_order','order'=>'ASC']);
            foreach($others as $o):
              $oi = get_post_meta($o->ID,'_tc_service_icon',true) ?: 'bi-gear';
            ?>
            <li class="mb-2"><a href="<?php echo get_permalink($o->ID); ?>" class="text-decoration-none text-dark d-flex align-items-center gap-2"><i class="bi <?php echo esc_attr($oi); ?> text-primary"></i><?php echo get_the_title($o->ID); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="card border-0 bg-primary text-white p-4">
          <h6 class="fw-700 mb-2">Ready to get started?</h6>
          <p class="small text-white-75 mb-3">Our experts are ready to discuss your requirements.</p>
          <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-light btn-sm fw-600">Get Free Consultation</a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
