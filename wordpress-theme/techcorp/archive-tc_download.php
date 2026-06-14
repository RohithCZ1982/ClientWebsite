<?php get_header(); ?>
<?php tc_page_header('Downloads & <span class="text-primary">Resources</span>', 'Access our library of brochures, guides, and templates.'); ?>

<section class="py-section bg-light">
  <div class="container">
    <?php
    $terms = get_terms(['taxonomy'=>'download_category','hide_empty'=>true]);
    $active_cat = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
    if($terms):
    ?>
    <div class="d-flex flex-wrap gap-2 mb-5">
      <a href="<?php echo get_post_type_archive_link('tc_download'); ?>" class="btn btn-sm <?php echo !$active_cat ? 'btn-primary' : 'btn-outline-secondary'; ?>">All</a>
      <?php foreach($terms as $term): ?>
      <a href="<?php echo add_query_arg('cat',$term->slug); ?>" class="btn btn-sm <?php echo $active_cat===$term->slug ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php echo esc_html($term->name); ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php
    $args = ['post_type'=>'tc_download','numberposts'=>-1,'post_status'=>'publish'];
    if($active_cat) $args['tax_query'] = [['taxonomy'=>'download_category','field'=>'slug','terms'=>$active_cat]];
    $downloads = get_posts($args);
    ?>

    <?php if($downloads): ?>
    <div class="row g-4">
      <?php foreach($downloads as $d):
        $file_url  = get_post_meta($d->ID,'_tc_download_url',true) ?: '#';
        $file_size = get_post_meta($d->ID,'_tc_download_size',true);
        $file_type = get_post_meta($d->ID,'_tc_download_type',true) ?: 'pdf';
        $icons = ['pdf'=>'bi-file-earmark-pdf text-danger','excel'=>'bi-file-earmark-spreadsheet text-success','word'=>'bi-file-earmark-word text-primary','ppt'=>'bi-file-earmark-slides text-warning','zip'=>'bi-file-earmark-zip text-secondary'];
        $icon_class = $icons[$file_type] ?? 'bi-file-earmark text-muted';
        $cats = get_the_terms($d->ID,'download_category');
        $cat_name = $cats ? $cats[0]->name : '';
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="card download-card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="download-file-icon mb-3"><i class="bi <?php echo $icon_class; ?>"></i></div>
            <?php if($cat_name): ?><span class="badge bg-secondary-subtle text-secondary mb-2"><?php echo esc_html($cat_name); ?></span><?php endif; ?>
            <h5 class="fw-700 mb-2"><?php echo get_the_title($d->ID); ?></h5>
            <p class="text-muted small mb-3"><?php echo get_the_excerpt($d->ID); ?></p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small"><?php if($file_size): ?><span><i class="bi bi-file-earmark me-1"></i><?php echo esc_html($file_size); ?></span><?php endif; ?></div>
              <a href="<?php echo esc_url($file_url); ?>" class="btn btn-primary btn-sm" <?php echo $file_url !== '#' ? 'download' : ''; ?>><i class="bi bi-download me-1"></i>Download</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5"><i class="bi bi-folder-x fs-1 text-muted mb-3 d-block"></i><h5 class="text-muted">No resources available yet.</h5></div>
    <?php endif; ?>
  </div>
</section>
<section class="py-5 bg-primary-subtle">
  <div class="container text-center">
    <i class="bi bi-envelope-paper-fill text-primary fs-2 mb-3 d-block"></i>
    <h4 class="fw-700 mb-2">Can't find what you're looking for?</h4>
    <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-primary px-5">Request a Resource</a>
  </div>
</section>
<?php get_footer(); ?>
