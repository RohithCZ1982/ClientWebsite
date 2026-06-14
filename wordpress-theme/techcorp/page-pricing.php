<?php /* Template Name: Pricing */ get_header(); ?>
<?php tc_page_header('Simple, Transparent <span class="text-primary">Pricing</span>', 'Choose the plan that fits your business. No hidden fees.'); ?>

<section class="py-section bg-light">
  <div class="container">

    <!-- plan toggle hint -->
    <div class="text-center mb-5">
      <p class="text-muted">All plans include a 30-day money-back guarantee. Prices in Indian Rupees (INR) + GST.</p>
    </div>

    <?php $plans = techcorp_get_plans(); ?>
    <div class="row g-4 justify-content-center">
      <?php foreach($plans as $i => $plan): $featured = $plan['featured'] ?? false; ?>
      <div class="col-md-6 col-lg-4 fade-in">
        <div class="pricing-card<?php echo $featured ? ' featured shadow-lg' : ''; ?>">
          <?php if($featured): ?><span class="pricing-badge">Most Popular</span><?php endif; ?>
          <h4 class="fw-700 mb-1"><?php echo esc_html($plan['name']); ?></h4>
          <p class="text-muted small mb-4"><?php echo esc_html($plan['tagline']); ?></p>

          <div class="pricing-price mb-4">
            <sup>₹</sup><?php echo number_format($plan['amount'] / 100); ?>
            <small class="d-block mt-1">one-time setup</small>
          </div>

          <ul class="pricing-features">
            <?php foreach($plan['features'] as $f): ?>
            <li>
              <i class="bi bi-check-circle-fill"></i>
              <?php echo esc_html($f); ?>
            </li>
            <?php endforeach; ?>
            <?php if(!empty($plan['not_included'])): foreach($plan['not_included'] as $f): ?>
            <li class="text-muted">
              <i class="bi bi-x-circle"></i>
              <?php echo esc_html($f); ?>
            </li>
            <?php endforeach; endif; ?>
          </ul>

          <?php if(get_option('techcorp_razorpay_key_id')): ?>
          <button class="btn btn-<?php echo $featured ? 'primary' : 'outline-primary'; ?> w-100 btn-lg"
                  data-plan="<?php echo $i; ?>"
                  onclick="tcRazorpayCheckout(<?php echo $i; ?>)">
            Get Started
          </button>
          <?php else: ?>
          <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-<?php echo $featured ? 'primary' : 'outline-primary'; ?> w-100 btn-lg">
            Contact Us to Purchase
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- FAQ -->
    <div class="row justify-content-center mt-5">
      <div class="col-lg-8">
        <h3 class="fw-700 text-center mb-4">Frequently Asked <span class="text-primary">Questions</span></h3>
        <div class="accordion" id="pricingFaq">
          <?php
          $faqs = [
            ['What is included in the setup fee?',
             'The one-time fee covers full website development, theme setup, content import, Razorpay integration, testing, and a 30-day post-launch support window.'],
            ['Are there recurring charges after setup?',
             'Yes — hosting (~₹150–300/mo), domain (~₹999/yr), SSL (free via Let\'s Encrypt or ~₹499/yr), Google Workspace email (~₹125/user/mo), and any premium plugin subscriptions.'],
            ['Do you offer monthly payment plans?',
             'Yes. We offer 40% upfront / 40% at staging / 20% on launch. EMI options available via Razorpay for qualifying amounts.'],
            ['What payment methods are accepted?',
             'All major cards, UPI, net banking, and wallets via Razorpay. Bank transfers available for Enterprise orders.'],
            ['Can I upgrade my plan later?',
             'Absolutely. You can start with Starter and upgrade any time. We\'ll bill the difference.'],
          ];
          foreach($faqs as $idx => $faq): ?>
          <div class="accordion-item border mb-2 rounded-3 overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button<?php echo $idx ? ' collapsed' : ''; ?> fw-600"
                      type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $idx; ?>">
                <?php echo esc_html($faq[0]); ?>
              </button>
            </h2>
            <div id="faq<?php echo $idx; ?>" class="accordion-collapse collapse<?php echo !$idx ? ' show' : ''; ?>" data-bs-parent="#pricingFaq">
              <div class="accordion-body text-muted"><?php echo esc_html($faq[1]); ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Contact CTA -->
    <div class="text-center mt-5">
      <p class="text-muted mb-3">Need a custom plan or have questions?</p>
      <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-primary btn-lg px-5">
        <i class="bi bi-chat-dots me-2"></i>Talk to Sales
      </a>
    </div>
  </div>
</section>

<!-- Payment success/fail banners (shown via URL param) -->
<?php if(isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
<div class="container mt-3">
  <div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i>
    Payment successful! Welcome aboard. You'll receive a confirmation email shortly.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php elseif(isset($_GET['payment']) && $_GET['payment'] === 'failed'): ?>
<div class="container mt-3">
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle-fill me-2"></i>
    Payment could not be completed. Please try again or <a href="<?php echo home_url('/contact/'); ?>">contact us</a>.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
