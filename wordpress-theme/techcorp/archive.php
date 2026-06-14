<?php get_header(); ?>
<?php tc_page_header('News & <span class="text-primary">Insights</span>', 'Thought leadership, company updates, and industry perspectives.'); ?>

<section class="py-section bg-light">
  <div class="container">
    <?php $categories = get_categories(['hide_empty'=>true]); ?>
    <?php if($categories): ?>
    <div class="d-flex flex-wrap gap-2 mb-5">
      <a href="<?php echo get_permalink(get_option('page_for_posts')) ?: home_url('/news/'); ?>" class="btn btn-sm <?php echo !is_category() ? 'btn-primary' : 'btn-outline-secondary'; ?>">All</a>
      <?php foreach($categories as $cat): ?>
      <a href="<?php echo get_category_link($cat->term_id); ?>" class="btn btn-sm <?php echo is_category($cat->term_id) ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php echo esc_html($cat->name); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if(have_posts()): ?>
    <div class="row g-4">
      <?php while(have_posts()): the_post(); $thumb = get_the_post_thumbnail_url(get_the_ID(),'techcorp-card'); ?>
      <div class="col-md-6 col-lg-4">
        <a href="<?php the_permalink(); ?>" class="card blog-card h-100 text-decoration-none">
          <?php if($thumb): ?><img src="<?php echo esc_url($thumb); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>" loading="lazy" style="height:200px;object-fit:cover;"><?php else: ?><div class="card-img-top bg-primary-subtle d-flex align-items-center justify-content-center" style="height:200px;"><i class="bi bi-newspaper text-primary" style="font-size:3rem;"></i></div><?php endif; ?>
          <div class="card-body p-4">
            <?php $cats = get_the_category(); if($cats): ?><span class="badge bg-primary-subtle text-primary mb-2"><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?>
            <h5 class="card-title fw-700 text-dark"><?php the_title(); ?></h5>
            <p class="card-text text-muted small"><?php the_excerpt(); ?></p>
          </div>
          <div class="card-footer bg-transparent border-top-0 px-4 pb-4">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2"><div class="avatar-circle-sm"><?php echo strtoupper(substr(get_the_author(),0,1)); ?></div><small class="text-muted"><?php the_author(); ?></small></div>
              <small class="text-muted"><?php the_date('M d, Y'); ?></small>
            </div>
          </div>
        </a>
      </div>
      <?php endwhile; ?>
    </div>
    <?php tc_pagination(); ?>
    <?php else: ?>
    <div class="text-center py-5"><i class="bi bi-newspaper fs-1 text-muted mb-3 d-block"></i><h5 class="text-muted">No posts found.</h5></div>
    <?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>
