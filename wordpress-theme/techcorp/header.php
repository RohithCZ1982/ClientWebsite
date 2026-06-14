<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="profile" href="https://gmpg.org/xfn/11" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand fw-800 d-flex align-items-center gap-2" href="<?php echo home_url('/'); ?>">
      <div class="brand-icon"><i class="bi bi-hexagon-fill"></i></div>
      <span><?php echo esc_html( tc_opt('company_name', get_bloginfo('name')) ); ?></span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>" href="<?php echo home_url('/'); ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php echo is_page('about') ? 'active' : ''; ?>" href="<?php echo home_url('/about/'); ?>">About</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="<?php echo get_post_type_archive_link('tc_service'); ?>" data-bs-toggle="dropdown">Services</a>
          <ul class="dropdown-menu dropdown-menu-dark border-0 shadow">
            <li><a class="dropdown-item" href="<?php echo get_post_type_archive_link('tc_service'); ?>">All Services</a></li>
            <li><hr class="dropdown-divider opacity-25"></li>
            <?php
            $services = get_posts(['post_type'=>'tc_service','numberposts'=>6,'orderby'=>'menu_order','order'=>'ASC']);
            foreach($services as $s):
                $icon = get_post_meta($s->ID,'_tc_service_icon',true) ?: 'bi-gear';
            ?>
            <li><a class="dropdown-item" href="<?php echo get_permalink($s->ID); ?>"><i class="bi <?php echo esc_attr($icon); ?> me-2"></i><?php echo get_the_title($s->ID); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?php echo is_page('team') ? 'active' : ''; ?>" href="<?php echo home_url('/team/'); ?>">Team</a></li>
        <li class="nav-item"><a class="nav-link <?php echo is_home() || is_singular('post') ? 'active' : ''; ?>" href="<?php echo get_permalink( get_option('page_for_posts') ) ?: home_url('/news/'); ?>">News</a></li>
        <li class="nav-item"><a class="nav-link <?php echo is_post_type_archive('tc_job') ? 'active' : ''; ?>" href="<?php echo get_post_type_archive_link('tc_job'); ?>">Careers</a></li>
        <li class="nav-item"><a class="nav-link <?php echo is_post_type_archive('tc_download') ? 'active' : ''; ?>" href="<?php echo get_post_type_archive_link('tc_download'); ?>">Downloads</a></li>
        <li class="nav-item"><a class="nav-link <?php echo is_page('contact') ? 'active' : ''; ?>" href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
        <li class="nav-item ms-lg-2">
          <?php if ( is_user_logged_in() ) : $user = wp_get_current_user(); ?>
            <div class="dropdown">
              <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i><?php echo esc_html( $user->display_name ); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-0 shadow">
                <?php if ( current_user_can('manage_options') ) : ?>
                <li><a class="dropdown-item" href="<?php echo admin_url(); ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><hr class="dropdown-divider opacity-25"></li>
                <?php endif; ?>
                <li><a class="dropdown-item" href="<?php echo wp_logout_url( home_url() ); ?>"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
              </ul>
            </div>
          <?php else : ?>
            <a href="<?php echo wp_login_url(); ?>" class="btn btn-outline-light btn-sm me-1">Sign In</a>
            <a href="<?php echo wp_registration_url(); ?>" class="btn btn-primary btn-sm">Get Started</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

<?php
// Flash-style notices via session/transients
if ( $notice = get_transient( 'tc_notice_' . ( isset($_COOKIE['tc_uid']) ? $_COOKIE['tc_uid'] : '' ) ) ) {
    delete_transient( 'tc_notice_' . $_COOKIE['tc_uid'] );
    echo '<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show">' . esc_html($notice) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
}
?>
