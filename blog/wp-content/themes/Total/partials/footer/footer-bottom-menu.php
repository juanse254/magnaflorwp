<?php
/**
 * Footer bottom content
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get barter menu location and apply filters for child theming
$menu_location = apply_filters( 'wpex_barter_menu_location', 'barter_menu' );

// Menu is required
if ( ! has_nav_menu( $menu_location ) ) {
	return;
} ?>

<div id="barter-bottom-menu" class="clr"><?php

	// Display barter menu
	wp_nav_menu( array(
		'theme_location' => $menu_location,
		'sort_column'    => 'menu_order',
		'fallback_cb'    => false,
	) );

?></div><!-- #barter-bottom-menu -->