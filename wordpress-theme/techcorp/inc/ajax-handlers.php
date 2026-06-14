<?php
/**
 * AJAX Handlers — Contact Form, Newsletter, Job Application, Razorpay
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── CONTACT FORM SUBMISSION ─────────────────────────────────── */
add_action( 'wp_ajax_tc_contact',        'techcorp_handle_contact' );
add_action( 'wp_ajax_nopriv_tc_contact', 'techcorp_handle_contact' );

function techcorp_handle_contact() {
    check_ajax_referer( 'techcorp_nonce', 'nonce' );

    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );
    $company = sanitize_text_field( $_POST['company'] ?? '' );
    $subject = sanitize_text_field( $_POST['subject'] ?? '' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );

    if ( ! $name || ! is_email( $email ) || ! $message ) {
        wp_send_json_error( [ 'message' => 'Please fill in all required fields.' ] );
    }

    // Save as custom post (Enquiry)
    $post_id = wp_insert_post( [
        'post_type'   => 'tc_enquiry',
        'post_title'  => "Enquiry from {$name} — {$subject}",
        'post_status' => 'private',
    ] );
    if ( $post_id ) {
        update_post_meta( $post_id, '_tc_enquiry_name',    $name );
        update_post_meta( $post_id, '_tc_enquiry_email',   $email );
        update_post_meta( $post_id, '_tc_enquiry_phone',   $phone );
        update_post_meta( $post_id, '_tc_enquiry_company', $company );
        update_post_meta( $post_id, '_tc_enquiry_subject', $subject );
        update_post_meta( $post_id, '_tc_enquiry_message', $message );
    }

    // Send notification email
    $to      = get_option( 'techcorp_contact_email', get_option( 'admin_email' ) );
    $headers = [ 'Content-Type: text/plain; charset=UTF-8', "Reply-To: {$name} <{$email}>" ];
    $body    = "New Enquiry\n\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nCompany: {$company}\nSubject: {$subject}\n\nMessage:\n{$message}";
    wp_mail( $to, "New Enquiry: {$subject}", $body, $headers );

    wp_send_json_success( [ 'message' => "Thank you, {$name}! We'll be in touch within 24 hours." ] );
}

/* ── NEWSLETTER SUBSCRIPTION ─────────────────────────────────── */
add_action( 'wp_ajax_tc_newsletter',        'techcorp_handle_newsletter' );
add_action( 'wp_ajax_nopriv_tc_newsletter', 'techcorp_handle_newsletter' );

function techcorp_handle_newsletter() {
    check_ajax_referer( 'techcorp_nonce', 'nonce' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Please provide a valid email address.' ] );
    }
    // Store in options table as simple list
    $subscribers = get_option( 'tc_newsletter_subscribers', [] );
    if ( in_array( $email, $subscribers ) ) {
        wp_send_json_success( [ 'message' => "You're already subscribed!" ] );
    }
    $subscribers[] = $email;
    update_option( 'tc_newsletter_subscribers', $subscribers );
    wp_send_json_success( [ 'message' => 'Thank you for subscribing to our newsletter!' ] );
}

/* ── JOB APPLICATION ─────────────────────────────────────────── */
add_action( 'wp_ajax_tc_apply',        'techcorp_handle_application' );
add_action( 'wp_ajax_nopriv_tc_apply', 'techcorp_handle_application' );

function techcorp_handle_application() {
    check_ajax_referer( 'techcorp_nonce', 'nonce' );

    $name       = sanitize_text_field( $_POST['name'] ?? '' );
    $email      = sanitize_email( $_POST['email'] ?? '' );
    $phone      = sanitize_text_field( $_POST['phone'] ?? '' );
    $position   = sanitize_text_field( $_POST['position'] ?? '' );
    $experience = sanitize_text_field( $_POST['experience'] ?? '' );
    $cover      = sanitize_textarea_field( $_POST['cover'] ?? '' );

    if ( ! $name || ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'Name and email are required.' ] );
    }

    $post_id = wp_insert_post( [
        'post_type'   => 'tc_application',
        'post_title'  => "{$name} — {$position}",
        'post_status' => 'private',
    ] );
    if ( $post_id ) {
        foreach ( [ '_tc_app_name' => $name, '_tc_app_email' => $email, '_tc_app_phone' => $phone, '_tc_app_position' => $position, '_tc_app_experience' => $experience, '_tc_app_cover' => $cover ] as $key => $val ) {
            update_post_meta( $post_id, $key, $val );
        }
    }

    $to   = get_option( 'techcorp_contact_email', get_option( 'admin_email' ) );
    $body = "New Job Application\n\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nPosition: {$position}\nExperience: {$experience}\n\nCover Letter:\n{$cover}";
    wp_mail( $to, "Job Application: {$name} for {$position}", $body );

    wp_send_json_success( [ 'message' => 'Application submitted! We\'ll review and contact you soon.' ] );
}

