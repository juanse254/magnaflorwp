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
 * @package     WC-First-Data/Payeezy
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Payeezy JS AJAX class.
 *
 * @since 4.1.8
 */
class WC_First_Data_Payeezy_AJAX {


	/** @var \WC_Gateway_First_Data_Payeezy the Payeezy JS gateway instance */
	protected $gateway;


	/**
	 * Constructs the class.
	 *
	 * @since 4.1.8
	 * @param \WC_Gateway_First_Data_Payeezy $gateway the gateway instance
	 */
	public function __construct( WC_Gateway_First_Data_Payeezy $gateway ) {

		$this->gateway = $gateway;

		add_action( 'wp_ajax_wc_' . $this->get_gateway()->get_id() . '_log_js_data',        array( $this, 'log_js_data' ) );
		add_action( 'wp_ajax_nopriv_wc_' . $this->get_gateway()->get_id() . '_log_js_data', array( $this, 'log_js_data' ) );
	}


	/**
	 * Writes card tokenization JS request/response data to the standard debug log.
	 *
	 * @since 4.1.8
	 */
	public function log_js_data() {

		check_ajax_referer( 'wc_' . $this->get_gateway()->get_id() . '_log_js_data', 'security' );

		if ( ! empty( $_REQUEST['data'] ) ) {

			$message = sprintf( "FDToken %1\$s\n%1\$s Body: ", ! empty( $_REQUEST['type'] ) ? ucfirst( $_REQUEST['type'] ) : 'Request' );

			// add the data
			$message .= print_r( $_REQUEST['data'], true );

			$this->get_gateway()->add_debug_message( $message );
		}

		wp_send_json_success();
	}


	/**
	 * Gets the gateway instance.
	 *
	 * @since 4.1.8
	 * @return \WC_Gateway_First_Data_Payeezy
	 */
	public function get_gateway() {

		return $this->gateway;
	}


}
