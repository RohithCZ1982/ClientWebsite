<?php get_header(); the_post(); ?>
<?php tc_page_header(); ?>
<section class="py-section bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="page-content"><?php the_content(); ?></div>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