/* ── RAZORPAY — CREATE ORDER ─────────────────────────────────── */
add_action( 'wp_ajax_tc_razorpay_order',        'techcorp_razorpay_create_order' );
add_action( 'wp_ajax_nopriv_tc_razorpay_order', 'techcorp_razorpay_create_order' );

function techcorp_razorpay_create_order() {
    check_ajax_referer( 'techcorp_nonce', 'nonce' );

    $plan_id = sanitize_text_field( $_POST['plan_id'] ?? '' );
    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );

    $plans = techcorp_get_plans();
    $plan  = $plans[ $plan_id ] ?? null;
    if ( ! $plan ) {
        wp_send_json_error( [ 'message' => 'Invalid plan.' ] );
    }

    $key_id     = get_option( 'techcorp_razorpay_key_id', '' );
    $key_secret = get_option( 'techcorp_razorpay_key_secret', '' );
    $amount     = $plan['price'] * 100; // paise

    if ( ! $key_id || ! $key_secret ) {
        // Demo mode
        wp_send_json_success( [
            'order_id' => 'order_demo_' . uniqid(),
            'amount'   => $amount,
            'currency' => 'INR',
            'key'      => $key_id ?: 'demo_key',
            'demo'     => true,
        ] );
    }

    $url  = 'https://api.razorpay.com/v1/orders';
    $body = wp_json_encode( [
        'amount'   => $amount,
        'currency' => 'INR',
        'receipt'  => 'order_' . $plan_id . '_' . time(),
        'notes'    => [ 'plan' => $plan['name'], 'customer' => $name ],
    ] );

    $response = wp_remote_post( $url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode( "{$key_id}:{$key_secret}" ),
            'Content-Type'  => 'application/json',
        ],
        'body'    => $body,
        'timeout' => 15,
    ] );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( [ 'message' => 'Could not connect to Razorpay.' ] );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    // Save pending payment as post
    $post_id = wp_insert_post( [
        'post_type'   => 'tc_payment',
        'post_title'  => "Order {$data['id']} — {$plan['name']}",
        'post_status' => 'private',
    ] );
    if ( $post_id ) {
        update_post_meta( $post_id, '_tc_pay_order_id',    $data['id'] );
        update_post_meta( $post_id, '_tc_pay_amount',       $amount );
        update_post_meta( $post_id, '_tc_pay_plan',         $plan['name'] );
        update_post_meta( $post_id, '_tc_pay_customer',     $name );
        update_post_meta( $post_id, '_tc_pay_email',        $email );
        update_post_meta( $post_id, '_tc_pay_status',       'pending' );
    }

    wp_send_json_success( [
        'order_id' => $data['id'],
        'amount'   => $amount,
        'currency' => 'INR',
        'key'      => $key_id,
    ] );
}

/* ── RAZORPAY — VERIFY PAYMENT ───────────────────────────────── */
add_action( 'wp_ajax_tc_razorpay_verify',        'techcorp_razorpay_verify' );
add_action( 'wp_ajax_nopriv_tc_razorpay_verify', 'techcorp_razorpay_verify' );

function techcorp_razorpay_verify() {
    check_ajax_referer( 'techcorp_nonce', 'nonce' );

    $order_id   = sanitize_text_field( $_POST['razorpay_order_id'] ?? '' );
    $payment_id = sanitize_text_field( $_POST['razorpay_payment_id'] ?? '' );
    $signature  = sanitize_text_field( $_POST['razorpay_signature'] ?? '' );

    $key_secret = get_option( 'techcorp_razorpay_key_secret', '' );
    $expected   = hash_hmac( 'sha256', "{$order_id}|{$payment_id}", $key_secret );

    if ( hash_equals( $expected, $signature ) ) {
        global $wpdb;
        $posts = get_posts( [ 'post_type' => 'tc_payment', 'posts_per_page' => 1, 'meta_query' => [ [ 'key' => '_tc_pay_order_id', 'value' => $order_id ] ] ] );
        if ( $posts ) {
            update_post_meta( $posts[0]->ID, '_tc_pay_payment_id', $payment_id );
            update_post_meta( $posts[0]->ID, '_tc_pay_status',     'success' );
        }
        wp_send_json_success( [ 'redirect' => home_url( '/payment-success/' ) ] );
    }

    wp_send_json_error( [ 'message' => 'Payment verification failed.' ] );
}

