<?php
/**
 * Visual Composer Bullets
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Not needed in admin ever
if ( is_admin() ) {
    return;
}

// Required VC functions
if ( ! function_exists( 'vc_map_get_attributes' ) ) {
	vcex_function_needed_notice();
	return;
}

// Content required
if ( ! empty( $content ) ) {

	// Get shortcode attributes
	$atts = vc_map_get_attributes( 'vcex_shortcode', $atts );

	// Define classes
	$classes = 'vcex-sshortcode clr';
	if ( $atts['visibility'] ) {
		$classes .= ' '. $atts['visibility'];
	}
	if ( $atts['css_animation'] && 'none' != $atts['css_animation'] ) {
		$classes .= ' '. vcex_get_css_animation( $atts['css_animation'] );
	}
	$classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $classes, 'vcex_shortcode', $atts );

	// Echo shortcode
	echo '<div class="'. esc_attr( $classes ) .'">'. do_shortcode( $content ) .'</div>';
	
}