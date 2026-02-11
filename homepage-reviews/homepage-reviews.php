<?php
/**
 * Plugin Name:       Homepage Reviews
 * Description:       A custom plugin to display reviews on the homepage with a customizable layout.
 * Version:           1.0.2
 * Author:            Sornavel Vikas
 * Author URI:        https://vikaszap.in
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       homepage-reviews
 * Domain Path:       /languages
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Tested up to:      6.9
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define('HOMEPAGE_REVIEWS_PATH', plugin_dir_path(__FILE__));
define('HOMEPAGE_REVIEWS_URL', plugin_dir_url(__FILE__));

// Include required files
require_once HOMEPAGE_REVIEWS_PATH . 'includes/class-reviews-cpt.php';
require_once HOMEPAGE_REVIEWS_PATH . 'includes/class-reviews-settings.php';
require_once HOMEPAGE_REVIEWS_PATH . 'includes/shortcodes.php';
require_once HOMEPAGE_REVIEWS_PATH . 'includes/class-reviews-demo.php';

// Initialize the plugin
function homepage_reviews_init()
{
    new Homepage_Reviews_CPT();
    new Homepage_Reviews_Settings();
    new Homepage_Reviews_Demo();
}
add_action('plugins_loaded', 'homepage_reviews_init');
