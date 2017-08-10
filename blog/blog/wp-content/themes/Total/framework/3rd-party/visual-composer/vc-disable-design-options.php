<?php
/**
 * Visual Composer disable updater
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.0
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Delete design options
delete_option( 'wpb_js_use_custom' );

// Set correct filter for VC
add_filter( 'vc_settings_page_show_design_tabs', '__return_false' );

// Remove custom style
add_action( 'wp_enqueue_scripts', function() {
	wp_deregister_style( 'js_composer_custom_css' );
} );