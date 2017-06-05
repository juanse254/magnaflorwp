<?php
/**
 * WooCommerce First Data
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce First Data to newer
 * versions in the future. If you wish to customize WooCommerce First Data for your
 * needs please refer to http://docs.woocommerce.com/document/firstdata/
 *
 * @package     WC-First-Data
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Ensures the plugin remains active when updated after main plugin file name change.
 *
 * For First Data, the main plugin file name was changed from `woocommerce-gateway-firstdata.php`
 * to `woocommerce-gateway-first-data.php`
 *
 * This can eventually be removed when most merchants will have upgraded to this
 * version.
 *
 * @since 4.0.0
 */
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $active_plugins as $key => $active_plugin ) {

	if ( strstr( $active_plugin, '/woocommerce-gateway-firstdata.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/woocommerce-gateway-firstdata.php', '/woocommerce-gateway-first-data.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
