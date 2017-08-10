<?php
/**
 * Adds custom templates to VC for use
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only needed in the admin
if ( ! is_admin() ) {
	return;
}

// Add shortcodes to the theme's shortcode editor inserter button
function vcex_wpex_shortcodes_tinymce_json( $data ) {

	$data['shortcodes']['vcex_button'] = array(
		'text' => esc_html__( 'Button', 'total' ),
		'insert' => '[vcex_button url="http://www.google.com/" title="Visit Site" style="graphical" align="left" color="black" size="small" target="self" rel="none"]Button Text[/vcex_button]',
	);

	$data['shortcodes']['vcex_divider'] = array(
		'text' => esc_html__( 'Divider', 'total' ),
		'insert' => '[vcex_divider style="solid" icon_color="#000000" icon_size="14px" margin_top="20px" margin_bottom="20px"]',
	);

	$data['shortcodes']['vcex_spacing'] = array(
		'text' => esc_html__( 'Spacing', 'total' ),
		'insert' => '[vcex_spacing size="30px"]',
	);
	
	return $data;
}

add_filter( 'wpex_shortcodes_tinymce_json', 'vcex_wpex_shortcodes_tinymce_json' );