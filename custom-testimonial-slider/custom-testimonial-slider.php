<?php
/**
 * Plugin Name: Custom Testimonial Slider
 * Description: A customizable testimonial slider with a 2x2 image grid layout.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: custom-testimonial-slider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants
define( 'CTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'CTS_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once CTS_PATH . 'includes/cpt.php';
require_once CTS_PATH . 'includes/shortcode.php';

/**
 * Register scripts and styles
 */
function cts_register_assets() {
	// Swiper.js CSS from CDN
	wp_register_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );

	// Plugin CSS
	wp_register_style( 'cts-style', CTS_URL . 'assets/css/style.css', array( 'swiper-css' ), '1.0.0' );

	// Swiper.js JS from CDN
	wp_register_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

	// Plugin JS
	wp_register_script( 'cts-script', CTS_URL . 'assets/js/slider.js', array( 'swiper-js' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'cts_register_assets' );
