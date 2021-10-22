<?php
/**
 * Plugin Name: Freshbooks Client Portal
 * Description: Provides client portal functionality for Freshbooks account holders.
 * Version: 1.2
 * Author: Mythic Design Company
 * Author URI: https://mythicdesigncompany.com/
 * Requires at least: 4.0
 * Requires PHP: 7.1
 */

defined( 'ABSPATH' ) or die( esc_html_e( 'No script kiddies please!', 'mythic-fcp' ) );

// Activate Functions
register_activation_hook( __FILE__, 'mythic_fcp_activation' );
function mythic_fcp_activation() {

}

// Register Plugin Settings
add_action( 'admin_init', 'mythic_fcp_register_settings' );
function mythic_fcp_register_settings() {
    add_option( 'mythic_fcp_remove_on_uninstall', '0');
    register_setting( 'mythic_fcp_options', 'mythic_fcp_remove_on_uninstall', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
    add_option( 'mythic_fcp_client_id', '');
    register_setting( 'mythic_fcp_token_options', 'mythic_fcp_client_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );

    add_option( 'mythic_fcp_client_secret', '');
    register_setting( 'mythic_fcp_token_options', 'mythic_fcp_client_secret', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
    add_option( 'mythic_fcp_bearer_token', '');
    register_setting( 'mythic_fcp_token_options', 'mythic_fcp_bearer_token', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
    add_option( 'mythic_fcp_refresh_token', '');
    register_setting( 'mythic_fcp_token_options', 'mythic_fcp_refresh_token', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
    add_option( 'mythic_fcp_token_expiry', '' );
    register_setting( 'mythic_fcp_token_options', 'mythic_fcp_token_expiry', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
    add_option( 'mythic_fcp_account_id', '');
    register_setting( 'mythic_fcp_identity_info', 'mythic_fcp_account_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );

    add_option( 'mythic_fcp_business_id', '');
    register_setting( 'mythic_fcp_identity_info', 'mythic_fcp_business_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
}

// Include WP_Http Class
add_action( 'plugins_loaded', 'mythic_fcp_class_includes' );
function mythic_fcp_class_includes() {
    if( !class_exists( 'WP_Http_Curl' ) ) {
        include_once( ABSPATH . WPINC. '/class-wp-http-curl.php' );
    }
}

// Create Options Page    
add_action( 'admin_menu', 'mythic_fcp_register_options_page' );
function mythic_fcp_register_options_page() {
  add_options_page( 'Freshbooks Client Portal', 'Freshbooks Client Portal', 'manage_options', 'mythic-fcp', 'mythic_fcp_token_options_page' );
}

// Options Page Content
function mythic_fcp_token_options_page() {
    
    $default_tab = null;
    $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ) : $default_tab;

    ?>

    <div class="wrap mythic_fcp">
        <nav class="nav-tab-wrapper">
            <a href="?page=mythic-fcp" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php esc_html_e(' Freshbooks Connection', 'mythic-fcp' ); ?></a>
            <a href="?page=mythic-fcp&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e( 'Settings', 'mythic-fcp' ); ?></a>
        </nav>
        <div class="tab-content">
        <?php

        switch($tab) {
            case 'settings';
                // Settings Tab
                ?>
        
                <h1><?php esc_html_e( 'Freshbooks Client Portal Plugin Settings', 'mythic-fcp' ); ?></h1>
                <form method="post" action="options.php">
                    <?php settings_fields( 'mythic_fcp_options' ); ?>

                    <input type="hidden" id="" name="mythic_fcp_remove_on_uninstall" value="0">
                    <input type="checkbox" id="mythic_fcp_remove_on_uninstall" name="mythic_fcp_remove_on_uninstall" value="1">
                    <label for="mythic_fcp_remove_on_uninstall"><?php esc_html_e( 'Check this box to remove plugin data on uninstall.', 'mythic-fcp' ); ?></label>

                    <?php submit_button(); ?>
                </form>

                <?php
                break;
            default;
                // Default Tab
                ?>

                <h1><?php esc_html_e( 'Freshbooks Connection Settings', 'mythic-fcp' ); ?></h1>
                <div class="col-1">
                    <form method="post" action="options.php">
                        <?php settings_fields( 'mythic_fcp_token_options' ); ?>
                        <h3><?php esc_html_e( 'Step 1) Application Settings', 'mythic-fcp' ); ?></h3>
                        <table class="mythic-fcp-table">
                            <tr valign="middle">
                                <th scope="row"><label for="mythic_fcp_client_id"><?php esc_html_e( 'Client ID', 'mythic-fcp' ); ?></label></th>
                                <td><input type="text" id="mythic_fcp_client_id" name="mythic_fcp_client_id" value="<?php echo esc_attr( get_option('mythic_fcp_client_id') ); ?>" /></td>
                            </tr>
                            <tr valign="middle">
                                <th scope="row"><label for="mythic_fcp_client_secret"><?php esc_html_e( 'Client Secret', 'mythic-fcp' ); ?></label></th>
                                <td><input type="text" id="mythic_fcp_client_secret" name="mythic_fcp_client_secret" value="<?php echo esc_attr( get_option('mythic_fcp_client_secret') ); ?>" /></td>
                            </tr>
                        </table>
                        
                        <?php submit_button(); ?>
                    </form>

                    <h3><?php esc_html_e( 'Step 2) Freshbooks Authorization', 'mythic-fcp' ); ?></h3>    
                    <?php
                        if(get_option('mythic_fcp_bearer_token') && get_option('mythic_fcp_refresh_token') && get_option('mythic_fcp_token_expiry')) {
                            echo "<p><strong>" . esc_html_e('Authentication Status:', 'mythic-fcp' ) . "</strong>" . esc_html_e( 'Connected', 'mythic-fcp' ) . "<span class='dashicons dashicons-yes-alt'></span></p>";
                        } else {
                            echo "<p><strong>" . esc_html_e( 'Authentication Status:', 'mythic-fcp' ) . "</strong>" . esc_html_e('Not Connected', 'mythic-fcp' ) . "<span class='dashicons dashicons-dismiss'></span></p>";
                        }
                    ?>

                    <a href="https://auth.freshbooks.com/service/auth/oauth/authorize?client_id=<?php echo get_option('mythic_fcp_client_id'); ?>&response_type=code&redirect_uri=<?php echo get_admin_url(null, 'options-general.php', 'https'); ?>&state=mythic_fcp_auth" class="button button-primary"><?php esc_html_e( 'Connect with Freshbooks', 'mythic-fcp' ); ?></a>

                    <h3><?php esc_html_e( 'Step 3) Business Identity Information', 'mythic-fcp' ); ?></h3>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'mythic_fcp_identity_info' ); ?>
                        <table class="mythic-fcp-table">
                            <?php 

                            $bearer_token = get_option('mythic_fcp_bearer_token');

                            // WP_Http Request        
                            $api_url = "https://api.freshbooks.com/auth/api/v1/users/me";
                            $api_args = array(
                                'method' => 'GET',
                                'timeout' => 30.000,
                                'redirection' => 10,
                                'headers' => array(
                                    "Authorization" => "Bearer " . $bearer_token,
                                    "Api-Version" => "alpha",
                                    "Content-Type" => "application/json"
                                ),
                            );
                            $request = new WP_Http_Curl;
                            $result = $request->request( $api_url, $api_args );
                            $response = $result['body'];
                            $json = json_decode($response, true);

                            $business_memberships = $json["response"]["business_memberships"];

                            ?>

                            <tr valign="middle">
                                <th scope="row"><label for="mythic_fcp_account_id"><?php esc_html_e( 'Select Your Account', 'mythic-fcp' ); ?></label></th>
                                <td><select name="mythic_fcp_account_id" id="mythic_fcp_account_id">
                                    <?php
                                        foreach($business_memberships as $business) {
                                            echo( "<option value='" . esc_attr( $business['business']['account_id'] ) . "'>" . esc_html( $business['business']['name'] ) . "</option>" );
                                        }
                                    ?>
                                </select></td>
                            </tr>
                            <tr valign="middle">
                                <th scope="row"><label for="mythic_fcp_business_id"><?php esc_html_e( 'Select Your Business', 'mythic-fcp' ); ?></label></th>
                                <td><select name="mythic_fcp_business_id" id="mythic_fcp_business_id">
                                    <?php
                                        foreach($business_memberships as $business) {
                                            echo( "<option value='" . esc_attr( $business['business']['id'] ) . "'>" . esc_html( $business['business']['name'] ) . "</option>" );
                                        }
                                    ?>
                                </select></td>
                            </tr> 
                        </table>
                        
                        <?php submit_button(); ?>
                    </form>
                </div>
                <div class="col-2">
                    <div class='embed-container'>
                        <iframe src='https://www.youtube.com/embed/m_bflx_1AQs' frameborder='0' allowfullscreen></iframe>
                    </div>
                    <p><?php esc_html_e('Pleasea also select the following scopes in the settings page:', 'mythic-fcp'); ?></p>
                    <ul>
                        <li>user:profile:read</li>
                        <li>user:clients:read</li>
                        <li>user:invoices:read</li>
                    </ul>
                </div>

                <?php
                break;
        }

        ?>
        </div>
    </div>

    <?php
    
}

// Authorize With Freshbooks
add_action( 'admin_init', 'mythic_fcp_authorize_app');
function mythic_fcp_authorize_app() {
    if( isset($_GET['code']) && $_GET['state'] == "mythic_fcp_auth" ) {

        // WP_Http Request
        $client_id = trim(get_option('mythic_fcp_client_id'));
        $client_secret = trim(get_option('mythic_fcp_client_secret'));
        $auth_code = sanitize_text_field( $_GET['code'] );
        $redirect_uri = get_admin_url(null, 'options-general.php', 'https');

        $api_url = "https://api.freshbooks.com/auth/oauth/token";
        $api_body = '{
            "grant_type": "authorization_code",
            "client_secret": "' . $client_secret .'",
            "client_id": "' .$client_id .'",
            "code": "' . $auth_code . '",
            "redirect_uri": "'. $redirect_uri . '"
        }';
        
        $api_args = array(
            'method' => 'POST',
            'timeout' => 30.000,
            'redirection' => 10,
            'body' => $api_body,
            'headers' => array(
                "Api-Version" => "alpha",
                "Content-Type" => "application/json",
                "cache-control" => "no-cache"
            )
        );
        
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json = json_decode($response, true);

        $bearer_token = $json["access_token"];
        $refresh_token = $json["refresh_token"];
        $expiry_time = $json["expires_in"];
        $expiry_date = new DateTime();
        $expiry_date->add(new DateInterval('PT' . $expiry_time . 'S'));
        $expiry_date_string = $expiry_date->format('Y-m-d H:i:s');

        update_option('mythic_fcp_bearer_token', $bearer_token);
        update_option('mythic_fcp_refresh_token', $refresh_token);
        update_option('mythic_fcp_token_expiry', $expiry_date_string);

        wp_redirect( admin_url( '/options-general.php?page=mythic-fcp' ) );

    }
}

// Refresh Bearer Token
function mythic_fcp_refresh_token() {
    $current_date = new DateTime();
    
    $current_date_str = $current_date->format('Y-m-d H:i:s');
    $expiry_date_str = get_option('mythic_fcp_token_expiry');
    
    // Make sure there are tokens
    if(!get_option('mythic_fcp_bearer_token') && !get_option('mythic_fcp_refresh_token')) {
        return false;
    }
    
    // If Current Date is Past Expiration Date
    if($current_date_str < $expiry_date_str) {
        return true;
    } else {
        // Token is Expired, Refresh Token
        $client_id = get_option('mythic_fcp_client_id');
        $client_secret = get_option('mythic_fcp_client_secret');
        $refresh_token = get_option('mythic_fcp_refresh_token');
        $redirect_uri = get_admin_url(null, 'options-general.php', 'https');
        
        // WP_Http Request
        $api_url = "https://api.freshbooks.com/auth/oauth/token";
        $api_body = '{
            "grant_type": "refresh_token",
            "client_secret": "' . esc_html( $client_secret ) .'",
            "refresh_token": "' . esc_html( $refresh_token ) . '",
            "client_id": "' . esc_html( $client_id ) .'",
            "redirect_uri": "'. esc_html( $redirect_uri ) . '"
        }';
        $api_args = array(
            'method' => 'POST',
            'timeout' => 30.000,
            'redirection' => 10,
            'body' => $api_body,
            'headers' => array(
                "api-Version" => "alpha",
                "cache-control" => "no-cache",
                "content-type" => "application/json",
                
            )
        );
        
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json = json_decode($response, true);
        
        $bearer_token = $json["access_token"];
        $refresh_token = $json["refresh_token"];
        $expiry_time = $json["expires_in"];
        $expiry_date = new DateTime();
        $expiry_date->add(new DateInterval('PT' . $expiry_time . 'S'));
        $expiry_date_string = $expiry_date->format('Y-m-d H:i:s');

        update_option('mythic_fcp_bearer_token', $bearer_token);
        update_option('mythic_fcp_refresh_token', $refresh_token);
        update_option('mythic_fcp_token_expiry', $expiry_date_string);

        // Tokens Updated!
        return true;

    }
}

// Find Client ID On User Login
add_action( 'wp_login', 'mythic_fcp_attach_client_id', 99, 2);
function mythic_fcp_attach_client_id($login, $user) {
    // Make Sure We Still Have Access to Freshbooks
    if(!mythic_fcp_refresh_token()) { return false; }

    $bearer_token = get_option('mythic_fcp_bearer_token');
    
    $page = 1;
    
    // WP_Http Request        
    $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/users/clients?per_page=100&page=" . $page;
    $api_args = array(
        'method' => 'GET',
        'timeout' => 30.000,
        'redirection' => 10,
        'headers' => array(
            "Api-Version" => "alpha",
            "Authorization" => "Bearer " . $bearer_token,
            "Content-Type" => "application/json"
        ),


    );
    $request = new WP_Http_Curl;
    $result = $request->request( $api_url, $api_args );
    $response = $result['body'];
    $json = json_decode($response, true);
    
    // Check if there's more than 100 clients
    $client_num = count($json["response"]["result"]["clients"]);
    
    while($client_num == 100) {
        // Get next page of clients until all pages are retrieved
        $page += 1;
        
        // WP_Http Request
        $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/users/clients?per_page=100&page=" . $page;
        $api_args = array(
            'method' => 'GET',
            'timeout' => 30.000,
            'redirection' => 10,
            'headers' => array(
                "Api-Version" => "alpha",
                "Authorization" => "Bearer " . $bearer_token,
                "Content-Type" => "application/json"
            )
        );
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json2 = json_decode($response, true);
        
        // Merge client lists
        $json = array_merge_recursive($json,$json2);
        $client_num = count($json["response"]["result"]["clients"]);
    }

    $user_email = $user->user_email;

    foreach($json["response"]["result"]["clients"] as $client) {
        if($client["email"] === $user_email) {

            if(!get_user_meta($user->ID, 'mythic_fcp_client_id', true)) {
                add_user_meta($user->ID, 'mythic_fcp_client_id', $client["userid"]);
            }
        }
    }
}

// Client Portal Shortcode
add_shortcode('fcp_client', 'mythic_fcp_show_client');
function mythic_fcp_show_client() {
	wp_enqueue_script( 'fcp-frontend' );
	wp_enqueue_style( 'mythic_fcp_frontend_css' );
	
    // Make Sure We Still Have Access to Freshbooks
    if(!mythic_fcp_refresh_token()) { return false; }
    
    $user_id = get_current_user_id();    
    
    if(get_user_meta($user_id, 'mythic_fcp_client_id', true)) {
        $fcp_client_id = get_user_meta($user_id, 'mythic_fcp_client_id', true);
        
        $bearer_token = get_option('mythic_fcp_bearer_token');
        
        // WP_Http Request        
        $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/users/clients/" . $fcp_client_id . "?include[]=outstanding_balance&include[]=credit_balance&include[]=draft_balance&include[]=overdue_balance&include[]=grand_total_balance";
        $api_args = array(
            'method' => 'GET',
            'timeout' => 30.000,
            'redirection' => 10,
            'headers' => array(
                "Api-Version" => "alpha",
                "Authorization" => "Bearer " . $bearer_token,
                "Content-Type" => "application/json"
            )
        );
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json = json_decode($response, true);
                
        return mythic_fcp_render_client_info($json);

    } else {
        return "<p>" . esc_html__('No Freshbooks data has been found for your account.', 'mythic-fcp') . "</p>";
    }
}

// Register Frontend JS
add_action( 'wp_enqueue_scripts', 'mythic_fcp_register_scripts' );
function mythic_fcp_register_scripts() {
    wp_register_script( 'fcp-frontend', plugins_url( 'assets/js/fcp-frontend.js', __FILE__) );
}

// Register Frontend CSS
add_action( 'wp_enqueue_scripts', 'mythic_fcp_register_style' );
function mythic_fcp_register_style() {
    wp_register_style( 'mythic_fcp_frontend_css', plugins_url( 'assets/css/style.css', __FILE__), array(), '1.0.0', 'all');
}

// Register Admin CSS
add_action( 'admin_enqueue_scripts', 'mythic_fcp_register_admin_style' );
function mythic_fcp_register_admin_style() {
    wp_register_style( 'mythic_fcp_admin_css', plugins_url( 'assets/css/admin.css', __FILE__), array(), '1.0.0', 'all');
    wp_enqueue_style('mythic_fcp_admin_css');
}

// Render Client Info
function mythic_fcp_render_client_info($json) {
    $bearer_token = get_option('mythic_fcp_bearer_token');
    
    $info = $json['response']['result']['client'];
    
    $client_accounting_id = $info["accounting_systemid"];
    $client_business_phone = $info["bus_phone"];
    $client_industry = $info["company_industry"];
    $client_company_size = $info["company_size"];
    $client_currency_code = $info["currency_code"];
    $client_email = $info["email"];
    $client_fax = $info["fax"];
    $client_fname = $info["fname"];
    $client_has_retainer = $info["has_retainer"];
    $client_home_phone = $info["home_phone"];
    $client_id = $info["id"];
    $client_language = $info["language"];
    $client_last_activity = $info["last_activity"];
    $client_last_login = $info["last_login"];
    $client_lname = $info["lname"];
    $client_mob_phone = $info["mob_phone"];
    $client_num_logins = $info["num_logins"];
    $client_organization = $info["organization"];
    $client_billing_city = $info["p_city"];
    $client_billing_postcode = $info["p_code"];
    $client_billing_country = $info["p_country"];
    $client_billing_province = $info["p_province"];
    $client_billing_street = $info["p_street"];
    $client_billing_street2 = $info["p_street2"];
    $client_pref_email = $info["pref_email"];
    $client_pref_mail = $info["pref_gmail"];
    $client_retainer_id = $info["retainer_id"];
    $client_role = $info["role"];
    $client_shipping_city = $info["s_city"];
    $client_shipping_postcode = $info["s_code"];
    $client_shipping_country = $info["s_country"];
    $client_shipping_province = $info["s_province"];
    $client_shipping_street = $info["s_street"];
    $client_shipping_street2 = $info["s_street2"];
    $client_signup_date = $info["signup_date"];
    $client_statement_token = $info["statement_token"];
    $client_subdomain = $info["subdomain"];
    $client_last_updated = $info["updated"];
    $client_username = $info["username"];
    $client_vat_name = $info["vat_name"];
    $client_vat_number = $info["vat_number"];
    $client_visibility = $info["vis_state"];
    $client_outstanding_balance = $info["outstanding_balance"];
    $client_credit_balance = $info["credit_balance"];
    $client_draft_balance = $info["draft_balance"];
    $client_overdue_balance = $info["overdue_balance"];
    $client_grand_total_balance = $info["grand_total_balance"];
    
    $output = "";
    
    $output.= '<div class="fcp_container">';
    $output.= '    <div class="fcp_header">';
    $output.= '        <div class="welcome"><h1>' . esc_html__( 'Hello', 'mythic-fcp' );
    
                        if($client_fname) {
                            $output.= esc_html__( ' ', 'mythic-fcp' ) . ' ' . esc_html( $client_fname );
                        }
    
    $output.=           '!</h1></div>';
    
    $output.= '        <div class="balance">';
    $output.= '            <p><strong>' . esc_html__( 'Total Outstanding:', 'mythic-fcp' ) . '</strong>$';
    
                            if($client_outstanding_balance) { 
                                $output .= ( esc_html( $client_outstanding_balance[0]["amount"]["amount"] ) . ' ' . esc_html__( ' ', 'mythic-fcp' ) . ' ' .esc_html( $client_outstanding_balance[0]["amount"]["code"] ) );
                            } else {
                                $output .= esc_html__( '0.00', 'mythic-fcp' );
                            }
    
    $output.= '             </p>';
    $output.= '         </div>';
    $output.= '    </div>';
    $output.= '    <div class="fcp_body">';
    $output.=          mythic_fcp_render_client_invoices();
    $output.= '    </div>';
    $output.= '</div>';
    
    return $output;
    
}

// Render Client Invoices List
function mythic_fcp_render_client_invoices() {
    $user_id = get_current_user_id();    
    $fcp_client_id = get_user_meta($user_id, 'mythic_fcp_client_id', true);
    $bearer_token = get_option('mythic_fcp_bearer_token');

    $page = "";
    
    if(isset($_GET['fcp_page'])) {
        $p = sanitize_text_field( $_GET['fcp_page'] );
        $page = "&page=" . $p . "";
    } else {
        $page = "";
    }
    
    // WP_Http Request        
    $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices?include[]=direct_links&per_page=10" . $page . "&search[customerid]=" . $fcp_client_id;
    $api_args = array(
        'method' => 'GET',
        'timeout' => 30.000,
        'redirection' => 10,
        'headers' => array(
            "Api-Version" => "alpha",
            "Authorization" => "Bearer " . $bearer_token,
            "Content-Type" => "application/json"
        ),


    );
    $request = new WP_Http_Curl;
    $result = $request->request( $api_url, $api_args );
    $response = $result['body'];
    $json = json_decode($response, true);
    
    $invoices =  $json["response"]["result"]["invoices"];
    $output = "";

    $output.='<table>';
    $output.='    <thead>';
    $output.='        <tr>';
    $output.='            <th scope="col">' . esc_html__( 'Invoice Number', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Date Created', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Due Date', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Total Amount', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Outstanding', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Status', 'mythic-fcp' ) . '</th>';
    $output.='            <th scope="col">' . esc_html__( 'Action', 'mythic-fcp' ) . '</th>';
    $output.='        </tr>';
    $output.='    </thead>';

    foreach($invoices as $invoice) {
        $amount = $invoice["amount"];
        $autobill = $invoice["auto_bill"];
        $autobill_status = $invoice["autobill_status"];
        $created_at = $invoice["create_date"];
        $date_paid = $invoice["date_paid"];
        $deposit_amount = $invoice["deposit_amount"];
        $deposit_percent = $invoice["deposit_percentage"];
        $deposit_status = $invoice["deposit_status"];
        $discount_description = $invoice["discount_description"];
        $discount_total = $invoice["discount_total"];
        $display_status = $invoice["display_status"];
        $dispute_status = $invoice["dispute_status"];
        $due_date = $invoice["due_date"];
        $fulfillment_date = $invoice["fulfillment_date"];
        $id = $invoice["id"];
        $invoice_number = $invoice["invoice_number"];
        $last_order_status = $invoice["last_order_status"];
        $outstanding = $invoice["outstanding"];
        $paid = $invoice["paid"];
        $payment_details = $invoice["payment_details"];
        $payment_status = $invoice["payment_status"];
        $status = $invoice["status"];
        $updated = $invoice["updated"];
        $deleted = $invoice["vis_state"];

        $current_date = date('Y-m-d');

        // Get Sharable Link   
        $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices/" . $id . "/share_link?share_method=share_link";
        $api_args = array(
            'method' => 'GET',
            'timeout' => 30.000,
            'redirection' => 10,
            'headers' => array(
                "Api-Version" => "alpha",
                "Authorization" => "Bearer " . $bearer_token,
                "Content-Type" => "application/json"
            ),


        );
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json = json_decode($response, true);
        
        $link = $json["response"]["result"]["share_link"]["share_link"];
        
            $output.='<tr>';
            $output.='    <td data-label="' . esc_html__( 'Invoice Number', 'mythic-fcp' ) . '"><a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $invoice_number ) . '</a></td>';
            $output.='    <td data-label="' . esc_html__( 'Date Created', 'mythic-fcp' ) . '">' . esc_html( $created_at ) . '</td>';
            $output.='    <td data-label="' . esc_html__( 'Due Date', 'mythic-fcp' ) . '">' . esc_html( $due_date ) . '</td>';
            $output.='    <td data-label="' . esc_html__( 'Total Amount', 'mythic-fcp' ) . '">$' . esc_html( $amount["amount"] ) . '</td>'; 
            $output.='    <td data-label="' . esc_html__( 'Outstanding', 'mythic-fcp' ) . '">$' . esc_html( $outstanding["amount"]) . '</td>'; 
            $output.='    <td data-label="' . esc_html__( 'Status', 'mythic-fcp' ) . '">';
        
                    switch($status) {
                        case 0;
                            $output .='<a class="status disputed" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Disputed', 'mythic-fcp' ) . '</a>';
                            break;
                        case 1;
                            $output .='<a class="status draft" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Draft', 'mythic-fcp' ) . '</a>';
                            break;
                        case 2;
                            $output .='<a class="status sent" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Sent', 'mythic-fcp' ) . '</a>';
                            break;
                        case 3;

                            if($due_date < $current_date) {
                                $output .='<a class="status overdue" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Overdue', 'mythic-fcp' ) . '</a>';
                            } else {
                                $output .='<a class="status viewed" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Viewed', 'mythic-fcp' ) . '</a>';
                            }

                            break;
                        case 4;
                            $output .='<a class="status paid" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Paid', 'mythic-fcp' ) . '</a>';
                            break;
                        case 5;
                            $output .='<a class="status autopaid" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Auto Paid', 'mythic-fcp' ) . '</a>';
                            break;
                        case 6;
                            $output .='<a class="status retry" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Retry', 'mythic-fcp' ) . '</a>';
                            break;
                        case 7;
                            $output .='<a class="status failed" href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Failed', 'mythic-fcp' ) . '</a>';
                            break;
                        case 8;
                            $output .='<a class="status partial" href="' . esc_url( $link ) . '" target="_blank"> ' . esc_html__( 'Partial', 'mythic-fcp' ) . '</a>';
                            break;
                    }

                $output .='</td>';
                
                $output .='<td data-label="' . esc_html__( 'Action', 'mythic-fcp' ) . '">';
                $output .='     <a class="action" title="' . esc_html__( 'View Invoice', 'mythic-fcp' ) . '" href="' . esc_url( $link ) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'assets/svg/search.svg"></a>';
                	
                    if($status == 2 || $status == 3 || $status == 6 || $status == 7 || $status == 8) {
                        $output .= '<a class="action" title="' . esc_html__( 'Pay Invoice', 'mythic-fcp' ) . '" href="' . esc_url( $link ) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'assets/svg/dollar.svg"></a>';
                    }
                $output.= '</td>';
            $output.= '</tr>';
    }                          

    $output.='</table>';

    // Pagination
    $output.='<div class="fcp_pagination">';
    
    // Set Current Page
    if(isset($_GET['fcp_page'])) {
        $current_page = sanitize_text_field( $_GET['fcp_page'] );
    } else {
        $current_page = 1;
    }

    // See if There's a page before or after

    $prev_page = $current_page - 1;
    $next_page = $current_page + 1;

    // Prev Page
    if($current_page > 1) {

        // WP_Http Request  
        $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices?per_page=10&page=" . esc_html( $prev_page ) . "&search[customerid]=" . esc_html( $fcp_client_id );
        $api_args = array(
            'method' => 'GET',
            'timeout' => 30.000,
            'redirection' => 10,
            'headers' => array(
                "Api-Version" => "alpha",
                "Authorization" => "Bearer " . esc_html( $bearer_token ),
                "Content-Type" => "application/json"
            ),


        );
        $request = new WP_Http_Curl;
        $result = $request->request( $api_url, $api_args );
        $response = $result['body'];
        $json = json_decode($response, true);
        
        $invoices =  $json["response"]["result"]["invoices"];

        if(count($invoices) > 1) {
            // Add Prev Button
            $output.='<a href="' . add_query_arg('fcp_page', esc_html( $prev_page ) ) . '">&lt;' . esc_html__( 'Previous Page', 'mythic-fcp' ) . '</a>';
        }
    }

    // Next Page
    // WP_Http Request  
    $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices?per_page=10&page=" . esc_html( $next_page ) . "&search[customerid]=" . esc_html( $fcp_client_id );
    $api_args = array(
        'method' => 'GET',
        'timeout' => 30.000,
        'redirection' => 10,
        'headers' => array(
            "Api-Version" => "alpha",
            "Authorization" => "Bearer " . esc_html( $bearer_token ),
            "Content-Type" => "application/json"
        ),


    );
    $request = new WP_Http_Curl;
    $result = $request->request( $api_url, $api_args );
    $response = $result['body'];
    $json = json_decode($response, true);

    $invoices =  $json["response"]["result"]["invoices"];

        if(count($invoices) > 1) {
            // Add Next Button
            $output.='<a href="' . add_query_arg('fcp_page', esc_html( $next_page ) ) . '">' . esc_html__( 'Next Page', 'mythic-fcp' ) . '&gt;</a>';
        } else {
            $output.='<a href="!#" class="disabled">' . esc_html__( 'Next Page', 'mythic-fcp' ) . '&gt;</a>';
        }


    $output.= '</div>';
    
    return $output;

}