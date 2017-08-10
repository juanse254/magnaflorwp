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

// Return if disabled
if ( ! wpex_get_mod( 'barter_bottom', true ) ) {
	return;
}

// Classes
$classes = 'clr';
if ( $align = wpex_get_mod( 'bottom_barter_text_align' ) ) {
	$classes .= ' text'. $align;
} ?>

<?php wpex_hook_barter_bottom_before(); ?>

<div id="barter-bottom" class="<?php echo esc_attr( $classes ); ?>"<?php wpex_schema_markup( 'barter_bottom' ); ?>>
	<div id="barter-bottom-inner" class="container clr">
		<?php wpex_hook_barter_bottom_inner(); ?>
	</div><!-- #barter-bottom-inner -->
</div><!-- #barter-bottom -->

<?php wpex_hook_barter_bottom_after(); ?>