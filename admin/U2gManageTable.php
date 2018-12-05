<?php
/**
 * Defining Unbounce Page Table List.
 * ============================================================================
 *
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/* ManageTable class that will display our 'Unbounce Page' table. */
class U2gManageTable extends WP_List_Table
{
    /* Some basic params. */
    function __construct()
    {
        parent::__construct(array(
            'singular' => 'ugform',
            'plural' => 'ugforms',
        ));
    }

    /* This is a default column renderer. */
    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    /* This is a 'page status' column renderer. */
    function column_u2gfstatus($item) {
        $output = '';
        $page_status = '';
        $u2g_form_id = $item['u2g-page-id'];
        if ($item['page-status'] == 1) {
            $output .= '<div class="onoffswitch">
                            <input onchange="passPageData('.$u2g_form_id.')" type="checkbox" name="onoffswitch" value="1" class="onoffswitch-checkbox" id="myonoffswitch-'.$u2g_form_id.'" checked data-value="'.$u2g_form_id.'">
                            <label class="onoffswitch-label" for="myonoffswitch-'.$u2g_form_id.'">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>';
        } else {
            $output .= '<div class="onoffswitch">
                            <input onchange="passPageData('.$u2g_form_id.')" type="checkbox" name="onoffswitch" value="0" class="onoffswitch-checkbox" id="myonoffswitch-'.$u2g_form_id.'" data-value="'.$u2g_form_id.'">
                            <label class="onoffswitch-label" for="myonoffswitch-'.$u2g_form_id.'">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>';
        }
        return $output;
    }

    /* This is a 'page name' column renderer */
    function column_u2gform_name($item) {
        // links going to /admin.php?page=gf_entries&id={id}
        $gf_form_id = $item['form-gf-id'];
        $actions = array(
            'view' => sprintf('<a href="admin.php?page=gf_entries&id=%s">%s</a>', $gf_form_id, __('View Entries', 'u2g_inte'))
        );
        return sprintf('%s %s',
            $item['page-name'],
            $this->row_actions($actions)
        );
    }

    /* Show all columns. */
    function get_columns() {
        $columns = array(
            'u2gform_name' => __('Page Name', 'u2g_inte'),
            'u2gfstatus' => __('Status', 'u2g_inte')
        );
        return $columns;
    }

    /* Sort any columns */
    function get_sortable_columns() {
        $sortable_columns = array(
            'u2gform_name' => array('u2gform_name', true),
            'u2gfstatus'=>array('u2gfstatus', true)
        );
        return $sortable_columns;
    }
    /* Get page name with status and prepare them to be showed in 'forms List'. */
    function prepare_items() {
        global $table_prefix, $wpdb;
        $table_name = $wpdb->prefix . 'u2gform_data';
        $per_page = $this->get_items_per_page('tables_per_page', 10); // Set Page records for pagination.

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /* Configure table headers, Disply columns with sorting. */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        /* Get total record that will be used in pagination settings. */
        $total_items = $wpdb->get_var("SELECT COUNT(u2gform_id) FROM $table_name");

        // Prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'u2gform_name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        
        /* Get page name, status and other records from table. */
        $db_forms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        $this->items = get_tbl_items($db_forms);

        /* Configure Pagination */
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

/* Fetch only pages/forms that are available in 'Gravity Form' and 'Unbounce Page'. */
function get_tbl_items($db_forms) {
    $page_items = array();
    foreach ($db_forms as $each_form) {
        $u2g_form_id = $each_form['u2gform_id'];
        $formname = $each_form['u2gform_name'];
        $formstatus = $each_form['u2gform_status'];
        if (RGFormsModel::get_form_id($formname)) {
            $gf_form_id = RGFormsModel::get_form_id($formname);
            $form_items = array(
                    'page-name' => $formname,
                    'page-status' => $formstatus,
                    'form-gf-id' => $gf_form_id,
                    'u2g-page-id' => $u2g_form_id
            );
            array_push($page_items, $form_items);
        }
    }
    return $page_items;
}