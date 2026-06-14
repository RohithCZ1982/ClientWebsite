<footer class="footer-main text-white mt-auto">
  <div class="footer-top py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="brand-icon"><i class="bi bi-hexagon-fill"></i></div>
            <span class="fw-700 fs-5"><?php echo esc_html( tc_opt('company_name', get_bloginfo('name')) ); ?></span>
          </div>
          <p class="text-white-50 mb-4"><?php echo esc_html( tc_opt('company_tagline', get_bloginfo('description')) ); ?>. Delivering enterprise technology solutions that empower businesses to grow, innovate, and lead.</p>
          <div class="d-flex gap-3 social-links">
            <?php if ($l = tc_opt('company_linkedin')): ?><a href="<?php echo esc_url($l); ?>" target="_blank" rel="noopener" title="LinkedIn"><i class="bi bi-linkedin"></i></a><?php endif; ?>
            <?php if ($t = tc_opt('company_twitter')): ?><a href="<?php echo esc_url($t); ?>" target="_blank" rel="noopener" title="Twitter"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
            <?php if ($f = tc_opt('company_facebook')): ?><a href="<?php echo esc_url($f); ?>" target="_blank" rel="noopener" title="Facebook"><i class="bi bi-facebook"></i></a><?php endif; ?>
            <?php if ($y = tc_opt('company_youtube')): ?><a href="<?php echo esc_url($y); ?>" target="_blank" rel="noopener" title="YouTube"><i class="bi bi-youtube"></i></a><?php endif; ?>
          </div>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="footer-heading">Company</h6>
          <ul class="footer-links">
            <li><a href="<?php echo home_url('/about/'); ?>">About Us</a></li>
            <li><a href="<?php echo home_url('/team/'); ?>">Leadership</a></li>
            <li><a href="<?php echo get_post_type_archive_link('tc_job'); ?>">Careers</a></li>
            <li><a href="<?php echo get_permalink( get_option('page_for_posts') ) ?: home_url('/news/'); ?>">News</a></li>
            <li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-6">
          <h6 class="footer-heading">Services</h6>
          <ul class="footer-links">
            <?php
            $svcs = get_posts(['post_type'=>'tc_service','numberposts'=>5,'orderby'=>'menu_order','order'=>'ASC']);
            foreach($svcs as $s): ?>
            <li><a href="<?php echo get_permalink($s->ID); ?>"><?php echo get_the_title($s->ID); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="footer-heading">Stay Updated</h6>
          <p class="text-white-50 small mb-3">Get the latest insights and company news in your inbox.</p>
          <div class="input-group newsletter-form">
            <input type="email" id="footerNewsletterEmail" class="form-control" placeholder="Your email address" />
            <button class="btn btn-primary" type="button" id="footerNewsletterBtn"><i class="bi bi-send"></i></button>
          </div>
          <div id="newsletterMsg" class="mt-2 small text-white-50"></div>
          <div class="mt-4">
            <?php if ($e = tc_opt('company_email')): ?><p class="text-white-50 small mb-1"><i class="bi bi-envelope me-2"></i><?php echo esc_html($e); ?></p><?php endif; ?>
            <?php if ($p = tc_opt('company_phone')): ?><p class="text-white-50 small mb-1"><i class="bi bi-telephone me-2"></i><?php echo esc_html($p); ?></p><?php endif; ?>
            <?php if ($a = tc_opt('company_address')): ?><p class="text-white-50 small"><i class="bi bi-geo-alt me-2"></i><?php echo esc_html($a); ?></p><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 border-top border-white border-opacity-10">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
      <p class="mb-0 text-white-50 small">&copy; <?php echo date('Y'); ?> <?php echo esc_html( tc_opt('company_name', get_bloginfo('name')) ); ?>. All rights reserved.</p>
      <div class="d-flex gap-3">
        <a href="<?php echo home_url('/privacy-policy/'); ?>" class="text-white-50 small text-decoration-none">Privacy Policy</a>
        <a href="<?php echo home_url('/terms-of-service/'); ?>" class="text-white-50 small text-decoration-none">Terms of Service</a>
        <a href="<?php echo home_url('/sitemap_index.xml'); ?>" class="text-white-50 small text-decoration-none">Sitemap</a>
      </div>
    </div>
  </div>
</footer>

<button id="backToTop" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

<?php wp_footer(); ?>
</body>
</html>