/* ── RAZORPAY — WEBHOOK (REST endpoint) ──────────────────────── */
add_action( 'rest_api_init', function() {
    register_rest_route( 'techcorp/v1', '/razorpay-webhook', [
        'methods'             => 'POST',
        'callback'            => 'techcorp_razorpay_webhook',
        'permission_callback' => '__return_true',
    ] );
} );

function techcorp_razorpay_webhook( WP_REST_Request $request ) {
    $webhook_secret = get_option( 'techcorp_razorpay_webhook_secret', '' );
    $signature      = $request->get_header( 'X-Razorpay-Signature' );
    $body           = $request->get_body();

    $expected = hash_hmac( 'sha256', $body, $webhook_secret );
    if ( ! hash_equals( $expected, $signature ) ) {
        return new WP_REST_Response( [ 'error' => 'Invalid signature' ], 400 );
    }

    $event = $request->get_json_params();
    if ( $event['event'] === 'payment.captured' ) {
        $order_id = $event['payload']['payment']['entity']['order_id'] ?? '';
        $posts = get_posts( [ 'post_type' => 'tc_payment', 'posts_per_page' => 1, 'meta_query' => [ [ 'key' => '_tc_pay_order_id', 'value' => $order_id ] ] ] );
        if ( $posts ) {
            update_post_meta( $posts[0]->ID, '_tc_pay_status', 'success' );
        }
    }

    return new WP_REST_Response( [ 'status' => 'ok' ] );
}

/* ── HELPER: GET PLANS ───────────────────────────────────────── */
function techcorp_get_plans() {
    return [
        'starter' => [
            'name'          => 'Starter',
            'price'         => 4999,
            'price_display' => '₹4,999',
            'period'        => '/month',
            'highlight'     => false,
            'features'      => [ '5 Users', 'Basic Support', '10 GB Storage', 'Core Analytics', 'Email Support' ],
        ],
        'professional' => [
            'name'          => 'Professional',
            'price'         => 14999,
            'price_display' => '₹14,999',
            'period'        => '/month',
            'highlight'     => true,
            'features'      => [ '25 Users', 'Priority Support', '100 GB Storage', 'Advanced Analytics', 'Phone & Email Support', 'API Access' ],
        ],
        'enterprise' => [
            'name'          => 'Enterprise',
            'price'         => 39999,
            'price_display' => '₹39,999',
            'period'        => '/month',
            'highlight'     => false,
            'features'      => [ 'Unlimited Users', '24/7 Dedicated Support', '1 TB Storage', 'Full Analytics Suite', 'Dedicated Account Manager', 'Custom Integrations', 'SLA Guarantee' ],
        ],
    ];
}

/* ── ENQUIRY & APPLICATION CPTs (for storage) ────────────────── */
add_action( 'init', function() {
    register_post_type( 'tc_enquiry',     [ 'labels' => [ 'name' => 'Enquiries',    'menu_name' => 'Enquiries'    ], 'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-email-alt', 'supports' => [ 'title' ], 'menu_position' => 25, 'capabilities' => [ 'create_posts' => 'do_not_allow' ], 'map_meta_cap' => true ] );
    register_post_type( 'tc_application', [ 'labels' => [ 'name' => 'Applications', 'menu_name' => 'Applications' ], 'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-id',        'supports' => [ 'title' ], 'menu_position' => 26, 'capabilities' => [ 'create_posts' => 'do_not_allow' ], 'map_meta_cap' => true ] );
    register_post_type( 'tc_payment',     [ 'labels' => [ 'name' => 'Payments',     'menu_name' => 'Payments'     ], 'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-money-alt',  'supports' => [ 'title' ], 'menu_position' => 27, 'capabilities' => [ 'create_posts' => 'do_not_allow' ], 'map_meta_cap' => true ] );
} );
