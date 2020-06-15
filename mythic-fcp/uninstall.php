<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Unregister Plugin Settings
function mythic_fcp_unregister_settings() {
    delete_option( 'mythic_fcp_client_id');
    unregister_setting( 'mythic_fcp_token_options', 'mythic_fcp_client_id' );

    delete_option( 'mythic_fcp_client_secret');
    unregister_setting( 'mythic_fcp_token_options', 'mythic_fcp_client_secret' );
    
    delete_option( 'mythic_fcp_bearer_token');
    unregister_setting( 'mythic_fcp_token_options', 'mythic_fcp_bearer_token' );
    
    delete_option( 'mythic_fcp_refresh_token');
    unregister_setting( 'mythic_fcp_token_options', 'mythic_fcp_refresh_token' );
    
    delete_option( 'mythic_fcp_token_expiry');
    unregister_setting( 'mythic_fcp_token_options', 'mythic_fcp_token_expiry' );
    
    delete_option( 'mythic_fcp_account_id');
    unregister_setting( 'mythic_fcp_identity_info', 'mythic_fcp_account_id' );

    delete_option( 'mythic_fcp_business_id');
    unregister_setting( 'mythic_fcp_identity_info', 'mythic_fcp_business_id' );
    
    delete_option( 'mythic_fcp_remove_on_uninstall' );
    unregister_setting( 'mythic_fcp_options', 'mythic_fcp_remove_on_uninstall' );
}

// Check if user wants to delete data
if(get_option('mythic_fcp_remove_on_uninstall') == 1) {
    mythic_fcp_unregister_settings();
}

?>