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
 * @package     WC-First-Data/Global-Gateway/API/Request
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Global Gateway API Transaction Request Class
 *
 * Handles transaction requests
 *
 * @since 4.0.0
 */
class WC_First_Data_Global_Gateway_API_Transaction_Request extends WC_First_Data_Global_Gateway_API_Request {


	/** transaction type used for regular purchases */
	const TRANSACTION_TYPE_SALE = 'SALE';

	/** transaction type used for pre-authorized (but not captured) purchases */
	const TRANSACTION_TYPE_PRE_AUTH = 'PREAUTH';

	/** transaction type used to capture a previously authorized transaction */
	const TRANSACTION_TYPE_POST_AUTH = 'POSTAUTH';

	/** transaction type used for refunding a previously auth/captured charge */
	const TRANSACTION_TYPE_REFUND = 'CREDIT';

	/** internet transaction origin */
	const TRANSACTION_ORIGIN_INTERNET = 'ECI';


	/**
	 * Create data for credit card charge
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_charge() {

		$this->create_transaction( self::TRANSACTION_TYPE_SALE );
	}


	/**
	 * Create data for credit card authorization
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_authorization() {

		$this->create_transaction( self::TRANSACTION_TYPE_PRE_AUTH );
	}


	/**
	 * Create data for credit card capture
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_capture() {

		$this->create_transaction( self::TRANSACTION_TYPE_POST_AUTH );
	}


	/**
	 * Create data for refunding a transaction
	 *
	 * @since 4.0.0
	 */
	public function create_refund() {

		$this->create_transaction( self::TRANSACTION_TYPE_REFUND );
	}


	/**
	 * Create transaction data
	 *
	 * @since 4.0.0
	 * @param string $type type of transaction
	 */
	protected function create_transaction( $type ) {

		// set common transaction request data
		$this->request_data = array(
			'orderoptions'       => array(
				'ordertype' => $type,
				'result' => 'live', // when testing, this can be set to "good" for approved response, "decline" for declined response, or "duplicate" for duplicate transaction response
			),
			'billing'            => $this->get_billing_addres(),
			'shipping'           => $this->get_shipping_address(),
			'transactiondetails' => $this->get_transaction_details(),
			'payment' => $this->get_payment_data(),
		);

		switch ( $type ) {

			// purchases
			case self::TRANSACTION_TYPE_SALE:
			case self::TRANSACTION_TYPE_PRE_AUTH:

				$this->request_data['creditcard'] = $this->get_credit_card_data();
			break;

			// capturing previous authorization
			case self::TRANSACTION_TYPE_POST_AUTH:

				$this->request_data['payment']                   = array( 'chargetotal' => $this->get_order()->capture->amount );
				$this->request_data['transactiondetails']['oid'] = $this->get_order()->capture->trans_id;
			break;

			// refund/void
			case self::TRANSACTION_TYPE_REFUND:

				$this->request_data['payment']                   = array( 'chargetotal' => $this->get_order()->refund->amount );
				$this->request_data['transactiondetails']['oid'] = $this->get_order()->refund->trans_id;
			break;
		}
	}


	/**
	 * Get credit card data for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_credit_card_data() {

		return array(
			'cardnumber'   => $this->get_order()->payment->account_number,
			'cardexpmonth' => $this->get_order()->payment->exp_month,
			'cardexpyear'  => $this->get_order()->payment->exp_year,
			'cvmvalue'     => $this->get_order()->payment->csc,
			'cvmindicator' => ! empty( $this->get_order()->payment->csc ) ? 'provided' : 'not_provided',
		);
	}


	/**
	 * Get the billing address data for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_billing_addres() {

		return array(
			'name'     => $this->truncate_address_field( trim( $this->get_order()->get_formatted_billing_full_name() ), 96 ),
			'company'  => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_company' ), 96 ),
			'address1' => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_address_1' ), 96 ),
			'address2' => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_address_2' ), 96 ),
			'city'     => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_city' ), 96 ),
			'state'    => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_state' ), 96 ),
			'zip'      => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_postcode' ), 5, true ),
			'country'  => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_country' ),
			'phone'    => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_phone' ), 32, true ),
			'email'    => SV_WC_Helper::str_truncate( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_email' ), 64 ),
			'userid'   => $this->get_order()->get_user_id(),
		);
	}


	/**
	 * Get the shipping address data for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_shipping_address() {

		return array(
			'name'     => $this->truncate_address_field( trim( $this->get_order()->get_formatted_shipping_full_name() ), 96 ),
			'address1' => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_address_1' ), 96 ),
			'address2' => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_address_2' ), 96 ),
			'city'     => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_city' ), 96 ),
			'state'    => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_state' ), 96 ),
			'zip'      => $this->truncate_address_field( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_postcode' ), 5, true ),
			'country'  => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'shipping_country' ),
		);
	}


	/**
	 * Helper method to truncate an address field according to Global Gateway
	 * specifications
	 *
	 * @since 4.0.0
	 * @param string $value field value to truncate
	 * @param int $limit field length limit
	 * @param bool $no_spaces true to prevent whitespace in field value, false by default
	 * @return string
	 */
	protected function truncate_address_field( $value, $limit, $no_spaces = false ) {

		$pattern = $no_spaces ? '/[^A-z0-9]/' : '/[^A-z0-9\s]/';

		return SV_WC_Helper::str_truncate( preg_replace( $pattern, '', $value ), $limit );
	}


	/**
	 * Get the transaction detail data for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_transaction_details() {

		return array(
			'transactionorigin' => self::TRANSACTION_ORIGIN_INTERNET,
			'ip'                => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'customer_ip_address' ),
		);
	}


	/**
	 * Get the payment data (totals, etc) for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_payment_data() {

		// when using a test amount, there can't be any other amounts present otherwise a validation error occurs ¯\_(ツ)_/¯
		if ( ! empty( $this->get_order()->payment->has_test_amount ) ) {

			return array( 'chargetotal' => $this->get_order()->payment_total );

		} else {

			return array(
				'subtotal'    => SV_WC_Helper::number_format( $this->get_order()->get_total() - $this->get_order()->get_total_shipping() - $this->get_order()->get_total_tax() ),
				'tax'         => SV_WC_Helper::number_format( $this->get_order()->get_total_tax() ),
				'shipping'    => SV_WC_Helper::number_format( $this->get_order()->get_total_shipping() ),
				'chargetotal' => $this->get_order()->payment_total,
			);
		}
	}


}
