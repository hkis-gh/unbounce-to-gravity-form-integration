<?php
    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }    
    global $wpdb,$table_prefix;
    $wp_u2gformdata = $table_prefix . "u2gform_data";
    
    /* Drop the table from the database. */
    $wpdb->query( "DROP TABLE IF EXISTS $wp_u2gformdata" );
    
    /* Delete the database version. */
    delete_option( "u2g_db_version" );

    /* Delete options which are stored in option table */
    delete_option( 'u2g_integration_options' );

    /* Remove menu WP back-end. */
    function u2g_integration_remove_options_page()
    {
        remove_menu_page( 'u2g_integration' );
    }
    add_action( 'admin_menu', 'u2g_integration_remove_options_page', 99 );
?>