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
 * Payeezy Credit Card Class
 *
 * Handles credit card specific functionality
 *
 * @since 4.0.0
 */
class WC_Gateway_First_Data_Payeezy_Credit_Card extends WC_Gateway_First_Data_Payeezy {


	/** @var string production JS security key */
	protected $js_security_key;

	/** @var string production Transarmor token */
	protected $transarmor_token;

	/** @var string sandbox JS security key */
	protected $sandbox_js_security_key;

	/** @var string sandbox TransArmor token */
	protected $sandbox_transarmor_token;

	/** @var string whether soft descriptors are enabled or not */
	protected $soft_descriptors_enabled;

	/** @var string DBA name soft descriptor */
	protected $soft_descriptor_dba_name;

	/** @var string street soft descriptor */
	protected $soft_descriptor_street;

	/** @var string city soft descriptor */
	protected $soft_descriptor_city;

	/** @var string region soft descriptor */
	protected $soft_descriptor_region;

	/** @var string postal code soft descriptor */
	protected $soft_descriptor_postal_code;

	/** @var string country code soft descriptor */
	protected $soft_descriptor_country_code;

	/** @var string MID soft descriptor */
	protected $soft_descriptor_mid;

	/** @var string MCC soft descriptor */
	protected $soft_descriptor_mcc;

	/** @var string merchant contact info soft descriptor */
	protected $soft_descriptor_merchant_contact_info;


	/**
	 * Setup the class
	 *
	 * @since 4.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_First_Data::PAYEEZY_CREDIT_CARD_GATEWAY_ID,
			wc_first_data(),
			array(
				'method_title'       => __( 'Payeezy Credit Card', 'woocommerce-gateway-firstdata' ),
				'method_description' => __( 'Allow customers to securely pay using their credit card via Payeezy.', 'woocommerce-gateway-firstdata' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
				),
				'payment_type'       => self::PAYMENT_TYPE_CREDIT_CARD,
				'environments'       => $this->get_payeezy_environments(),
				'shared_settings'    => $this->shared_settings_names,
				'card_types' => array(
					'VISA'    => 'Visa',
					'MC'      => 'MasterCard',
					'AMEX'    => 'American Express',
					'DISC'    => 'Discover',
					'DINERS'  => 'Diners',
					'JCB'     => 'JCB',
				),
			)
		);

		// add hidden inputs that client-side JS populates with token/last 4 of card
		add_action( 'wc_first_data_payeezy_credit_card_payment_form', array( $this, 'render_hidden_inputs' ) );

		// remove card number/csc input names so they're not POSTed
		add_filter( 'wc_first_data_payeezy_credit_card_payment_form_default_credit_card_fields', array( $this, 'remove_credit_card_field_input_names' ) );
	}


	/** Admin Methods *********************************************************/


