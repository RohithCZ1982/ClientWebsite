<?php get_header(); the_post(); ?>
<section class="page-header">
  <div class="container">
    <?php $cats = get_the_category(); if($cats): ?><span class="badge bg-primary-subtle text-primary mb-3"><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?>
    <h1 class="fs-2 fw-800"><?php the_title(); ?></h1>
    <div class="d-flex flex-wrap justify-content-center gap-3 text-white-75 mt-3 small">
      <span><i class="bi bi-person me-1"></i><?php the_author(); ?></span>
      <span><i class="bi bi-calendar3 me-1"></i><?php the_date('F d, Y'); ?></span>
      <span><i class="bi bi-clock me-1"></i><?php echo ceil(str_word_count(strip_tags(get_the_content())) / 200); ?> min read</span>
    </div>
    <?php tc_breadcrumbs(); ?>
  </div>
</section>

<section class="py-section bg-white">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if(has_post_thumbnail()): ?><img src="<?php the_post_thumbnail_url('techcorp-hero'); ?>" alt="<?php the_title_attribute(); ?>" class="img-fluid rounded-4 mb-4 w-100" loading="lazy" style="max-height:420px;object-fit:cover;"><?php endif; ?>
        <article class="blog-content"><?php the_content(); ?></article>

        <?php $tags = get_the_tags(); if($tags): ?>
        <div class="mt-4 pt-4 border-top"><strong>Tags:</strong>
          <?php foreach($tags as $tag): ?><a href="<?php echo get_tag_link($tag->term_id); ?>" class="badge bg-secondary-subtle text-secondary text-decoration-none ms-1"><?php echo esc_html($tag->name); ?></a><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="mt-4 pt-4 border-top">
          <strong class="me-3">Share:</strong>
          <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-twitter-x"></i></a>
          <a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-linkedin"></i></a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-facebook"></i></a>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card border-0 bg-light p-4 mb-4">
          <div class="d-flex gap-3 align-items-center mb-2">
            <div class="avatar-circle"><?php echo strtoupper(substr(get_the_author(),0,2)); ?></div>
            <div><div class="fw-700"><?php the_author(); ?></div><div class="text-muted small">TechCorp Team</div></div>
          </div>
          <p class="text-muted small mb-0">Thought leader in enterprise technology and digital transformation.</p>
        </div>

        <?php
        $cats = get_the_category();
        if($cats) {
            $related = get_posts(['category'=>$cats[0]->term_id,'numberposts'=>3,'post__not_in'=>[get_the_ID()]]);
            if($related):
        ?>
        <div class="card border-0 bg-light p-4">
          <h6 class="fw-700 mb-3">Related Articles</h6>
          <div class="d-flex flex-column gap-3">
            <?php foreach($related as $rp): $rt = get_the_post_thumbnail_url($rp->ID,'techcorp-thumb'); ?>
            <a href="<?php echo get_permalink($rp->ID); ?>" class="text-decoration-none">
              <div class="d-flex gap-2 align-items-start">
                <?php if($rt): ?><img src="<?php echo esc_url($rt); ?>" alt="" class="rounded-2 flex-shrink-0" style="width:60px;height:50px;object-fit:cover;" loading="lazy"><?php endif; ?>
                <div><div class="text-dark fw-600 small lh-sm"><?php echo get_the_title($rp->ID); ?></div><div class="text-muted" style="font-size:.75rem;"><?php echo get_the_date('M d, Y',$rp->ID); ?></div></div>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; } ?>

        <div class="card border-0 bg-primary text-white p-4 mt-4">
          <h6 class="fw-700 mb-2">Ready to get started?</h6>
          <p class="small text-white-75 mb-3">Talk to our experts about your technology needs.</p>
          <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-light btn-sm fw-600">Contact Us</a>
        </div>

        <?php if(is_active_sidebar('blog-sidebar')): dynamic_sidebar('blog-sidebar'); endif; ?>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
