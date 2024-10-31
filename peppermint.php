<?php
/**
 * Plugin Name:       Peppermint
 * Description:       Peppermint chatbot for websites
 * Version:           1.0.0
 * Author:            Peppermint.com
 * Author URI:        https://peppermint.com
 * Text Domain:       peppermint.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function peppermint_plugin_option_page()
{
    ?>
    <div class="wrap">
        <form id="peppermint-admin-form" method="post" action="options.php">
            <?php
            settings_fields('peppermint_settings_group');
            do_settings_sections('peppermint_plugin_option_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function peppermint_render_menu()
{
    add_options_page('Peppermint', 'Peppermint', 'manage_options', 'peppermint', 'peppermint_plugin_option_page');
}


function peppermint_print_section_info()
{
    print 'If you do not have peppermint account please <a target="_blank" href="https://peppermint.com/app/signup">signup here</a> to get widget id';
}

function peppermint_id_widget_callback()
{
    $options = get_option('peppermint_settings_widget_id');
    printf(
        '<input type="text" id="widget_id" name="peppermint_settings_widget_id[widget_id]" value="%s" />',
        isset($options['widget_id']) ? esc_attr($options['widget_id']) : ''
    );
}

function peppermint_page_init()
{
    register_setting(
        'peppermint_settings_group', // Option group
        'peppermint_settings_widget_id' // Option name
    );

    add_settings_section(
        'peppermint_settings', // ID
        'Peppermint Settings', // Title
        'peppermint_print_section_info', // Callback
        'peppermint_plugin_option_page' // Page
    );

    add_settings_field(
        'widget_id', // ID
        'Widget ID', // Title
        'peppermint_id_widget_callback', // Callback
        'peppermint_plugin_option_page', // Page
        'peppermint_settings' // Section
    );
}

function peppermint_add_scripts()
{
    $options = get_option('peppermint_settings_widget_id');
    if (isset($options['widget_id'])) {
        wp_enqueue_script('peppermint-widget-js', 'https://peppermint.com/api/widget.js?id=' . $options['widget_id'], array(), 1.0);
    }
}

function peppermint_add_async_attribute($tag, $handle)
{
    if ('peppermint-widget-js' !== $handle) {
        return $tag;
    }

    return str_replace(' src', ' defer="defer" src', $tag);
}

if (is_admin()) {
    add_action('admin_menu', 'peppermint_render_menu');
    add_action('admin_init', 'peppermint_page_init');
} else {
    add_action('wp_enqueue_scripts', 'peppermint_add_scripts');
    add_filter('script_loader_tag', 'peppermint_add_async_attribute', 10, 2);
}
?>
