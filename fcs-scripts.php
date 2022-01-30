<?php
function fcs_adding_scripts()
{
    $pluginPath = WP_PLUGIN_DIR . '/fc-stripe';
    //wp_enqueue_script('fc_stripe_donation', $pluginPath . '/js/donation.js');
    wp_enqueue_style('fc-grid-css', plugins_url('/css/fcs-grid.css', __FILE__));
    wp_enqueue_script('fc-stripe-donation-js', plugins_url('/js/donation.js', __FILE__));
    $scriptData = array(
        'test_key' => get_option('fcs_test_key_publishable') ?? '',
        'prod_key' => get_option('fcs_prod_key_publishable') ?? '',
        'success_slug' => get_option('fcs_success_slug') ?? '',
        'test_mode' => get_option('fcs_test_mode') ?? 'true'
    );
    wp_localize_script('fc-stripe-donation-js', 'my_options', $scriptData);
}
add_action('wp_enqueue_scripts', 'fcs_adding_scripts');