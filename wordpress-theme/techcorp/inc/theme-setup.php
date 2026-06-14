<?php
/**
 * TechCorp Theme Setup
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── THEME CONSTANTS ─────────────────────────────────────────── */
define( 'TECHCORP_VERSION', '1.0.0' );
define( 'TECHCORP_DIR',     get_template_directory() );
define( 'TECHCORP_URI',     get_template_directory_uri() );

/* ── THEME SUPPORT ───────────────────────────────────────────── */
function techcorp_setup() {
    load_theme_textdomain( 'techcorp', TECHCORP_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','script','style' ] );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );

    add_image_size( 'techcorp-hero',    1920, 800, true );
    add_image_size( 'techcorp-card',     800, 500, true );
    add_image_size( 'techcorp-thumb',    400, 300, true );
    add_image_size( 'techcorp-team',     400, 400, true );
    add_image_size( 'techcorp-service',  600, 400, true );

    register_nav_menus( [
        'primary'     => __( 'Primary Navigation', 'techcorp' ),
        'footer-col1' => __( 'Footer: Company Links', 'techcorp' ),
        'footer-col2' => __( 'Footer: Services Links', 'techcorp' ),
    ] );
}
add_action( 'after_setup_theme', 'techcorp_setup' );

/* ── WIDGET AREAS ────────────────────────────────────────────── */
function techcorp_widgets_init() {
    register_sidebar( [
        'name'          => __( 'Blog Sidebar', 'techcorp' ),
        'id'            => 'blog-sidebar',
        'before_widget' => '<div class="widget mb-4 p-3 bg-light rounded-3">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title fw-700 mb-3">',
        'after_title'   => '</h6>',
    ] );
}
add_action( 'widgets_init', 'techcorp_widgets_init' );

/* ── ENQUEUE SCRIPTS & STYLES ────────────────────────────────── */
function techcorp_enqueue() {
    // Bootstrap 5
    wp_enqueue_style(  'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3' );
    wp_enqueue_style(  'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3' );
    // Theme CSS
    wp_enqueue_style(  'techcorp-theme', TECHCORP_URI . '/assets/css/theme.css', [ 'bootstrap' ], TECHCORP_VERSION );
    // Main stylesheet (style.css — just the header)
    wp_enqueue_style(  'techcorp-style', get_stylesheet_uri(), [ 'techcorp-theme' ], TECHCORP_VERSION );

    // Bootstrap JS
    wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true );
    // Theme JS
    wp_enqueue_script( 'techcorp-main', TECHCORP_URI . '/assets/js/theme.js', [ 'bootstrap' ], TECHCORP_VERSION, true );

    // Localize for AJAX
    wp_localize_script( 'techcorp-main', 'TechCorpAjax', [
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'techcorp_nonce' ),
        'razorpay_key' => get_option( 'techcorp_razorpay_key_id', '' ),
        'site_name'  => get_bloginfo( 'name' ),
    ] );

    if ( is_singular() ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Razorpay on pricing/payment page
    if ( is_page_template( 'page-pricing.php' ) || is_page( 'pricing' ) ) {
        wp_enqueue_script( 'razorpay', 'https://checkout.razorpay.com/v1/checkout.js', [], null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'techcorp_enqueue' );

/* ── SECURITY HEADERS ────────────────────────────────────────── */
function techcorp_security_headers() {
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}
add_action( 'send_headers', 'techcorp_security_headers' );

/* ── EXCERPT LENGTH ──────────────────────────────────────────── */
function techcorp_excerpt_length() { return 25; }
add_filter( 'excerpt_length', 'techcorp_excerpt_length' );

function techcorp_excerpt_more( $more ) { return '…'; }
add_filter( 'excerpt_more', 'techcorp_excerpt_more' );

/* ── BODY CLASSES ────────────────────────────────────────────── */
function techcorp_body_classes( $classes ) {
    if ( is_singular() && ! is_front_page() ) $classes[] = 'singular';
    if ( is_front_page() ) $classes[] = 'home-page';
    return $classes;
}
add_filter( 'body_class', 'techcorp_body_classes' );

/* ── ALLOW SVG UPLOADS ───────────────────────────────────────── */
function techcorp_allow_svg( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'techcorp_allow_svg' );
