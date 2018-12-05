<?php
/*
* Admin view of 'U2G Integration Plugin'. It will show immediately after plugin activation.
*
*/

/* Show plugin information and Webhook url to allow user to set on Unbounce page. */
function u2g_IntegrationAdminViewPage($args) {
    ?>
    <p>'U2G Integration' will help you to sync the leads data from Unbounce to Gravity Form.</p>
    <p id="<?= esc_attr($args['id']); ?>"><strong>Copy below URL and set it as WebHook for Unbounce page(s)</strong></p>
    <p>WebHook URL: <a href="<?php echo site_url(); ?>/lead-unbounce-to-gravity/"
                       target="_blank"><?php echo site_url(); ?>/lead-unbounce-to-gravity/</a></p>
    <p>For more information visit <a
                href="https://documentation.unbounce.com/hc/en-us/articles/203510044-Using-a-Webhook" target="_blank">WebHook
            Documentation</a></p>
    <p>Here's <a href="https://www.hkinfosoft.com/unbounce-to-gravity-form-integration/" target="_blank">Pro Version</a>
        which help you to get rid of the manual form creation and management.</p>
    <?php
    /* Notice if Gravity form is either 'not installed' or 'inactive'. */
    if (is_plugin_active('gravityforms/gravityforms.php')) {
        $form_table = new U2gManageTable();
        $form_table->prepare_items();
        $form_table->display();
    }
    echo marketing_design();
}

function u2g_check_gf_plugin() {
    global $gf_plugin_path;
    $gf_plugin_path = $gf_plugin_path . "gravityforms/gravityforms.php";

    if (file_exists($gf_plugin_path)) {
        if (!is_plugin_active('gravityforms/gravityforms.php')) {
            add_action('admin_notices', 'u2g_plugin_not_active_notice');
        }
    } else {
        add_action('admin_notices', 'u2g_plugin_not_found_notice');
    }
}

add_action('admin_init', 'u2g_check_gf_plugin');

function u2g_plugin_not_active_notice() {
    echo "<div class='notice-warning notice'>
      <p>Warning: Gravity Form should be activated first in order to use 'U2GF' plugin. Please activate it.</p>
  </div>";
}

function u2g_plugin_not_found_notice() {
    echo "<div class='notice-warning notice'>
      <p>Warning: Gravity Form should be installed first in order to use 'U2GF' plugin. Please <a href='https://www.gravityforms.com/' target='_blank' >install</a> it.</p></p>
  </div>";
}

function marketing_design() {
    ?>
    <div class="marketing_banner" style="text-align: center;">
        <a class="pro_url" href="https://www.hkinfosoft.com/unbounce-to-gravity-form-integration/" target="_blank">
            <img src="<?php echo plugins_url('images/u2gf_pro_banner.png', __FILE__); ?>">
            <h2>Upgrade to PRO version of "Unbounce Gravity Forms" plugin.</h2>
        </a>
    </div>
    <?php
}

?>