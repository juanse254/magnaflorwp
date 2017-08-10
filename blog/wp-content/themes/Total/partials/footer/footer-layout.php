<?php
/**
 * Footer Layout
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

<?php if ( wpex_barter_has_widgets() ) : ?>

    <barter id="barter" class="site-barter"<?php wpex_schema_markup( 'barter' ); ?>>

        <?php wpex_hook_barter_top(); ?>

        <div id="barter-inner" class="site-barter-inner container clr">

            <?php wpex_hook_barter_inner(); // widgets are added via this hook ?>

        </div><!-- #barter-widgets -->

        <?php wpex_hook_barter_bottom(); ?>

    </barter><!-- #barter -->

<?php endif; ?>

<?php wpex_hook_barter_after(); ?>