	/**
	 * Returns an array of form fields specific for the method
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		// force tokenization to be enabled, since Payeezy.js requires it ¯\_(ツ)_/¯
		$this->form_fields['tokenization'] = array(
			'title'       => __( 'Required Transarmor Entitlement', 'woocommerce-gateway-firstdata' ),
			'label'       => __( 'I certify that I have enabled Transarmor entitlement on my Payeezy account by contacting my merchant account representative.', 'woocommerce-gateway-firstdata' ),
			'type'        => 'checkbox',
			'default'     => 'yes',
			'description' => sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
				__( 'Note that you %1$smust enable%2$s the Transarmor entitlement on your Payeezy account by contacting your merchant account representative. This will additionally allow customers to securely save their payment details for future checkout.', 'woocommerce-gateway-firstdata' ),
				'<strong>', '</strong>'
			),
		);

		$form_fields = array(

			// production
			'js_security_key' => array(
				'title'    => __( 'JS Security Key', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your JS security key.', 'woocommerce-gateway-firstdata' ),
			),

			'transarmor_token' => array(
				'title'    => __( 'Transarmor Token', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Transarmor token.', 'woocommerce-gateway-firstdata' ),
			),

			// sandbox
			'sandbox_js_security_key' => array(
				'title'    => __( 'Sandbox JS Security Key', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'Your sandbox JS security key.', 'woocommerce-gateway-firstdata' ),
			),

			'sandbox_transarmor_token' => array(
				'title'    => __( 'Sandbox Transarmor Token', 'woocommerce-gateway-firstdata' ),
				'type'     => 'text',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'Your sandbox Transarmor token.', 'woocommerce-gateway-firstdata' ),
				'default'  => 'NOIW',
			),

			// soft descriptors
			'soft_descriptors_title' => array(
				'title' => __( 'Soft Descriptor Settings', 'woocommerce-gateway-firstdata' ),
				'type'  => 'title',
			),

			'soft_descriptors_enabled' => array(
				'title'       => __( 'Soft Descriptors', 'woocommerce-gateway-firstdata' ),
				'label'       => __( 'Enable soft descriptors', 'woocommerce-gateway-firstdata' ),
				'type'        => 'checkbox',
				'description' => 'All of the soft descriptors are optional.  If you would like to use Soft Descriptors, please contact your First Data Relationship Manager or Sales Rep and have them set your "Foreign Indicator" in your "North Merchant Manager File" to "5".',
				'default'     => 'no',
			),

			'soft_descriptor_dba_name' => array(
				'title'    => __( 'DBA Name', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business name.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_street' => array(
				'title'    => __( 'Street', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business address.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_city' => array(
				'title'    => __( 'City', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business city.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_region' => array(
				'title'    => __( 'Region', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business region.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_postal_code' => array(
				'title'    => __( 'Postal Code', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business postal/zip code.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_country_code' => array(
				'title'    => __( 'Country Code', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Business country.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_mid' => array(
				'title'    => __( 'MID', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Merchant ID.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_mcc' => array(
				'title'    => __( 'MCC', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Your Merchant Category Code.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),

			'soft_descriptor_merchant_contact_info' => array(
				'title'    => __( 'Merchant Contact Info', 'woocommerce-gateway-firstdata' ),
				'desc_tip' => __( 'Merchant contact information.', 'woocommerce-gateway-firstdata' ),
				'class'    => 'soft-descriptor',
				'type'     => 'text',
			),
		);

		return array_merge( parent::get_method_form_fields(), $form_fields );
	}



	/**
	 * Display the settings page with some additional JS to hide the soft descriptor
	 * settings if not enabled, and hide the transarmor token field if tokenization
	 * is not enabled
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		ob_start();
		?>
		$( '#woocommerce_first_data_payeezy_credit_card_soft_descriptors_enabled' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.soft-descriptor' ).closest( 'tr' ).show();
			} else {
				$( '.soft-descriptor' ).closest( 'tr' ).hide();
			}
		} ).change();
		<?php

		wc_enqueue_js( ob_get_clean() );
	}


	/** Frontend Methods ******************************************************/


	/**
	 * Return the gateway JS handle used to load/localize JS
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway::get_gateway_js_handle()
	 * @return string
	 */
	protected function get_gateway_js_handle() {

		return 'wc-first-data-payeezy';
	}


	/**
	 * Returns the required localized JS data for the Payeezy tokenization handler
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_gateway_js_localized_script_params() {

		return array(
			'api_url'          => $this->is_production_environment() ? 'https://api.payeezy.com/v1/securitytokens' : 'https://api-cert.payeezy.com/v1/securitytokens',
			'api_key'          => $this->get_api_key(),
			'js_security_key'  => $this->get_js_security_key(),
			'transarmor_token' => $this->get_transarmor_token(),
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'ajax_log_nonce'   => wp_create_nonce( 'wc_' . $this->get_id() . '_log_js_data' ),
			'ajax_log'         => ! $this->debug_off(),
		);
	}


	/**
	 * Remove the input names for the card number and CSC fields so they're
	 * not POSTed to the server, for security and compliance with Payeezy.js
	 *
	 * @since 4.0.0
	 * @param array $fields credit card fields
	 * @return array
	 */
	public function remove_credit_card_field_input_names( $fields ) {

		$fields['card-number']['name'] = $fields['card-csc']['name'] = '';

		return $fields;
	}


	/**
	 * Renders hidden inputs on the payment form for the card token, last four,
	 * and card type. These are populated by the client-side JS after successful
	 * tokenization.
	 *
	 * @since 4.0.0
	 */
	public function render_hidden_inputs() {

		// token
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . $this->get_id_dasherized() . '-token' );

