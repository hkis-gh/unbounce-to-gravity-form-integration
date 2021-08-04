<?php
/*
* Admin view of 'U2G Integration Plugin'. It will show immediately after plugin activation.
*
*/

/* Show plugin information and Webhook url to allow user to set on Unbounce page. */
function u2g_IntegrationAdminViewPage($args) {
    ?>
    <p>'U2GF Integration' will help you to sync the leads data from Unbounce to Gravity Form.</p>
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

function subscription_design(){
    
    if (!empty($_POST)) {
        $u2gf_recipient_emails = isset( $_POST['u2gf_recipient_emails'] ) ? trim($_POST['u2gf_recipient_emails']) : '';
        
        $to = 'support@hkinfosoft.com';
        $user = get_user_by( 'email', get_option('admin_email') );

        $user_name =  ($user->first_name !== '' && $user->last_name !== '') ? $user->first_name . ' ' . $user->last_name : $user->user_login;
        $domain = get_site_url();
        $subject = 'User subscribed from U2GF (Free)';
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: '.$user_name.' <'. get_option('admin_email') .'>',
            'Reply-To: ' . get_option('admin_email')
        );

        $message = "
            <h2>New user subscribed.</h2>
            <p>Hello HK Infosoft admin, </p>
            <p></p>
            <p>Mr/Ms {$user_name} {$u2gf_recipient_emails} has just subscribed from {$domain}</p>
            <p></p>
            <p>Thank you.</p>
        ";

        $isSend = wp_mail($to, $subject, $message, $headers);
        if ($isSend){
            echo '<div class="notice notice-success is-dismissible">
                    <p>Thank you for subscribing.</p>
                </div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible">
                    <p>Error: something went wrong! Please try again.</p>
                </div>';
        }
    }
    ?>
    <div class="subscription-box">
        <form class="subsc-form" method="POST" id="urgf_subs_form" enctype="multipart/form-data">
            <div class="container">
                <h2>Subscribe to our Newsletter</h2>
                <p>Receive plugin related latest updates.</p>
            </div>

            <div class="container">
                <input type="email" id="u2gf-recipient-emails" name="u2gf_recipient_emails" required>
            </div>

            <div class="container">
                <input type="submit" value="Subscribe">
            </div>
        </form>
    </div>
    
    <?php

}

function marketing_design() {
    ?>
    <div class="marketing_banner">
        <a class="pro_url" href="https://www.hkinfosoft.com/unbounce-to-gravity-form-integration/" target="_blank">
            <img src="<?php echo plugins_url('images/u2gf_pro_banner.png', __FILE__); ?>">
            <h2>Upgrade to PRO version of "Unbounce Gravity Forms" plugin.</h2>
        </a>
    </div>
    <?php
}
?>