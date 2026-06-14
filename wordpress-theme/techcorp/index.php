<?php get_header(); ?>
<?php tc_page_header('News & <span class="text-primary">Insights</span>'); ?>
<section class="py-section bg-light">
  <div class="container">
    <?php if(have_posts()): ?>
    <div class="row g-4">
      <?php while(have_posts()): the_post(); $thumb = get_the_post_thumbnail_url(get_the_ID(),'techcorp-card'); ?>
      <div class="col-md-6 col-lg-4">
        <a href="<?php the_permalink(); ?>" class="card blog-card h-100 text-decoration-none">
          <?php if($thumb): ?><img src="<?php echo esc_url($thumb); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>" loading="lazy" style="height:200px;object-fit:cover;"><?php endif; ?>
          <div class="card-body p-4">
            <h5 class="card-title fw-700 text-dark"><?php the_title(); ?></h5>
            <p class="card-text text-muted small"><?php the_excerpt(); ?></p>
          </div>
          <div class="card-footer bg-transparent border-top-0 px-4 pb-4">
            <small class="text-muted"><?php the_date('M d, Y'); ?></small>
          </div>
        </a>
      </div>
      <?php endwhile; ?>
    </div>
    <?php tc_pagination(); ?>
    <?php else: ?>
    <div class="text-center py-5 text-muted"><i class="bi bi-newspaper fs-1 d-block mb-3"></i><h5>No posts found.</h5></div>
    <?php endif; ?>
  </div>
</section>
<?php get_footer(); ?>