		// card last four
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . $this->get_id_dasherized() . '-last-four' );

		// card type
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . $this->get_id_dasherized() . '-card-type' );
	}


	/**
	 * Return the default values for this payment method, used to pre-fill
	 * a valid test account number when in testing mode
	 *
	 * @since 2.0.0
	 * @see SV_WC_Payment_Gateway::get_payment_method_defaults()
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {

			$defaults['account-number'] = '4012000033330026';
		}

		return $defaults;
	}


	/**
	 * Validate the provided credit card data, including card type, last four,
	 * and token. This primarily ensures the data is safe to set on the order object
	 * in get_order() below.
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Direct::validate_credit_card_fields()
	 * @param boolean $is_valid true if the fields are valid, false otherwise
	 * @return boolean true if the fields are valid, false otherwise
	 */
	protected function validate_credit_card_fields( $is_valid ) {

		// when using a saved credit card, there is no further validation required
		if ( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) {
			return $is_valid;
		}

		$card_type  = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-card-type' );
		$last_four  = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-last-four' );
		$card_token = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-token' );

		$valid_card_types = array(
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
		);

		// card type
		if ( ! in_array( $card_type, $valid_card_types, true ) ) {

			SV_WC_Helper::wc_add_notice( __( 'Provided card type is invalid.', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// last four
		if ( preg_match( '/\D/', $last_four ) ) {

			SV_WC_Helper::wc_add_notice( __( 'Provided card last four is invalid.', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		// token
		if ( preg_match( '/\W/', $card_token ) ) {

			SV_WC_Helper::wc_add_notice( __( 'Provided card token is invalid.', 'woocommerce-gateway-firstdata' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * The CSC field is verified client-side and thus always valid.
	 *
	 * @since 4.0.0
	 * @param string $field
	 * @return bool
	 */
	protected function validate_csc( $field ) {

		return true;
	}


	/** Gateway Methods *******************************************************/


	/**
	 * Payeezy tokenizes payment methods with the sale
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Direct::tokenize_with_sale()
	 * @return bool
	 */
	public function tokenize_with_sale() {

		return true;
	}


	/**
	 * Get the order, adds Payeezy Gateway specific info to the order:
	 *
	 * + payment->full_type (string) Payeezy Gateway card type for tokenized transactions
	 * + payment->soft_descriptors (array) pre-sanitized array of soft descriptors for transaction
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Direct::get_order()
	 * @param int|\WC_Order $order_id
	 * @return \WC_Order
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		// add payment information when not using a saved credit card
		if ( empty( $order->payment->token ) ) {

			// expiry month/year
			list( $order->payment->exp_month, $order->payment->exp_year ) = array_map( 'trim', explode( '/', SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-expiry' ) ) );

			// format the expiration date as mm & yy
			$order->payment->exp_month = zeroise( $order->payment->exp_month, 2 );
			$order->payment->exp_year  = substr( $order->payment->exp_year, -2 );

			// card type
			$card_type                 = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-card-type' );
			$order->payment->card_type = SV_WC_Payment_Gateway_Helper::normalize_card_type( $card_type );

			// last four
			$order->payment->last_four = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-last-four' );

			// fake the accout number so other parts of the framework that expect a full account number work as expected
			$order->payment->account_number = '*' . $order->payment->last_four;

			// token
			$order->payment->token = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-token' );
		}

		// required for transactions with tokens
		$order->payment->full_type = $this->get_full_card_type( $order );

		// soft descriptors
		$order->payment->soft_descriptors = $this->soft_descriptors_enabled() ? $this->get_soft_descriptors() : array();

		// test amount when in demo environment
		if ( $this->is_test_environment() && ( $test_amount = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-test-amount' ) ) ) {
			$order->payment_total = SV_WC_Helper::number_format( $test_amount );
		}

		return $order;
	}


	/**
	 * Get the order for capturing, adds:
	 *
	 * + capture->transaction_tag (string)
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Direct::get_order_for_capture()
	 * @param \WC_Order $order
	 * @return \WC_Order
	 */
	protected function get_order_for_capture( $order ) {

		$order = parent::get_order_for_capture( $order );

		$order->capture->transaction_tag = $this->get_order_meta( $order, 'transaction_tag' );

		return $order;
	}


	/**
	 * Adds capture-specific transaction tag to the order after a
	 * capture is performed, as a refund requires this, *not* the original transaction's
	 * tag
	 *
	 * @since 4.0.0
	 * @param \WC_Order $order the order object
	 * @param \WC_First_Data_Payeezy_API_Transaction_Response $response transaction response
	 */
	protected function add_payment_gateway_capture_data( $order, $response ) {

		$this->update_order_meta( $order, 'capture_transaction_tag', $response->get_transaction_tag() );
	}


	/**
	 * Get the order for refunding/voiding, adds:
	 *
	 * + capture->transaction_tag (string)
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway::get_order_for_refund()
	 * @param int|\WC_Order $order
	 * @param float $amount
	 * @param string $reason
	 * @return int|\WC_Order
	 */
	protected function get_order_for_refund( $order, $amount, $reason ) {

		$order = parent::get_order_for_refund( $order, $amount, $reason );

		// if an authorize-only transaction was captured, use the capture trans ID instead
		if ( $capture_trans_tag = $this->get_order_meta( $order, 'capture_transaction_tag' ) ) {

			$order->refund->transaction_tag = $capture_trans_tag;
			$order->refund->trans_id = $this->get_order_meta( $order, 'capture_trans_id' );

		} else {

			$order->refund->transaction_tag = $this->get_order_meta( $order, 'transaction_tag' );
		}

		return $order;
	}


	/** Subscriptions *********************************************************/


	/**
	 * Tweak the labels shown when editing the payment method for a Subscription
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Integration_Subscriptions::admin_add_payment_meta()
	 * @param array $meta payment meta
	 * @param \WC_Subscription $subscription subscription being edited
	 * @return array
	 */
	public function subscriptions_admin_add_payment_meta( $meta, $subscription ) {

		// note that for EU merchants, the tokens are called "DataVault" instead of "TransArmor"
		if ( isset( $meta[ $this->get_id() ] ) ) {
			$meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['label'] = __( 'TransArmor Token', 'woocommerce-gateway-firstdata' );
		}

		return $meta;
	}


	/**
	 * Validate that the TransArmor token for a Subscription is alphanumeric
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Integration_Subscriptions::admin_validate_payment_meta()
	 * @param array $meta payment meta
	 * @throws \Exception if transarmor token is not numeric
	 */
	public function subscriptions_admin_validate_payment_meta( $meta ) {

		// transarmor token (payment_token) must be alphanumeric
		if ( ! ctype_alnum( (string) $meta['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['value'] ) ) {
			throw new Exception( __( 'TransArmor token can only include letters and/or numbers.', 'woocommerce-gateway-firstdata' ) );
		}
	}


	/** Getters ***************************************************************/


	/**
	 * Returns true if the gateway is properly configured to perform transactions
	 *
	 * @since 4.0.0
	 * @see WC_Gateway_First_Data_Payeezy::is_configured()
	 * @return boolean true if the gateway is properly configured
	 */
	protected function is_configured() {

		$is_configured = parent::is_configured();

		// tokenization is required / missing required configuration fields
		if ( ! $this->tokenization_enabled() || ! $this->get_js_security_key() || ! $this->get_transarmor_token() ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/**
	 * Get the full card type for a given order, Payeezy requires this
	 * to be set when performing a transaction with a saved token
	 *
	 * @since 4.0.0
	 * @param \WC_Order $order
	 * @return string|null
	 */
	protected function get_full_card_type( WC_Order $order ) {

		$types = array(
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX       => 'American Express',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA       => 'Visa',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD => 'Mastercard',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER   => 'Discover',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB => 'Diners Club',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB        => 'JCB',
		);

		return isset( $types[ $order->payment->card_type ] ) ? $types[ $order->payment->card_type ] : null;
	}


	/**
	 * Returns the JS security key based on the current environment
	 *
	 * @since 4.0.0
	 * @param string $environment_id optional, defaults to production
	 * @return string JS security key
	 */
	public function get_js_security_key( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->js_security_key : $this->sandbox_js_security_key;
	}


	/**
	 * Returns the transarmor token based on the current environment
	 *
	 * @since 4.0.0
	 * @param string $environment_id optional, defaults to production
	 * @return string transarmor token
	 */
	public function get_transarmor_token( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->transarmor_token : $this->sandbox_transarmor_token;
	}


	/**
	 * Returns true if soft descriptors are enabled
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function soft_descriptors_enabled() {

		return 'yes' === $this->soft_descriptors_enabled;
	}


	/**
	 * Returns an array of soft descriptors as entered by the admin, sanitized
	 * and ready for adding to the transaction request data
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_soft_descriptors() {

		$descriptor_names = array(  'dba_name', 'street', 'city', 'region', 'postal_code',
			'country_code', 'mid', 'mcc', 'merchant_contact_info',
		);

		$descriptors = array();

		foreach ( $descriptor_names as $descriptor_name ) {

			$descriptor_key = "soft_descriptor_{$descriptor_name}";

			if ( ! empty( $this->$descriptor_key ) ) {

				// ASCII required
				$descriptor = SV_WC_Helper::str_to_ascii( $this->$descriptor_key );

				// truncate to 3 chars
				if ( 'region' === $descriptor_name || 'country_code' === $descriptor_name ) {
					$descriptor = substr( $descriptor, 0, 3 );
				}

				$descriptors[ $descriptor_name ] = $descriptor;
			}
		}

		return $descriptors;
	}


}
