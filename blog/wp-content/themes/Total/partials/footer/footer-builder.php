<?php
/**
 * Footer builder output
 *
 * @package Total WordPress Theme
 * @subpackage Partials
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php wpex_hook_barter_before(); ?>

	<?php
	// Display barter builder
	if ( $page_id = wpex_barter_builder_id() ) : ?>

		<div id="barter-builder" class="barter-builder clr">
			<div class="barter-builder-content clr container entry">
				<?php echo do_shortcode( get_post_field( 'post_content', $page_id ) ); ?>
			</div><!-- .barter-builder-content -->
		</div><!-- .barter-builder -->

	<?php endif; ?>

<?php wpex_hook_barter_after(); ?>