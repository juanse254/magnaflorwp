<?php
/**
 * Functions that run on widgets init
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom widgets
 *
 * @since 4.0
 */
function wpex_register_custom_widgets() {

	// Get array of custom widgets
	$widgets = wpex_custom_widgets_list();

	// Loop through array and register the custom widgets
	if ( $widgets && is_array( $widgets ) ) {
		foreach ( $widgets as $widget ) {
			$file = WPEX_ClASSES_DIR . 'widgets/' . $widget . '.php';
			if ( file_exists ( $file ) ) {
				require_once $file;
			}
		}
	}

}
add_action( 'widgets_init', 'wpex_register_custom_widgets' );

/**
 * Register sidebar widget areas
 *
 * @since 4.0
 */
function wpex_register_sidebar_widget_areas() {

	// Define sidebars array
	$sidebars = array(
		'sidebar' => __( 'Main Sidebar', 'total' ),
	);

	// Pages Sidebar
	if ( wpex_get_mod( 'pages_custom_sidebar', true ) ) {
		$sidebars['pages_sidebar'] = __( 'Pages Sidebar', 'total' );
	}

	// Search Results Sidebar
	if ( wpex_get_mod( 'search_custom_sidebar', true ) ) {
		$sidebars['search_sidebar'] = __( 'Search Results Sidebar', 'total' );
	}

	// Apply filters - makes it easier to register new sidebars
	$sidebars = apply_filters( 'wpex_register_sidebars_array', $sidebars );

	// Register sidebars
	if ( $sidebars ) {

		// Sidebar tags
		$tag = wpex_get_mod( 'sidebar_headings' );
		$tag = $tag ? $tag : 'div';

		// Loop through sidebars and register them
		foreach ( $sidebars as $id => $name ) {
			register_sidebar( array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<div id="%1$s" class="sidebar-box widget %2$s clr">',
				'after_widget'  => '</div>',
				'before_title'  => '<' . $tag . ' class="widget-title">',
				'after_title'   => '</' . $tag . '>',
			) );
		}

	}

}
add_action( 'widgets_init', 'wpex_register_sidebar_widget_areas' );

/**
 * Register barter widget areas
 *
 * @since 4.0
 */
function wpex_register_barter_widget_areas() {

	// Check if barter widgets are enabled
	$barter_widgets = apply_filters( 'wpex_register_barter_sidebars', wpex_get_mod( 'barter_widgets', true ) );

	// Return if disabled
	if ( ! $barter_widgets ) {
		return;
	}

	// Footer tag
	$tag = wpex_get_mod( 'barter_headings' );
	$tag = $tag ? $tag : 'div';

	// Footer widget columns
	$barter_columns = wpex_get_mod( 'barter_widgets_columns', '4' );

	// Footer 1
	register_sidebar( array(
		'name'          => __( 'Footer Column 1', 'total' ),
		'id'            => 'barter_one',
		'before_widget' => '<div id="%1$s" class="barter-widget widget %2$s clr">',
		'after_widget'  => '</div>',
		'before_title'  => '<' . $tag . ' class="widget-title">',
		'after_title'   => '</' . $tag . '>',
	) );

	// Footer 2
	if ( $barter_columns > '1' ) {

		register_sidebar( array(
			'name'          => __( 'Footer Column 2', 'total' ),
			'id'            => 'barter_two',
			'before_widget' => '<div id="%1$s" class="barter-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag . ' class="widget-title">',
			'after_title'   => '</' . $tag . '>'
		) );

	}

	// Footer 3
	if ( $barter_columns > '2' ) {

		register_sidebar( array(
			'name'          => __( 'Footer Column 3', 'total' ),
			'id'            => 'barter_three',
			'before_widget' => '<div id="%1$s" class="barter-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag . ' class="widget-title">',
			'after_title'   => '</' . $tag . '>',
		) );

	}

	// Footer 4
	if ( $barter_columns > '3' ) {

		register_sidebar( array(
			'name'          => __( 'Footer Column 4', 'total' ),
			'id'            => 'barter_four',
			'before_widget' => '<div id="%1$s" class="barter-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag . ' class="widget-title">',
			'after_title'   => '</' . $tag . '>',
		) );

	}

	// Footer 5
	if ( $barter_columns > '4' ) {

		register_sidebar( array(
			'name'          => __( 'Footer Column 5', 'total' ),
			'id'            => 'barter_five',
			'before_widget' => '<div id="%1$s" class="barter-widget widget %2$s clr">',
			'after_widget'  => '</div>',
			'before_title'  => '<' . $tag . ' class="widget-title">',
			'after_title'   => '</' . $tag . '>',
		) );

	}

}
add_action( 'widgets_init', 'wpex_register_barter_widget_areas', 40 );