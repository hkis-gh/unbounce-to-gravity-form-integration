<?php
/*
 * Fetch and store Unbounce leads to Gravity form while submission on Unbounce landing page.
 */

/* Receive Data from Unbounce Webhook */

require $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $ub_postdata;
$error_short_text = "";
$error_content = "";

$ub_postdata = json_decode(stripslashes($_POST['data_json']), true);

if ($ub_postdata) {
    global $table_prefix, $wpdb, $ub_page_name, $wp_u2gform_data;
    $wp_u2gform_data = $table_prefix . "u2gform_data";
    $ub_page_name = $wpdb->_real_escape($_POST['page_name']); // Unbounce page name

    // Check if the page already exists?
    $sql = "SELECT u2gform_status FROM $wp_u2gform_data WHERE u2gform_name='$ub_page_name'";
    $db_page_status = $wpdb->get_var($sql);

    if (isset($db_page_status)) {
        if (1 == $db_page_status) {
            u2g_insertLeads();
        } else {
            $status_short_text = "inactive-form";
            $status_msg = 'The form is currently Inactive!';
            echo $status_msg;
            create_error_log($status_short_text, $status_msg);
        }

    } else {
        $wpdb->query("INSERT INTO $wp_u2gform_data (`u2gform_name`) VALUES ('$ub_page_name')");
        u2g_insertLeads();
    }
} else {
    wp_redirect(home_url());
    exit();
}

// Insert leads data into Gravity form database.
function u2g_insertLeads() {
    global $ub_page_name;

    // Remove backslash from Unbounce page name
    if (preg_match('/\\\\/', $ub_page_name)) {
        $ub_page_name = str_replace('\\', '', $ub_page_name);
    }

    // Find Form from GF
    $gf_id = RGFormsModel::get_form_id($ub_page_name);
    if ($gf_id == 0) {
        $status_short_text = "form-not-found";
        $status_msg = 'Form not found in gravity form!';
        echo $status_msg;
        create_error_log($status_short_text, $status_msg);
        return false;
    }

    $gf_fields = u2g_getGfFields($gf_id);
    if ($gf_fields == 0) {
        $status_short_text = "fields-not-found";
        $status_msg = 'Fields are not found.';
        echo $status_msg;
        create_error_log($status_short_text, $status_msg);
        return false;
    } else {
        $get_ub_fields = u2g_getUbFields();
        $ub_field_names = array_keys($get_ub_fields);

        $total_gf_fields = count($gf_fields);
        $total_ub_fields = count($get_ub_fields);

        if ($total_gf_fields == $total_ub_fields) {
            $gf_um_fields = array(); // gravity forms [u]n[m]atched fields.
            $entry['form_id'] = $gf_id;
            foreach ($gf_fields as $field_id => $field_name) {
                $lower_field_name = strtolower($field_name);
                $no_special_char_field_name = preg_replace('/[^a-zA-Z0-9\s]/', "", $lower_field_name);
                $gf_field_name = preg_replace('/\s+/', '_', $no_special_char_field_name);

                if (in_array($gf_field_name, $ub_field_names)) {
                    $entry[$field_id] = $get_ub_fields[$gf_field_name];
                } else {
                    $gf_um_fields[] = $gf_field_name;
                }
            }

            // Display message as in response when any UB field name doesn't match with respective GF field name.
            if ($gf_um_fields) {
                $status_short_text = "field-mismatch";
                $status_msg = implode(', ', $gf_um_fields) . " doesn't match with their field name(s) in Unbounce.";
                echo $status_msg;
                create_error_log($status_short_text, $status_msg);
                return false;
            }

            GFAPI::add_entry($entry);
            echo 'Entries inserted!!';
            http_response_code(200);
        } else {
            $status_short_text = "field-difference";
            $status_msg = 'Total no. of fields are different in the Unbounce page! Please check fields with Gravity Forms.';
            echo $status_msg;
            create_error_log($status_short_text, $status_msg);
            return false;
        }
    }
}

/* Get all fields for specific Gravity Form. */
function u2g_getGfFields($gf_id) {
    $gf_fields = GFAPI::get_form($gf_id);
    $gf_form_fields = $gf_fields['fields'];
    $count_gf_fields = count($gf_form_fields);

    if ($count_gf_fields != 0) {
        foreach ($gf_form_fields as $form_field) {
            $all_gf_fields[$form_field['id']] = $form_field['label'];
        }
        return $all_gf_fields;
    }
    return $count_gf_fields;
}

/* Get all fields for specific Unbounce Page. */
function u2g_getUbFields() {
    global $ub_postdata;

    $unwanted_fields = [
        'page_uuid',
        'date_submitted',
        'page_name',
        'ip_address',
        'page_url',
        'variant',
        'time_submitted'
    ];

    foreach ($ub_postdata as $key => $value) {
        if (!in_array($key, $unwanted_fields)) {
            $ub_fields[$key] = $value[0];
        }
    }
    return $ub_fields;
}

/* Write error info into plugin log file. */
function create_error_log($error_short_text, $error_content) {
    $plugin_dir = plugin_dir_path(__FILE__);
    $error_date = date("M,d,Y h:i:s A");
    $error = $error_date . " : " . $error_short_text . " - " . $error_content . "\n";
    error_log($error, 3, $plugin_dir . "/u2gf.log");
}
?>