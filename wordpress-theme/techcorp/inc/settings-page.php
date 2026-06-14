<?php
/**
 * Theme Settings Admin Page
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function() {
    add_menu_page( 'TechCorp Settings', 'TechCorp Settings', 'manage_options', 'techcorp-settings', 'techcorp_settings_page', 'dashicons-admin-generic', 3 );
} );

add_action( 'admin_init', function() {
    $fields = [
        'techcorp_company_name'              => 'Company Name',
        'techcorp_company_tagline'           => 'Company Tagline',
        'techcorp_company_email'             => 'Contact Email',
        'techcorp_company_phone'             => 'Phone Number',
        'techcorp_company_address'           => 'Address',
        'techcorp_company_linkedin'          => 'LinkedIn URL',
        'techcorp_company_twitter'           => 'Twitter URL',
        'techcorp_company_facebook'          => 'Facebook URL',
        'techcorp_company_youtube'           => 'YouTube URL',
        'techcorp_contact_email'             => 'Enquiry Notification Email',
        'techcorp_razorpay_key_id'           => 'Razorpay Key ID',
        'techcorp_razorpay_key_secret'       => 'Razorpay Key Secret',
        'techcorp_razorpay_webhook_secret'   => 'Razorpay Webhook Secret',
        'techcorp_google_analytics_id'       => 'Google Analytics ID (G-XXXXXXXXX)',
        'techcorp_google_maps_key'           => 'Google Maps API Key',
    ];
    foreach ( $fields as $key => $label ) {
        register_setting( 'techcorp_settings', $key, [ 'sanitize_callback' => 'sanitize_text_field' ] );
    }
} );

function techcorp_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'techcorp_settings-options' ) ) {
        // Settings saved by WordPress
    }
    ?>
    <div class="wrap">
        <h1>🏢 TechCorp Theme Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'techcorp_settings' ); ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:1rem;">
                <div>
                    <h2>Company Information</h2>
                    <table class="form-table">
                        <?php
                        $company_fields = [
                            'techcorp_company_name'     => 'Company Name',
                            'techcorp_company_tagline'  => 'Tagline',
                            'techcorp_company_email'    => 'Email',
                            'techcorp_company_phone'    => 'Phone',
                            'techcorp_company_address'  => 'Address',
                            'techcorp_company_linkedin' => 'LinkedIn URL',
                            'techcorp_company_twitter'  => 'Twitter URL',
                            'techcorp_company_facebook' => 'Facebook URL',
                            'techcorp_company_youtube'  => 'YouTube URL',
                            'techcorp_contact_email'    => 'Enquiry Email (receives notifications)',
                        ];
                        foreach ( $company_fields as $key => $label ) :
                            $val = get_option( $key, '' );
                        ?>
                        <tr>
                            <th><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
                            <td><input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" /></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div>
                    <h2>Razorpay Payment Gateway</h2>
                    <div class="notice notice-info inline"><p>Get your keys from <a href="https://dashboard.razorpay.com/app/keys" target="_blank">Razorpay Dashboard → Settings → API Keys</a>. Use <code>rzp_test_</code> prefix for testing.</p></div>
                    <table class="form-table">
                        <?php
                        $rzp_fields = [
                            'techcorp_razorpay_key_id'         => 'Key ID (rzp_test_ or rzp_live_)',
                            'techcorp_razorpay_key_secret'     => 'Key Secret',
                            'techcorp_razorpay_webhook_secret' => 'Webhook Secret',
                        ];
                        foreach ( $rzp_fields as $key => $label ) :
                            $val = get_option( $key, '' );
                        ?>
                        <tr>
                            <th><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
                            <td><input type="<?php echo str_contains( $key, 'secret' ) ? 'password' : 'text'; ?>" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" /></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <h2>Analytics & Maps</h2>
                    <table class="form-table">
                        <?php
                        $misc = [
                            'techcorp_google_analytics_id' => 'Google Analytics ID',
                            'techcorp_google_maps_key'     => 'Google Maps API Key',
                        ];
                        foreach ( $misc as $key => $label ) :
                            $val = get_option( $key, '' );
                        ?>
                        <tr>
                            <th><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
                            <td><input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" /></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php submit_button( 'Save All Settings' ); ?>
        </form>
        <hr>
        <h2>Newsletter Subscribers (<?php echo count( get_option( 'tc_newsletter_subscribers', [] ) ); ?>)</h2>
        <textarea class="large-text" rows="5" readonly><?php echo implode( "\n", get_option( 'tc_newsletter_subscribers', [] ) ); ?></textarea>
    </div>
    <?php
}

/* ── GOOGLE ANALYTICS ────────────────────────────────────────── */
add_action( 'wp_head', function() {
    $ga_id = get_option( 'techcorp_google_analytics_id', '' );
    if ( ! $ga_id ) return;
    echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga_id}\"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga_id}');</script>\n";
} );
