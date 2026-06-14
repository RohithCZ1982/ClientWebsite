<?php
/**
 * TechCorp Theme — Main functions.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/settings-page.php';

/* ── HELPER: Company option with fallback ────────────────────── */
function tc_opt( $key, $fallback = '' ) {
    return get_option( "techcorp_{$key}", $fallback );
}

/* ── HELPER: Render star rating ──────────────────────────────── */
function tc_stars( $count = 5 ) {
    $out = '';
    for ( $i = 0; $i < intval( $count ); $i++ ) {
        $out .= '<i class="bi bi-star-fill text-warning"></i>';
    }
    return $out;
}

/* ── HELPER: Get service icon HTML ───────────────────────────── */
function tc_service_icon( $post_id, $size = '' ) {
    $icon  = get_post_meta( $post_id, '_tc_service_icon', true ) ?: 'bi-gear';
    $class = $size === 'lg' ? 'service-icon-lg' : 'service-icon';
    return "<div class=\"{$class}\"><i class=\"bi {$icon}\"></i></div>";
}

/* ── BREADCRUMBS ─────────────────────────────────────────────── */
function tc_breadcrumbs() {
    $separator = '<span class="breadcrumb-separator mx-2">/</span>';
    $home      = '<a href="' . home_url() . '" class="breadcrumb-home">Home</a>';
    $crumbs    = [ $home ];

    if ( is_singular( 'tc_service' ) ) {
        $crumbs[] = '<a href="' . home_url('/services/') . '">Services</a>';
        $crumbs[] = get_the_title();
    } elseif ( is_singular( 'tc_job' ) || is_post_type_archive( 'tc_job' ) ) {
        $crumbs[] = '<a href="' . home_url('/careers/') . '">Careers</a>';
        if ( is_singular() ) $crumbs[] = get_the_title();
    } elseif ( is_singular() || is_page() ) {
        if ( $parent = wp_get_post_parent_id( get_the_ID() ) ) {
            $crumbs[] = '<a href="' . get_permalink( $parent ) . '">' . get_the_title( $parent ) . '</a>';
        }
        $crumbs[] = get_the_title();
    } elseif ( is_archive() ) {
        $crumbs[] = post_type_archive_title( '', false );
    } elseif ( is_search() ) {
        $crumbs[] = 'Search Results for "' . get_search_query() . '"';
    } elseif ( is_404() ) {
        $crumbs[] = '404 Not Found';
    }

    echo '<nav aria-label="breadcrumb"><ol class="breadcrumb justify-content-center mb-0">';
    foreach ( $crumbs as $i => $crumb ) {
        $active = ( $i === count( $crumbs ) - 1 ) ? ' active' : '';
        echo "<li class=\"breadcrumb-item{$active}\">{$crumb}</li>";
    }
    echo '</ol></nav>';
}

/* ── PAGE HEADER BANNER ──────────────────────────────────────── */
function tc_page_header( $title = '', $subtitle = '' ) {
    if ( ! $title ) $title = get_the_title();
    echo '<section class="page-header">';
    echo '<div class="container">';
    echo '<h1>' . wp_kses_post( $title ) . '</h1>';
    if ( $subtitle ) echo '<p class="lead text-white-75">' . esc_html( $subtitle ) . '</p>';
    tc_breadcrumbs();
    echo '</div></section>';
}

/* ── PAGINATION ──────────────────────────────────────────────── */
function tc_pagination() {
    $pages = paginate_links( [
        'type'      => 'array',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ] );
    if ( ! $pages ) return;
    echo '<nav aria-label="Pagination"><ul class="pagination justify-content-center mt-5">';
    foreach ( $pages as $page ) {
        $active = str_contains( $page, 'current' ) ? ' active' : '';
        echo '<li class="page-item' . $active . '"><span class="page-link">' . $page . '</span></li>';
    }
    echo '</ul></nav>';
}

/* ── FLUSH REWRITE RULES ON ACTIVATION ──────────────────────── */
function techcorp_activate() {
    techcorp_register_cpts();
    techcorp_register_taxonomies();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'techcorp_activate' );

