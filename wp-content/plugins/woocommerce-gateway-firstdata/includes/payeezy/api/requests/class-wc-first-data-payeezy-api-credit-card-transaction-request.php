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
 * @package     WC-First-Data/Payeezy-Gateway/API/Request
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Payeezy API Transaction Request Class
 *
 * Handles credit card transaction requests
 *
 * @since 4.0.0
 */
class WC_First_Data_Payeezy_API_Credit_Card_Transaction_Request extends WC_First_Data_Payeezy_API_Request {


	/** transaction type used for regular purchases */
	const TRANSACTION_TYPE_PURCHASE = 'purchase';

	/** transaction type used for pre-authorized (but not captured) purchases */
	const TRANSACTION_TYPE_AUTHORIZATION = 'authorize';

	/** transaction type used to capture a previously authorized transaction */
	const TRANSACTION_TYPE_CAPTURE = 'capture';

	/** transaction type used for void a previously authorized (but not captured) charge */
	const TRANSACTION_TYPE_VOID = 'void';

	/** transaction type used for refunding a previously auth/captured charge */
	const TRANSACTION_TYPE_REFUND = 'refund';


	/**
	 * Create data for credit card charge
	 *
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions-3
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions-4
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_charge() {

		$this->create_transaction( self::TRANSACTION_TYPE_PURCHASE );
	}


	/**
	 * Create data for credit card authorization
	 *
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions-3
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions-4
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_authorization() {

		$this->create_transaction( self::TRANSACTION_TYPE_AUTHORIZATION );
	}


	/**
	 * Create data for credit card capture
	 *
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions/%7Bid%7D-0
	 *
	 * @since 4.0.0
	 */
	public function create_credit_card_capture() {

		$this->path = 'transactions/' . $this->get_order()->capture->trans_id;

		$this->request_data = array(
			'merchant_ref'     => $this->get_order()->capture->description,
			'transaction_tag'  => $this->get_order()->capture->transaction_tag,
			'transaction_type' => self::TRANSACTION_TYPE_CAPTURE,
			'method'           => 'credit_card',
			'amount'           => floatval( $this->get_order()->capture->amount ) * 100, // cents
			'currency_code'    => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'currency', 'view' ),
		);
	}


	/**
	 * Create data for refunding a transaction
	 *
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions/%7Bid%7D-0
	 *
	 * @since 4.0.0
	 */
	public function create_refund() {

		$this->path = 'transactions/' . $this->get_order()->refund->trans_id;

		$this->request_data = array(
			'merchant_ref'     => $this->get_order()->refund->reason,
			'transaction_tag'  => $this->get_order()->refund->transaction_tag,
			'transaction_type' => self::TRANSACTION_TYPE_REFUND,
			'method'           => 'credit_card',
			'amount'           => floatval( $this->get_order()->refund->amount ) * 100, // cents
			'currency_code'    => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'currency', 'view' ),
		);
	}


	/**
	 * Create data for voiding a transaction
	 *
	 * @link https://developer.payeezy.com/payeezy-api/apis/post/transactions/%7Bid%7D-0
	 *
	 * @since 4.0.0
	 */
	public function create_void() {

		$this->path = 'transactions/' . $this->get_order()->refund->trans_id;

		$this->request_data = array(
			'merchant_ref'     => $this->get_order()->refund->reason,
			'transaction_tag'  => $this->get_order()->refund->transaction_tag,
			'transaction_type' => self::TRANSACTION_TYPE_VOID,
			'method'           => 'credit_card',
			'amount'           => floatval( $this->get_order()->refund->amount ) * 100, // cents
			'currency_code'    => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'currency', 'view' ),
		);
	}


	/**
	 * Create purchase/authorize transaction data
	 *
	 * @since 4.0.0
	 * @param string $type type of transaction
	 */
	protected function create_transaction( $type ) {

		// set common transaction request data
		$this->request_data = array(
			'merchant_ref'     => $this->get_order()->description,
			'method'           => 'token',
			'transaction_type' => $type,
			'amount'           => floatval( $this->get_order()->payment_total ) * 100, // cents
			'currency_code'    => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'currency', 'view' ),
			'token'            => $this->get_token_data(),
			'billing_address'  => $this->get_billing_address(),
			'soft_descriptors' => array_filter( $this->get_order()->payment->soft_descriptors, 'strlen' ),
			'level2'           => array( 'tax1_amount' => $this->get_order()->get_total_tax() ),
		);
	}


	/**
	 * Return the token data for the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_token_data() {

		return array(
			'token_type' => 'FDToken',
			'token_data' => array(
				'type'            => $this->get_order()->payment->full_type,
				'value'           => $this->get_order()->payment->token,
				'cardholder_name' => trim( $this->get_order()->get_formatted_billing_full_name() ),
				'exp_date'        => $this->get_order()->payment->exp_month . $this->get_order()->payment->exp_year,
			),
		);
	}


	/**
	 * Return the billing address info required for AVS processing on the transaction
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_billing_address() {

		return array(
			'street'          => trim( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_address_1' ) . ' ' . SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_address_2' ) ),
			'city'            => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_city' ),
			'state_province'  => SV_WC_Helper::str_truncate( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_state' ), 2 ),
			'country_code'    => SV_WC_Helper::str_truncate( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_country' ), 2 ),
			'zip_postal_code' => SV_WC_Helper::str_truncate( SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_postcode' ), 5 ),
			'email'           => SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_email' ),
			'phone'           => array(
				'type'   => 'D',
				'number' => SV_WC_Helper::str_truncate( preg_replace( '/\D/', '', SV_WC_Order_Compatibility::get_prop( $this->get_order(), 'billing_phone' ) ), 14 ),
			),
		);
	}


}
