<?php
/**
 * Custom Post Types & Taxonomies
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── SERVICES CPT ────────────────────────────────────────────── */
function techcorp_register_cpts() {

    register_post_type( 'tc_service', [
        'labels'        => [
            'name'               => 'Services',
            'singular_name'      => 'Service',
            'add_new_item'       => 'Add New Service',
            'edit_item'          => 'Edit Service',
            'menu_name'          => 'Services',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'services' ],
        'menu_icon'     => 'dashicons-admin-settings',
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'menu_position' => 5,
    ] );

    /* ── TEAM CPT ──── */
    register_post_type( 'tc_team', [
        'labels'        => [
            'name'          => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new_item'  => 'Add Team Member',
            'menu_name'     => 'Team',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'team' ],
        'menu_icon'     => 'dashicons-businessperson',
        'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ],
        'menu_position' => 6,
    ] );

    /* ── JOBS CPT ──── */
    register_post_type( 'tc_job', [
        'labels'        => [
            'name'          => 'Job Listings',
            'singular_name' => 'Job',
            'add_new_item'  => 'Add New Job',
            'menu_name'     => 'Jobs',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'careers' ],
        'menu_icon'     => 'dashicons-groups',
        'supports'      => [ 'title', 'editor', 'excerpt', 'custom-fields' ],
        'menu_position' => 7,
    ] );

    /* ── DOWNLOADS CPT ──── */
    register_post_type( 'tc_download', [
        'labels'        => [
            'name'          => 'Downloads',
            'singular_name' => 'Download',
            'add_new_item'  => 'Add New Resource',
            'menu_name'     => 'Downloads',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => true,
        'rewrite'       => [ 'slug' => 'downloads' ],
        'menu_icon'     => 'dashicons-download',
        'supports'      => [ 'title', 'editor', 'custom-fields' ],
        'menu_position' => 8,
    ] );

    /* ── TESTIMONIALS CPT ──── */
    register_post_type( 'tc_testimonial', [
        'labels'        => [
            'name'          => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new_item'  => 'Add Testimonial',
            'menu_name'     => 'Testimonials',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-format-quote',
        'supports'      => [ 'title', 'editor', 'custom-fields', 'thumbnail' ],
        'menu_position' => 9,
    ] );
}
add_action( 'init', 'techcorp_register_cpts' );

/* ── TAXONOMIES ──────────────────────────────────────────────── */
function techcorp_register_taxonomies() {

    // Service Category
    register_taxonomy( 'service_category', [ 'tc_service' ], [
        'labels'        => [ 'name' => 'Service Categories', 'singular_name' => 'Service Category' ],
        'hierarchical'  => true,
        'show_in_rest'  => true,
        'rewrite'       => [ 'slug' => 'service-category' ],
    ] );

    // Job Department
    register_taxonomy( 'job_department', [ 'tc_job' ], [
        'labels'        => [ 'name' => 'Departments', 'singular_name' => 'Department' ],
        'hierarchical'  => true,
        'show_in_rest'  => true,
        'rewrite'       => [ 'slug' => 'department' ],
    ] );

    // Job Type (Full-time, Contract, Part-time)
    register_taxonomy( 'job_type', [ 'tc_job' ], [
        'labels'        => [ 'name' => 'Job Types', 'singular_name' => 'Job Type' ],
        'hierarchical'  => false,
        'show_in_rest'  => true,
        'rewrite'       => [ 'slug' => 'job-type' ],
    ] );

    // Download Category
    register_taxonomy( 'download_category', [ 'tc_download' ], [
        'labels'        => [ 'name' => 'Resource Categories', 'singular_name' => 'Category' ],
        'hierarchical'  => true,
        'show_in_rest'  => true,
        'rewrite'       => [ 'slug' => 'resource-category' ],
    ] );
}
add_action( 'init', 'techcorp_register_taxonomies' );

/* ── CUSTOM META BOXES ───────────────────────────────────────── */
function techcorp_add_meta_boxes() {

    add_meta_box( 'tc_service_meta', 'Service Details', 'techcorp_service_meta_cb', 'tc_service', 'normal', 'high' );
    add_meta_box( 'tc_team_meta',    'Team Member Details', 'techcorp_team_meta_cb', 'tc_team', 'normal', 'high' );
    add_meta_box( 'tc_job_meta',     'Job Details',     'techcorp_job_meta_cb',  'tc_job',  'normal', 'high' );
    add_meta_box( 'tc_download_meta','Download File',   'techcorp_download_meta_cb', 'tc_download', 'normal', 'high' );
    add_meta_box( 'tc_testimonial_meta', 'Testimonial Details', 'techcorp_testimonial_meta_cb', 'tc_testimonial', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'techcorp_add_meta_boxes' );

/* Service meta fields */
function techcorp_service_meta_cb( $post ) {
    wp_nonce_field( 'tc_service_nonce', 'tc_service_nonce' );
    $icon    = get_post_meta( $post->ID, '_tc_service_icon', true );
    $slug    = get_post_meta( $post->ID, '_tc_service_slug', true );
    $order   = get_post_meta( $post->ID, '_tc_service_order', true );
    echo '<table class="form-table"><tbody>';
    echo '<tr><th>Bootstrap Icon Class</th><td><input type="text" name="tc_service_icon" value="' . esc_attr( $icon ) . '" placeholder="bi-cpu" class="regular-text" /></td></tr>';
    echo '<tr><th>Menu Order</th><td><input type="number" name="tc_service_order" value="' . esc_attr( $order ) . '" class="small-text" /></td></tr>';
    echo '</tbody></table>';
}

/* Team meta fields */
function techcorp_team_meta_cb( $post ) {
    wp_nonce_field( 'tc_team_nonce', 'tc_team_nonce' );
    $fields = [
        '_tc_team_title'    => 'Job Title',
        '_tc_team_linkedin' => 'LinkedIn URL',
        '_tc_team_twitter'  => 'Twitter URL',
        '_tc_team_email'    => 'Email',
        '_tc_team_order'    => 'Display Order (number)',
    ];
    echo '<table class="form-table"><tbody>';
    foreach ( $fields as $key => $label ) {
        $val = get_post_meta( $post->ID, $key, true );
        echo "<tr><th>{$label}</th><td><input type=\"text\" name=\"{$key}\" value=\"" . esc_attr( $val ) . "\" class=\"regular-text\" /></td></tr>";
    }
    echo '</tbody></table>';
}

/* Job meta fields */
function techcorp_job_meta_cb( $post ) {
    wp_nonce_field( 'tc_job_nonce', 'tc_job_nonce' );
    $fields = [
        '_tc_job_location'   => 'Location',
        '_tc_job_type'       => 'Type (Full-time/Contract)',
        '_tc_job_experience' => 'Experience Required',
        '_tc_job_salary'     => 'Salary Range (optional)',
        '_tc_job_deadline'   => 'Application Deadline',
    ];
    $is_active = get_post_meta( $post->ID, '_tc_job_active', true );
    echo '<table class="form-table"><tbody>';
    foreach ( $fields as $key => $label ) {
        $val = get_post_meta( $post->ID, $key, true );
        echo "<tr><th>{$label}</th><td><input type=\"text\" name=\"{$key}\" value=\"" . esc_attr( $val ) . "\" class=\"regular-text\" /></td></tr>";
    }
    echo '<tr><th>Status</th><td><label><input type="checkbox" name="_tc_job_active" value="1" ' . checked( $is_active, '1', false ) . ' /> Active / Accepting Applications</label></td></tr>';
    echo '</tbody></table>';
}

/* Download meta fields */
function techcorp_download_meta_cb( $post ) {
    wp_nonce_field( 'tc_download_nonce', 'tc_download_nonce' );
    $file_url  = get_post_meta( $post->ID, '_tc_download_url', true );
    $file_size = get_post_meta( $post->ID, '_tc_download_size', true );
    $file_type = get_post_meta( $post->ID, '_tc_download_type', true );
    echo '<table class="form-table"><tbody>';
    echo '<tr><th>File URL (or Media Library URL)</th><td><input type="url" name="_tc_download_url" value="' . esc_attr( $file_url ) . '" class="large-text" /></td></tr>';
    echo '<tr><th>File Size</th><td><input type="text" name="_tc_download_size" value="' . esc_attr( $file_size ) . '" placeholder="2.4 MB" class="small-text" /></td></tr>';
    echo '<tr><th>File Type</th><td><select name="_tc_download_type"><option value="pdf" ' . selected( $file_type, 'pdf', false ) . '>PDF</option><option value="excel" ' . selected( $file_type, 'excel', false ) . '>Excel</option><option value="word" ' . selected( $file_type, 'word', false ) . '>Word</option><option value="ppt" ' . selected( $file_type, 'ppt', false ) . '>PowerPoint</option><option value="zip" ' . selected( $file_type, 'zip', false ) . '>ZIP</option></select></td></tr>';
    echo '</tbody></table>';
}

/* Testimonial meta fields */
function techcorp_testimonial_meta_cb( $post ) {
    wp_nonce_field( 'tc_testimonial_nonce', 'tc_testimonial_nonce' );
    $company = get_post_meta( $post->ID, '_tc_testimonial_company', true );
    $rating  = get_post_meta( $post->ID, '_tc_testimonial_rating', true ) ?: '5';
    echo '<table class="form-table"><tbody>';
    echo '<tr><th>Company / Title</th><td><input type="text" name="_tc_testimonial_company" value="' . esc_attr( $company ) . '" class="regular-text" /></td></tr>';
    echo '<tr><th>Rating (1–5)</th><td><select name="_tc_testimonial_rating">';
    for ( $i = 5; $i >= 1; $i-- ) {
        echo '<option value="' . $i . '" ' . selected( $rating, $i, false ) . '>' . $i . ' Stars</option>';
    }
    echo '</select></td></tr>';
    echo '</tbody></table>';
}

/* Save meta boxes */
function techcorp_save_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $post_type = get_post_type( $post_id );

    $maps = [
        'tc_service'     => [ 'tc_service_nonce', [ 'tc_service_icon' => '_tc_service_icon', 'tc_service_order' => '_tc_service_order' ] ],
        'tc_team'        => [ 'tc_team_nonce',    [ '_tc_team_title' => '_tc_team_title', '_tc_team_linkedin' => '_tc_team_linkedin', '_tc_team_twitter' => '_tc_team_twitter', '_tc_team_email' => '_tc_team_email', '_tc_team_order' => '_tc_team_order' ] ],
        'tc_job'         => [ 'tc_job_nonce',     [ '_tc_job_location' => '_tc_job_location', '_tc_job_type' => '_tc_job_type', '_tc_job_experience' => '_tc_job_experience', '_tc_job_salary' => '_tc_job_salary', '_tc_job_deadline' => '_tc_job_deadline', '_tc_job_active' => '_tc_job_active' ] ],
        'tc_download'    => [ 'tc_download_nonce', [ '_tc_download_url' => '_tc_download_url', '_tc_download_size' => '_tc_download_size', '_tc_download_type' => '_tc_download_type' ] ],
        'tc_testimonial' => [ 'tc_testimonial_nonce', [ '_tc_testimonial_company' => '_tc_testimonial_company', '_tc_testimonial_rating' => '_tc_testimonial_rating' ] ],
    ];

    if ( ! isset( $maps[ $post_type ] ) ) return;
    list( $nonce_action, $field_map ) = $maps[ $post_type ];

    if ( ! isset( $_POST[ $nonce_action ] ) || ! wp_verify_nonce( $_POST[ $nonce_action ], $nonce_action ) ) return;

    foreach ( $field_map as $input_key => $meta_key ) {
        if ( isset( $_POST[ $input_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $input_key ] ) );
        } else {
            delete_post_meta( $post_id, $meta_key );
        }
    }
}
add_action( 'save_post', 'techcorp_save_meta' );
