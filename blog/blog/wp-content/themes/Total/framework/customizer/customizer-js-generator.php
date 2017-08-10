<?php
/**
 * Generates JS for live customizer previews
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$output = '';
$customizer = new WPEX_Customizer();
$customizer->add_sections();
$settings = wp_list_pluck( $customizer->sections, 'settings' );
foreach ( $settings as $settings_array ) {
	foreach ( $settings_array as $setting ) {
		if ( ! isset( $setting['inline_css'] ) ) {
			continue;
		}
		$transport = isset( $setting['transport'] ) ? $setting['transport'] : 'refresh';

		if ( 'postMessage' == $transport ) {

			// Open js output
			$output .= 'api("'. $setting['id'] .'", function(value){value.bind(function(newval){';

			// Get inline css
			$inline_css  = $setting['inline_css'];
			$target      = isset( $inline_css['target'] ) ? $inline_css['target'] : '';
			$target      = is_array( $target ) ? $target : array( $target );
			$target      = implode( ',', $target );
			$is_hover    = isset( $inline_css['is_hover'] ) ? true : false;
			$alter       = isset( $inline_css['alter'] ) ? $inline_css['alter'] : '';
			$important   = isset( $inline_css['important'] ) ? '!important' : false;
			$media_query = isset( $inline_css['media_query'] ) ? $inline_css['media_query'] : false;

			// Generate style classname
			$style_class = 'customizer-'. $setting['id'];

			// Get output
			$mods = '';
			if ( is_array( $alter ) ) {
				foreach( $alter as $alter_val ) {
					$mods .= $alter_val .': \' + newval + \''. $important .';';
				}
			} else {
				$mods = $alter .': \' + newval + \''. $important .';';
			}

			// These are the styles to add inside the style tag
			$styles = $target .' { '. $mods .' }';

			// If it has a media query it's its own thing
			if ( $media_query ) {
				$styles = '@media only screen and '. $media_query . '{ '. $styles .' }';
			}

			$output .= '
				var el = $( \'.'. $style_class .'\' );
				if ( newval ) {
					var style = \'<style class="'. $style_class .'">'. $styles .'</style>\';
					if ( el.length ) {
						el.replaceWith( style );
					} else {
						 $( \'head\' ).append( style );
					}
				} else {
					el.remove();
				}
			';

			// Close js output
			$output .= '});});';

		}
	} // End foreach setting
}
$output = $output;
echo $output;
exit;