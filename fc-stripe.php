<?php

/**
 * Plugin Name:       FC Stripe Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Just add the shortcode [fcs_stripe], and configure the API keys. I'll get a site up with docs eventually.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Christopher Brown
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */

require_once WP_PLUGIN_DIR . '/fc-stripe/fcs-settings.php';
require_once WP_PLUGIN_DIR . '/fc-stripe/fcs-endpoints.php';
require_once WP_PLUGIN_DIR . '/fc-stripe/fcs-shortcodes.php';
require_once WP_PLUGIN_DIR . '/fc-stripe/fcs-scripts.php';