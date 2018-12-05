<?php
/**
 * Plugin Setting page and create admin menu.
 */
/* Plugin option and settings */
function u2g_integration_settings_init() {
    register_setting('u2g_integration', 'u2g_integration_options');
    add_settings_section(
        'u2g_integration_section_developers',
        __('', 'u2g_integration'),
        'u2g_integration_section_developers_cb',
        'u2g_integration'
    );
}
add_action('admin_init', 'u2g_integration_settings_init');
/* Developers section call back to crete html design. */
function u2g_integration_section_developers_cb($args) {
    u2g_IntegrationAdminViewPage($args);
}
/* 'u2g Integration' menu. */
function u2g_integration_options_page() {
    add_menu_page(
        'U2G Integration',
        'U2G Integration',
        'manage_options',
        'u2g_integration',
        'u2g_integration_options_page_html',
        plugins_url('images/u2gf-icon-30.png',__FILE__)
    );
}
add_action('admin_menu', 'u2g_integration_options_page');
/* Call back function for u2g Integration' menu. */
function u2g_integration_options_page_html() {
    /* check user capabilities */
    if (!current_user_can('manage_options')) {
        return;
    }
    /* Add error/update messages. */
    if (isset($_GET['settings-updated'])) {
        add_settings_error('u2g_integration_messages', 'u2g_integration_message', __('Settings Saved', 'u2g_integration'), 'updated');
    }
    /* show error/update messages */
    settings_errors('u2g_integration_messages');
    ?>
    <div class="wrap container">
        <h1>Unbounce Leads Migration</h1>
        <form action="options.php" method="post">
            <?php
            /* output fields */
            settings_fields('u2g_integration');
            /* output setting sections and their fields. */
            do_settings_sections('u2g_integration');
            ?>
        </form>
    </div>
    <?php
}
/* Load CSS and JS in WP back end (WP admin). */
add_action( 'admin_init', 'load_scripts' );
function load_scripts(){
    add_action('admin_enqueue_scripts', 'callback_to_load_scripts');
}
function callback_to_load_scripts() {
    wp_register_style( 'admin-cust', plugins_url( '/css/admin_css.css', __FILE__ ), 'all' );
    wp_enqueue_style( 'admin-cust' );
    wp_enqueue_script('admin-custom-js',plugins_url( '/js/admin_switch.js', __FILE__ ), array('jquery'), false,false);
}
?>