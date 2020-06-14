<?php
/**
 * Plugin Name: Freshbooks Client Portal
 * Description: Provides client portal functionality for Freshbooks account holders.
 * Version: 1.0.0
 * Author: Mythic Design Company
 * Author URI: https://mythicdesigncompany.com/
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Activate Functions
register_activation_hook( __FILE__, 'mythic_fcp_activation' );
function mythic_fcp_activation() {

}

// Deactivate Functions
register_deactivation_hook( __FILE__, 'mythic_fcp_activation' );
function mythic_fcp_deactivation() {

}

// Uninstall Functions
register_uninstall_hook( __FILE__, 'mythic_fcp_uninstall' );
function mythic_fcp_uninstall() {

}

// Register Plugin Settings
add_action( 'admin_init', 'mythic_fcp_register_settings' );
function mythic_fcp_register_settings() {
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
    ?>
    <h1>Freshbooks Client Portal Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mythic_fcp_token_options' ); ?>
        <h3>Step 1) Application Settings</h3>
        <table>
            <tr valign="middle">
                <th scope="row" style="text-align:left;"><label for="mythic_fcp_client_id">Client ID</label></th>
                <td><input type="text" id="mythic_fcp_client_id" name="mythic_fcp_client_id" value="<?php echo get_option('mythic_fcp_client_id'); ?>" /></td>
            </tr>
            <tr valign="middle">
                <th scope="row" style="text-align:left;"><label for="mythic_fcp_client_secret">Client Secret</label></th>
                <td><input type="text" id="mythic_fcp_client_secret" name="mythic_fcp_client_secret" value="<?php echo get_option('mythic_fcp_client_secret'); ?>" /></td>
            </tr>
        </table>
        <?php  submit_button(); ?>
    </form>
    
    <h3>Step 2) Freshbooks Authorization</h3>    
    <?php
        if(get_option('mythic_fcp_bearer_token') && get_option('mythic_fcp_refresh_token') && get_option('mythic_fcp_token_expiry')) {
            echo "<p><strong>Authentication Status: </strong>Connected <span class='dashicons dashicons-yes-alt'></span></p>";
        } else {
            echo "<p><strong>Authentication Status: </strong>Not Connected <span class='dashicons dashicons-dismiss'></span></p>";
        }
    ?>
    
    <a href="https://auth.freshbooks.com/service/auth/oauth/authorize?client_id=<?php echo get_option('mythic_fcp_client_id'); ?>&response_type=code&redirect_uri=<?php echo get_admin_url(null, 'options-general.php', 'https'); ?>&state=mythic_fcp_auth" class="button button-primary">Connect with Freshbooks</a>
    
    <h3>Step 3) Business Identity Information</h3>
    <form method="post" action="options.php">
        <?php settings_fields( 'mythic_fcp_identity_info' ); ?>
        <table>
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
                <th scope="row" style="text-align:left;"><label for="mythic_fcp_account_id">Select Your Account</label></th>
                <td><select name="mythic_fcp_account_id" id="mythic_fcp_account_id">
                    <?php
                        foreach($business_memberships as $business) {
                            echo( "<option value='" . $business['business']['account_id'] . "'>" . $business['business']['name'] . "</option>" );
                        }
                    ?>
                </select></td>
            </tr>
            <tr valign="middle">
                <th scope="row" style="text-align:left;"><label for="mythic_fcp_business_id">Select Your Business</label></th>
                <td><select name="mythic_fcp_business_id" id="mythic_fcp_business_id">
                    <?php
                        foreach($business_memberships as $business) {
                            echo( "<option value='" . $business['business']['id'] . "'>" . $business['business']['name'] . "</option>" );
                        }
                    ?>
                </select></td>
            </tr> 
        </table>
        <?php  submit_button(); ?>
    </form>
    
    <?php
    
}

// Authorize With Freshbooks
add_action( 'admin_init', 'mythic_fcp_authorize_app');
function mythic_fcp_authorize_app() {
    if( isset($_GET['code']) && $_GET['state'] == "mythic_fcp_auth" ) {

        // WP_Http Request
        $client_id = trim(get_option('mythic_fcp_client_id'));
        $client_secret = trim(get_option('mythic_fcp_client_secret'));
        $auth_code = $_GET['code'];
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
    
    // If Current Date is Past Expiration Date
    if($current_date_str < $expiry_date_str) {
        return true;
    } else {
        // Token is Expired, Refresh Token
        $client_id = trim(get_option('mythic_fcp_client_id'));
        $client_secret = trim(get_option('mythic_fcp_client_secret'));
        $refresh_token = trim(get_option('mythic_fcp_refresh_token'));
        $redirect_uri = get_admin_url(null, 'options-general.php', 'https');
        
        echo "Client ID: " . $client_id . "<br>";
        echo "Client Secret: " . $client_secret . "<br>";
        echo "Refresh Token: " . $refresh_token . "<br>";
        echo "Redirect URI: " . $redirect_uri . "<br>";
        
        // WP_Http Request
        $api_url = "https://api.freshbooks.com/auth/oauth/token";
        $api_body = '{
            "grant_type": "refresh_token",
            "client_secret": "' . $client_secret .'",
            "refresh_token": "' . $refresh_token . '",
            "client_id": "' .$client_id .'",
            "redirect_uri": "'. $redirect_uri . '"
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

        update_option('mythic_fcp_bearer_token', $bearer_token);
        update_option('mythic_fcp_refresh_token', $refresh_token);
        update_option('mythic_fcp_token_expiry', $expiry_date);

        // Tokens Updated!
        return true;

    }
}

// Find Client ID On User Login
add_action( 'wp_login', 'mythic_fcp_attach_client_id', 99, 2);
function mythic_fcp_attach_client_id($login, $user) {
    // Make Sure We Still Have Access to Freshbooks
    mythic_fcp_refresh_token();
    
    $bearer_token = get_option('mythic_fcp_bearer_token');
    
    // WP_Http Request        
    $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/users/clients?per_page=100";
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
    // Make Sure We Still Have Access to Freshbooks
    mythic_fcp_refresh_token();
    
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
                
        mythic_fcp_render_client_info($json);

    } else {
        echo "<p>No Freshbooks data has been found for your account.</p>";
    }
}

// Register Frontend CSS
add_action( 'wp_enqueue_scripts', 'mythic_fcp_register_style' );
function mythic_fcp_register_style() {
    wp_register_style( 'mythic_fcp_frontend_css', plugins_url( 'css/style.css', __FILE__), array(), '1.0.0', 'all');
}

// Render Client Info
function mythic_fcp_render_client_info($json) {
    $bearer_token = get_option('mythic_fcp_bearer_token');
    
    wp_enqueue_style('mythic_fcp_frontend_css');
    
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
    
    ?>
    
    <div class="fcp_container">
        <div class="fcp_header">
            <div class="welcome"><h1>Hello<?php if($client_fname) { echo " " . $client_fname; } ?>!</h1></div>
            <div class="balance">
                <p><strong>Total Outstanding: </strong>$
                <?php
                    if($client_outstanding_balance) { 
                        echo($client_outstanding_balance[0]["amount"]["amount"] . " " . $client_outstanding_balance[0]["amount"]["code"]);
                    } else {
                        echo "$0.00";
                    }
                ?></p>
            </div>
        </div>
        <div class="fcp_body">
            <?php  mythic_fcp_render_client_invoices(); ?>
        </div>
    </div>
    
    <?php
}

// Render Client Invoices List
function mythic_fcp_render_client_invoices() {
    $user_id = get_current_user_id();    
    $fcp_client_id = get_user_meta($user_id, 'mythic_fcp_client_id', true);
    $bearer_token = get_option('mythic_fcp_bearer_token');

    $page = "";
    
    if(isset($_GET['fcp_page'])) {
        $p = $_GET['fcp_page'];
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
        
    ?>

    <table>
        <thead>
            <tr>
                <th scope="col">Invoice Number</th>
                <th scope="col">Date Created</th>
                <th scope="col">Due Date</th>
                <th scope="col">Total Amount</th>
                <th scope="col">Outstanding</th>
                <th scope="col">Status</th>
            </tr>
        </thead>

    <?php
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
        
        ?>
            <tr>
                <td data-label="Invoice Number"><?php echo $invoice_number; ?></td>
                <td data-label="Date Created"><?php echo $created_at; ?></td>
                <td data-label="Due Date"><?php echo $due_date; ?></td>
                <td data-label="Total Amount">$<?php echo $amount["amount"]; ?></td>
                <td data-label="Outstanding">$<?php echo $outstanding["amount"]; ?></td>
                <td data-label="Status"><?php 
                    switch($status) {
                        case 0;
                            ?><a class="status disputed" href="<?php echo $link; ?>" target="_blank">Disputed</a><?php
                            break;
                        case 1;
                            ?><a class="status draft" href="<?php echo $link; ?>" target="_blank">Draft</a><?php
                            break;
                        case 2;
                            ?><a class="status sent" href="<?php echo $link; ?>" target="_blank">Sent</a><?php
                            break;
                        case 3;

                            if($due_date < $current_date) {
                                ?><a class="status overdue" href="<?php echo $link; ?>" target="_blank">Overdue</a><?php
                            } else {
                                ?><a class="status viewed" href="<?php echo $link; ?>" target="_blank">Viewed</a><?php
                            }

                            break;
                        case 4;
                            ?><a class="status paid" href="<?php echo $link; ?>" target="_blank">Paid</a><?php
                            break;
                        case 5;
                            ?><a class="status autopaid" href="<?php echo $link; ?>" target="_blank">Auto Paid</a><?php
                            break;
                        case 6;
                            ?><a class="status retry" href="<?php echo $link; ?>" target="_blank">Retry</a><?php
                            break;
                        case 7;
                            ?><a class="status failed" href="<?php echo $link; ?>" target="_blank">Failed</a><?php
                            break;
                        case 8;
                            ?><a class="status partial" href="<?php echo $link; ?>"> target="_blank"Partial</a><?php
                            break;
                    }

                ?></td>
            </tr>
        <?php
    }                          
     ?>

    </table>

    <?php

    // Pagination
    ?><div class="fcp_pagination"><?php

    // Set Current Page
    if(isset($_GET['fcp_page'])) {
        $current_page = $_GET['fcp_page'];
    } else {
        $current_page = 1;
    }

    // See if There's a page before or after

    $prev_page = $current_page - 1;
    $next_page = $current_page + 1;

    // Prev Page
    if($current_page > 1) {

        // WP_Http Request  
        $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices?per_page=10&page=" . $prev_page . "&search[customerid]=" . $fcp_client_id;
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

        if(count($invoices) > 1) {
            // Add Prev Button
            $current_url = explode("?", $_SERVER['REQUEST_URI']);
            ?><a href="<?php echo($current_url[0] . "?fcp_page=" . $prev_page); ?>">&lt; Previous Page</a> | <?php
        }
    }

    // Next Page
    // WP_Http Request  
    $api_url = "https://api.freshbooks.com/accounting/account/" . get_option('mythic_fcp_account_id') . "/invoices/invoices?per_page=10&page=" . $next_page . "&search[customerid]=" . $fcp_client_id;
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

        if(count($invoices) > 1) {
            // Add Next Button
            $current_url = explode("?", $_SERVER['REQUEST_URI']);
            ?><a href="<?php echo($current_url[0] . "?fcp_page=" . $next_page); ?>">Next Page &gt;</a><?php
        } else {
            ?><a href="!#" class="disabled">Next Page &gt;</a><?php
        }


    ?></div><?php

}