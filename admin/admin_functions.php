<?php
/**
 *  Ajax call back to update status based on received page id from table.
 */

add_action( 'wp_ajax_set_page_status', 'u2g_setPageStatus' );

function u2g_setPageStatus() {
    global $table_prefix, $wpdb;
    $table_name = $wpdb->prefix . 'u2gform_data'; // do not forget about tables prefix
    $db_formid = intval( $_POST['page_id'] );

    // Find existing page status.
    $sql = "SELECT u2gform_status FROM $table_name where u2gform_id=$db_formid";
    $page_oldstatus = intval( $wpdb->get_var($sql) ); // Get existing status.
    
    if (isset($page_oldstatus)) {
        $page_newstatus = ($page_oldstatus == 1) ? 0 : 1;
    }

    // Update Page status.
    $update_action = $wpdb->update($table_name, array('u2gform_status' => $page_newstatus), array('u2gform_id' => $db_formid));
    /* Is page status updated? */
    if (false === $update_action) {
        echo "Status is not updated.";
    } else {
        echo "Page status set to ".$page_newstatus." successfully!!";
    }
    wp_die(); // this is required to terminate immediately and return a status response.
}
?>