<?php
/*
* Core function to redirect to 'lead-unbounce-to-gravity' page template.
*/
add_filter('template_include', 'u2g_PluginTemplateInclude', 1, 1);
function u2g_PluginTemplateInclude($template)
{
    global $wp;
    /* Check for query var 'name' to fetch 'lead-unbounce-to-gravity' from query string. */
    $query_vars_name = add_query_arg( array(), $wp->request );
    
    /* Verify 'lead-unbounce-to-gravity' exists and value is 'true'. */
    if ($query_vars_name && $query_vars_name == "lead-unbounce-to-gravity") {
        return plugin_dir_path( __DIR__ ) . 'lead-unbounce-to-gravity/index.php';
    }
    return $template;
}
?>