/* ── DEMO DATA IMPORT ON FIRST ACTIVATION ────────────────────── */
function techcorp_import_demo_data() {
    if ( get_option( 'techcorp_demo_imported' ) ) return;

    // Default options
    $defaults = [
        'techcorp_company_name'    => get_bloginfo('name') ?: 'TechCorp Solutions',
        'techcorp_company_tagline' => 'Innovating the Future, Today',
        'techcorp_company_email'   => get_option('admin_email'),
        'techcorp_company_phone'   => '+1 (800) 123-4567',
        'techcorp_company_address' => '123 Tech Avenue, Silicon Valley, CA 94025',
        'techcorp_contact_email'   => get_option('admin_email'),
    ];
    foreach ( $defaults as $key => $val ) {
        if ( ! get_option( $key ) ) update_option( $key, $val );
    }

    // Sample services
    $services = [
        [ 'Digital Transformation', 'End-to-end digital transformation services.', 'bi-cpu' ],
        [ 'Cloud Solutions',        'Scalable cloud infrastructure on AWS, Azure, GCP.',  'bi-cloud-arrow-up' ],
        [ 'Cybersecurity',          'Comprehensive security assessments and compliance.',  'bi-shield-lock' ],
        [ 'AI & Machine Learning',  'Custom AI/ML solutions and predictive analytics.',   'bi-robot' ],
        [ 'Mobile & Web Dev',       'Modern responsive web and mobile applications.',     'bi-phone' ],
        [ 'IT Staffing',            'On-demand access to top-tier technology talent.',    'bi-people' ],
    ];
    foreach ( $services as $i => [ $title, $excerpt, $icon ] ) {
        if ( ! get_page_by_path( sanitize_title($title), OBJECT, 'tc_service' ) ) {
            $id = wp_insert_post( [ 'post_type' => 'tc_service', 'post_title' => $title, 'post_excerpt' => $excerpt, 'post_status' => 'publish', 'menu_order' => $i + 1 ] );
            update_post_meta( $id, '_tc_service_icon', $icon );
            update_post_meta( $id, '_tc_service_order', $i + 1 );
        }
    }

    // Sample testimonials
    $testimonials = [
        [ 'Sarah Johnson',  'CTO, RetailMax Inc.',        5, 'TechCorp transformed our legacy infrastructure. The team delivered on time and exceeded performance targets.' ],
        [ 'Michael Chen',   'VP Eng, FinTech Partners',   5, 'Exceptional AI/ML implementation that reduced our fraud detection time by 70%. World-class engineering.' ],
        [ 'Priya Sharma',   'CEO, HealthBridge Solutions', 5, 'Their cybersecurity audit saved us from a potential breach. Professional, thorough, and highly responsive.' ],
    ];
    foreach ( $testimonials as [ $name, $company, $rating, $content ] ) {
        $id = wp_insert_post( [ 'post_type' => 'tc_testimonial', 'post_title' => $name, 'post_content' => $content, 'post_status' => 'publish' ] );
        update_post_meta( $id, '_tc_testimonial_company', $company );
        update_post_meta( $id, '_tc_testimonial_rating',  $rating );
    }

    // Sample team members
    $team = [
        [ 'James Mitchell', 'Chief Executive Officer',      'With 20+ years in enterprise tech, James leads TechCorp\'s vision.' ],
        [ 'Ananya Reddy',   'Chief Technology Officer',     'Ananya drives innovation in cloud-native systems, AI, and cybersecurity.' ],
        [ 'Robert Kim',     'VP, Delivery & Operations',    'Robert oversees global delivery across 12 time zones.' ],
        [ 'Lisa Torres',    'Head of Customer Success',     'Lisa maintains a 98% satisfaction rate through proactive partnership.' ],
    ];
    foreach ( $team as $i => [ $name, $title, $bio ] ) {
        $id = wp_insert_post( [ 'post_type' => 'tc_team', 'post_title' => $name, 'post_content' => $bio, 'post_status' => 'publish', 'menu_order' => $i + 1 ] );
        update_post_meta( $id, '_tc_team_title', $title );
        update_post_meta( $id, '_tc_team_order', $i + 1 );
    }

    // Sample jobs
    $jobs = [
        [ 'Senior Cloud Architect', 'Remote / San Francisco', 'Full-time', 'Engineering', 'Design scalable cloud solutions on AWS/Azure. 8+ years experience required.' ],
        [ 'AI/ML Engineer',         'Remote',                 'Full-time', 'Engineering', 'Build ML models for enterprise clients. Python, TensorFlow required.' ],
        [ 'Cybersecurity Analyst',  'Bangalore, India',       'Full-time', 'Security',    'Security assessments, threat modeling. CISSP preferred.' ],
        [ 'Project Manager',        'Remote / Hyderabad',     'Full-time', 'Operations',  'Lead multi-disciplinary projects. PMP certified, Agile/Scrum experience.' ],
    ];
    foreach ( $jobs as [ $title, $location, $type, $dept, $desc ] ) {
        $id = wp_insert_post( [ 'post_type' => 'tc_job', 'post_title' => $title, 'post_excerpt' => $desc, 'post_status' => 'publish' ] );
        update_post_meta( $id, '_tc_job_location',   $location );
        update_post_meta( $id, '_tc_job_type',       $type );
        update_post_meta( $id, '_tc_job_experience', '5+ years' );
        update_post_meta( $id, '_tc_job_active',     '1' );
        wp_set_object_terms( $id, $dept, 'job_department', false );
        wp_set_object_terms( $id, $type, 'job_type', false );
    }

    // Sample downloads
    $downloads = [
        [ 'Company Brochure 2026',          'Overview of TechCorp services and client success stories.', 'pdf',  '2.4 MB' ],
        [ 'Cloud Migration Checklist',       'Step-by-step guide for planning enterprise cloud migrations.', 'pdf', '1.1 MB' ],
        [ 'Security Assessment Template',    'Evaluate your organization\'s security posture.', 'excel', '340 KB' ],
    ];
    foreach ( $downloads as [ $title, $desc, $type, $size ] ) {
        $id = wp_insert_post( [ 'post_type' => 'tc_download', 'post_title' => $title, 'post_excerpt' => $desc, 'post_status' => 'publish' ] );
        update_post_meta( $id, '_tc_download_type', $type );
        update_post_meta( $id, '_tc_download_size', $size );
        update_post_meta( $id, '_tc_download_url',  '#' ); // Replace with real URL
        wp_set_object_terms( $id, 'Resources', 'download_category', false );
    }

    // Sample blog posts
    $posts = [
        [ 'TechCorp Launches Next-Gen Cloud Platform', 'Company News', 'Our new cloud platform offers 40% better performance. ## Introducing CloudPilot 2.0\n\nWe are thrilled to announce **CloudPilot 2.0**, our next-generation cloud management platform.' ],
        [ '5 Cybersecurity Trends Every CTO Should Know in 2026', 'Security', 'From AI-powered threats to zero-trust architecture. ## The Threat Landscape Is Evolving\n\nCybersecurity is now a boardroom priority. Here are five trends reshaping the field.' ],
        [ 'How AI is Transforming Enterprise Software Development', 'Technology', 'AI is rewriting the rules of software delivery. ## AI in the Developer Toolchain\n\nThe software development lifecycle is undergoing its most significant transformation.' ],
    ];
    foreach ( $posts as [ $title, $cat, $content ] ) {
        if ( ! get_page_by_title( $title, OBJECT, 'post' ) ) {
            $id = wp_insert_post( [ 'post_type' => 'post', 'post_title' => $title, 'post_content' => $content, 'post_status' => 'publish', 'post_category' => [ get_cat_ID($cat) ?: wp_create_category($cat) ] ] );
        }
    }

    update_option( 'techcorp_demo_imported', true );
}
add_action( 'after_switch_theme', 'techcorp_import_demo_data', 20 );

/* ── DISABLE GUTENBERG FOR CPTs ──────────────────────────────── */
add_filter( 'use_block_editor_for_post_type', function( $use, $post_type ) {
    $classic = [ 'tc_enquiry', 'tc_application', 'tc_payment' ];
    return in_array( $post_type, $classic ) ? false : $use;
}, 10, 2 );
