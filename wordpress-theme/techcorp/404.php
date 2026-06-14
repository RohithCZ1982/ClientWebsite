<?php get_header(); ?>
<section class="py-section bg-white error-page position-relative overflow-hidden">
  <div class="container text-center position-relative" style="z-index:1">
    <div class="mb-4" style="font-size:clamp(4rem,15vw,9rem);font-weight:900;color:var(--tc-primary);opacity:.1;line-height:1;user-select:none">404</div>
    <div style="margin-top:-2rem">
      <div class="mb-3"><i class="bi bi-emoji-frown" style="font-size:3.5rem;color:var(--tc-muted)"></i></div>
      <h1 class="fw-700 mb-3">Page Not Found</h1>
      <p class="text-muted mb-4" style="max-width:460px;margin:0 auto">
        The page you're looking for doesn't exist or has been moved. Let's get you back on track.
      </p>
      <div class="d-flex flex-wrap gap-3 justify-content-center mb-5">
        <a href="<?php echo home_url('/'); ?>" class="btn btn-primary btn-lg px-5">
          <i class="bi bi-house me-2"></i>Go Home
        </a>
        <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-outline-secondary btn-lg px-4">
          <i class="bi bi-chat me-2"></i>Contact Support
        </a>
      </div>

      <!-- Quick links -->
      <div class="row g-3 justify-content-center" style="max-width:680px;margin:0 auto">
        <?php
        $quick = [
          ['bi-grid','Services',     '/services/'],
          ['bi-people','About Us',   '/about/'],
          ['bi-briefcase','Careers', '/careers/'],
          ['bi-newspaper','Blog',    '/news/'],
        ];
        foreach($quick as [$icon, $label, $slug]): ?>
        <div class="col-6 col-md-3">
          <a href="<?php echo home_url($slug); ?>" class="card border hover-shadow text-decoration-none p-3 text-center h-100">
            <i class="bi <?php echo $icon; ?> text-primary fs-3 mb-2 d-block"></i>
            <span class="fw-600 small text-dark"><?php echo $label; ?></span>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<style>
.hover-shadow { transition: box-shadow .2s,transform .2s; }
.hover-shadow:hover { box-shadow: 0 4px 20px rgba(13,110,253,.12); transform: translateY(-3px); }
</style>
<?php get_footer(); ?>
