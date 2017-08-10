<?php
// @version 3.6.0

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class_to_filter = 'wpb_text_column wpb_content_element ' . $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

// Total inline styles
$inline_style = vcex_inline_style( array(
	'color'               => isset( $color ) ? $color : '',
	'font_family'         => isset( $font_family ) ? $font_family : '',
	'font_size'           => isset( $font_size ) ? $font_size : '',
	'letter_spacing'      => isset( $letter_spacing ) ? $letter_spacing : '',
	'font_weight'         => isset( $font_weight ) ? $font_weight : '',
	'text_align'          => isset( $text_align ) ? $text_align : '',
	'line_height'         => isset( $line_height ) ? $line_height : '',
	'width'               => isset( $width ) ? $width : '',
) );

// Italic
if ( isset( $italic ) && 'true' == $italic ) {
	$css_class .= ' wpex-italic';
}

// Load custom font
if ( $font_family ) {
	wpex_enqueue_google_font( $font_family );
}

// Responsive Text
$wrap_data = '';
if ( 'true' == $responsive_text && $font_size && $min_font_size ) {

	// Convert em font size to pixels
	if ( strpos( $font_size, 'em' ) !== false ) {
		$font_size = str_replace( 'em', '', $font_size );
		$font_size = $font_size * wpex_get_body_font_size();
	}

	// Convert em min-font size to pixels
	if ( strpos( $min_font_size, 'em' ) !== false ) {
		$min_font_size = str_replace( 'em', '', $min_font_size );
		$min_font_size = $min_font_size * wpex_get_body_font_size();
	}

	// Add inline Jsv
	vcex_inline_js( 'responsive_text' );

	// Add wrap classes and data
	$css_class .= ' wpex-responsive-txt';
	$wrap_data .= ' data-max-font-size="'. absint( $font_size ) .'"';
	$wrap_data .= ' data-min-font-size="'. absint( $min_font_size ) .'"';
}

// Output
$output = '';

$output .= '<div class="'. esc_attr( $css_class ) . '"'. $inline_style . $wrap_data .'>';

	$output .= '<div class="wpb_wrapper">';

		$output .= wpb_js_remove_wpautop( $content, true );

	$output .= '</div>';

$output .= '</div>';

echo $output;