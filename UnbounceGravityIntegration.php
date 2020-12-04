<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name:  Unbounce Gravity Forms
Plugin URI:   https://wordpress.org/plugins/hkinfosoft-unbounce-to-gravity-form-integration/
Description:  Fetch leads from Unbounce landing pages to Gravity forms. U2GF Pro Version is also available.
Version:      1.6
Author:       HK Infosoft
Author URI:   https://hkinfosoft.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
/*
Unbounce To Gravity Form Integration plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
Unbounce To Gravity Form Integration plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with Unbounce Gravity Form Integration Plugin. If not, see {URI to Plugin License}.
*/
if ( is_admin() ) {
    // we are in admin mode
    require_once( dirname( __FILE__ ) . '/admin/settings_option_page.php' );
    require_once( dirname( __FILE__ ) . '/admin/admin_page_view.php' );
    require_once( dirname( __FILE__ ) . '/admin/U2gManageTable.php' );
    require_once( dirname( __FILE__ ) . '/admin/admin_functions.php' );
}
require_once( dirname( __FILE__ ) . '/includes/core_functions.php' );

global $u2g_db_version, $gf_plugin_path;
$u2g_db_version = '1.0';
$u2g_plugin_version = '1.6';
$gf_plugin_path = plugin_dir_path( __DIR__ );

add_option('u2g_plugin_version', $u2g_plugin_version);

/* Create table on activation. */
add_action('admin_init', 'u2g_integration_redirect');
function u2g_integration_activate() {
    if ( ! current_user_can( 'activate_plugins' ) )
        return;
    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    check_admin_referer( "activate-plugin_{$plugin}" );
    add_option('u2g_integration_redirect', true);
    createtbl_on_install();
    flush_rewrite_rules();
    manage_code_htaccess(true); // Add re-write rule on activation.
}

/* Add/Remove re-write rule into htaccess file. */
function manage_code_htaccess($status) {
    $htaccess_file = get_home_path() . '.htaccess';

    $rewrite_rule_content = "# BEGIN U2GF\n";
    $rewrite_rule_content .= "<IfModule mod_rewrite.c>\n";
    $rewrite_rule_content .= "RewriteRule ^lead-unbounce-to-gravity/$ /wp-content/plugins/hkinfosoft-unbounce-to-gravity-form-integration/lead-unbounce-to-gravity/index.php [QSA,L]"."\n";
    $rewrite_rule_content .="</IfModule>\n";
    $rewrite_rule_content .= "# END U2GF";

    $htaccess_content = file_get_contents($htaccess_file);

    if ($status) { // Add re-write rule.
        file_put_contents($htaccess_file, "\n\n".$rewrite_rule_content, FILE_APPEND);
    } else { // Remove re-write rule.
        $replaced_rule = preg_replace('/\n\n# BEGIN U2GF[\s\S]+?# END U2GF/', '', $htaccess_content);
        file_put_contents($htaccess_file, $replaced_rule);
    }
}

/* Remove re-write rule on de-activation. */
function remove_code_htaccess() {
    manage_code_htaccess(false);
}

/* Redirect to this plugin's main page. */
function u2g_integration_redirect() {
    if (get_option('u2g_integration_redirect', false)) {
        delete_option('u2g_integration_redirect');
        wp_redirect('admin.php?page=u2g_integration');
    }
}

/* Check plugin's dependancy. */
function u2g_plugin_dependancy_check_activation() {
    global $wp_version;
    $php = '5.3';
    $wp  = '4.7';
    if ( version_compare( PHP_VERSION, $php, '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
        wp_die(
            '<p>' .
            sprintf(
                __( 'This plugin can not be activated because it requires a PHP version greater than %1$s. Your PHP version can be updated by your hosting company.', 'my_plugin' ),
                $php
            )
            . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
        );
    }
    if ( version_compare( $wp_version, $wp, '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
        wp_die(
            '<p>' .
            sprintf(
                __( 'This plugin can not be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard &#9656; Updates to gran the latest version of WordPress .', 'my_plugin' ),
                $php
            )
            . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
        );
    }
}

/* Deactivate plugin and drop table that created by this plugin. */
function u2g_integration_plugin_deactivation() {
    if ( ! current_user_can( 'deactivate_plugins' ) )
        return;

    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    check_admin_referer( "deactivate-plugin_{$plugin}" );
    delete_option('rewrite_rules');
}

/* Function to create table on activation. */
function createtbl_on_install () {
    global $table_prefix, $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $wp_u2gformdata_table = $table_prefix . "u2gform_data";
    /* Table: ( u2gform_data ) */

    $sql = "CREATE TABLE IF NOT EXISTS $wp_u2gformdata_table (
         `u2gform_id` int(11) NOT NULL AUTO_INCREMENT,
         `u2gform_name` tinytext NOT NULL,
         `u2gform_status` tinyint(1) NOT NULL DEFAULT '1',
         `u2gform_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
         PRIMARY KEY (`u2gform_id`)
       ) $charset_collate;";
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

register_activation_hook( __FILE__, 'u2g_plugin_dependancy_check_activation' );
register_activation_hook(__FILE__, 'u2g_integration_activate');
register_deactivation_hook( __FILE__, 'u2g_integration_plugin_deactivation' );
register_deactivation_hook( __FILE__, 'remove_code_htaccess' );
?>