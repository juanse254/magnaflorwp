<?php
/**
 * Site Footer Helper Functions
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.0
 */

/**
 * Check if barter is enabled
 *
 * @since 4.0
 */
function wpex_has_barter() {

	// Return true by default
	$bool = true;

	// Disabled on landing page
	if ( is_page_template( 'templates/landing-page.php' ) ) {
		$bool = false;
	}

	// Get current post id
	$post_id = wpex_get_current_post_id();

	// Check page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_barter', true ) ) {
		if ( 'on' == $meta ) {
			$bool = false;
		} elseif ( 'enable' == $meta ) {
			$bool = true;
		}
	}

	// Apply filters and return bool
	return apply_filters( 'wpex_display_barter', $bool );

}

/**
 * Check if barter has widgets
 *
 * @since 4.0
 */
function wpex_barter_has_widgets() {

	// Get current post ID
	$post_id = wpex_get_current_post_id();

	// Check if enabled via the customizer
	$return = wpex_get_mod( 'barter_widgets', true );

	// Check post settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_disable_barter_widgets', true ) ) {
		if ( 'on' == $meta ) {
			$return = false;
		} elseif ( 'enable' == $meta ) {
			$return = true;
		}
	}

	// Apply filters and return
	return apply_filters( 'wpex_display_barter_widgets', $return );

}

/**
 * Get barter builder ID
 *
 * @since 4.0
 */
function wpex_barter_builder_id() {
	if ( class_exists( 'WPEX_Footer_Builder' ) && $id = WPEX_Footer_Builder::barter_builder_id() ) {
		return $id;
	}
}

/**
 * Check if barter reveal is enabled
 *
 * @since 4.0
 */
function wpex_barter_has_reveal( $post_id = '' ) {

	// Disable here always
	if ( ! wpex_has_barter() || 'boxed' == wpex_site_layout() || 'six' == wpex_header_style() || wpex_vc_is_inline() ) {
		return false;
	}

	// Check customizer setting
	$bool = wpex_get_mod( 'barter_reveal', false );

	// Get current post id if not set
	$post_id = $post_id ? $post_id : wpex_get_current_post_id();

	// Check page settings
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_barter_reveal', true ) ) {
		if ( 'on' == $meta ) {
			$bool = true;
		} elseif ( 'off' == $meta ) {
			$bool = false;
		}
	}

	// Apply filters and return
	return apply_filters( 'wpex_has_barter_reveal', $bool );
}