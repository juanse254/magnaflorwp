<?php
/**
 * Plugin Name: WooCommerce First Data Payeezy Gateway
 * Plugin URI: http://www.woocommerce.com/products/firstdata/
 * Description: Accept credit cards and eChecks in WooCommerce through First Data Payeezy Gateway, Payeezy, or Global Gateway
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com/
 * Version: 4.2.1
 * Text Domain: woocommerce-gateway-firstdata
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2017, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-First-Data
 * @author    SkyVerge
 * @category  Payment-Gateways
 * @copyright Copyright (c) 2013-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'eb3e32663ec0810592eaf0d097796230', '18645' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.6.1', __( 'WooCommerce First Data Gateway', 'woocommerce-gateway-firstdata' ), __FILE__, 'init_woocommerce_gateway_first_data', array(
	'is_payment_gateway'   => true,
	'minimum_wc_version'   => '2.5.5',
	'minimum_wp_version'   => '4.1',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_gateway_first_data() {

/**
 * The main class for the First Data Payeezy Gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.
 *
 * @since 2.0.0
 */
class WC_First_Data extends SV_WC_Payment_Gateway_Plugin {


	/** version number */
	const VERSION = '4.2.1';

	/** @var WC_First_Data single instance of this plugin */
	protected static $instance;

	/** the plugin identifier */
	const PLUGIN_ID = 'first_data';

	/** global gateway class name */
	const GLOBAL_GATEWAY_CLASS_NAME = 'WC_Gateway_First_Data_Global_Gateway';

	/** global gateway ID */
	const GLOBAL_GATEWAY_ID = 'first_data_global_gateway';

	/** payeezy gateway credit card class name */
	const PAYEEZY_GATEWAY_CREDIT_CARD_CLASS_NAME = 'WC_Gateway_First_Data_Payeezy_Gateway_Credit_Card';

	/** payeezy gateway credit card ID */
	const PAYEEZY_GATEWAY_CREDIT_CARD_ID = 'first_data_payeezy_gateway_credit_card';

	/** payeezy gateway echeck class name */
	const PAYEEZY_GATEWAY_ECHECK_CLASS_NAME = 'WC_Gateway_First_Data_Payeezy_Gateway_eCheck';

	/** payeezy gateway echeck ID */
	const PAYEEZY_GATEWAY_ECHECK_ID = 'first_data_payeezy_gateway_echeck';

	/** payeezy gateway credit card class name */
	const PAYEEZY_CREDIT_CARD_CLASS_NAME = 'WC_Gateway_First_Data_Payeezy_Credit_Card';

	/** payeezy gateway ID */
	const PAYEEZY_CREDIT_CARD_GATEWAY_ID = 'first_data_payeezy_credit_card';

	/** payeezy gateway echeck class name */
	const PAYEEZY_ECHECK_CLASS_NAME = 'WC_Gateway_First_Data_Payeezy_eCheck';

	/** payeezy gateway echeck ID */
	const PAYEEZY_ECHECK_GATEWAY_ID = 'first_data_payeezy_echeck';

	/** @var \WC_First_Data_Payeezy_AJAX the Payeezy JS AJAX instance */
	protected $payeezy_ajax_instance;


	/**
	 * Setup main plugin class
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::__construct()
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-gateway-firstdata',
				'gateways'    => $this->get_active_gateways(),
				'require_ssl' => true,
				'supports'    => $this->get_active_gateway_features(),
				'dependencies' => $this->get_active_gateway_dependencies(),
			)
		);

		// include required files
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ) );

		// handle switching between the included gateways
		if ( is_admin() && ! is_ajax() ) {

			// add a JS confirmation when changing to the Payeezy JS gateway
			add_action( 'pre_current_active_plugins', array( $this, 'add_change_gateway_js' ) );

			add_action( 'admin_action_wc_first_data_change_gateway', array( $this, 'change_gateway' ) );
		}
	}


	/**
	 * Include required files
	 *
	 * @since 4.0.0
	 */
	public function includes() {

		if ( $this->is_global_gateway_active() ) {

			$files = array( 'global-gateway/class-wc-gateway-first-data-global-gateway.php' );

		} elseif ( $this->is_payeezy_gateway_active() ) {

			$files = array(
				'payeezy-gateway/abstract-wc-gateway-first-data-payeezy-gateway.php',
				'payeezy-gateway/class-wc-gateway-first-data-payeezy-gateway-credit-card.php',
				'payeezy-gateway/class-wc-gateway-first-data-payeezy-gateway-echeck.php',
			);

		} elseif ( $this->is_payeezy_active() ) {

			$files = array(
				'payeezy/abstract-wc-gateway-first-data-payeezy.php',
				'payeezy/class-wc-gateway-first-data-payeezy-credit-card.php',
				'payeezy/class-wc-gateway-first-data-payeezy-echeck.php',
				'payeezy/class-wc-first-data-payeezy-ajax.php',
			);
		}

		foreach ( $files as $file_path ) {
			require_once( $this->get_plugin_path() . '/includes/' . $file_path );
		}

		if ( class_exists( 'WC_First_Data_Payeezy_AJAX' ) ) {
			$this->payeezy_ajax_instance = new WC_First_Data_Payeezy_AJAX( $this->get_gateway( self::PAYEEZY_CREDIT_CARD_GATEWAY_ID ) );
		}
	}


	/**
	 * Gets the Payeezy JS AJAX handler instance.
	 *
	 * @since 4.1.8
	 * @return \WC_First_Data_Payeezy_AJAX|null
	 */
	public function get_payeezy_ajax_instance() {

		return $this->payeezy_ajax_instance;
	}


	/**
	 * Return deprecated/removed hooks
	 *
	 * @since 4.0.0
	 * @see SV_WC_Plugin::get_deprecated_hooks()
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		// hooks removed in 4.0.0
		$payeezy_gateway_v4_0_hooks = array(
			'wc_gateway_firstdata_is_available' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_gateway_first_data_payeezy_gateway_credit_card_is_available',
			),
			'wc_firstdata_api_timeout' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_first_data_payeezy_gateway_credit_card_http_request_args',
			),
			'wc_firstdata_icon' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_first_data_payeezy_gateway_credit_card_icon',
			),
			'wc_firstdata_card_types' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_first_data_payeezy_gateway_credit_card_available_card_types',
			),
			'wc_first_data_validate_fields' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_payment_gateway_first_data_payeezy_gateway_credit_card_validate_credit_card_fields',
			),
			'wc_firstdata_manage_my_cards' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_first_data_payeezy_gateway_credit_card_manage_payment_methods_text',
			),
			'wc_firstdata_tokenize_card_text' => array(
				'version'     => '4.0.0',
				'removed'     => true,
				'replacement' => 'wc_first_data_payeezy_gateway_credit_card_tokenize_payment_method_text',
			),
		);

		return $this->is_payeezy_gateway_active() ? $payeezy_gateway_v4_0_hooks : array();
	}


	/** Gateway methods ******************************************************/


	/**
	 * Return the supported features for the active gateway
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_active_gateway_features() {

		if ( $this->is_global_gateway_active() ) {

			return array(
				self::FEATURE_CAPTURE_CHARGE,
			);

		} else {

			return array(
				self::FEATURE_CAPTURE_CHARGE,
				self::FEATURE_MY_PAYMENT_METHODS,
			);
		}
	}


	/**
	 * Return the required dependencies for the active gateway
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_active_gateway_dependencies() {

		return $this->is_global_gateway_active() ? array( 'SimpleXML', 'xmlwriter', 'dom' ) : array( 'json' );
	}


	/**
	 * Return the activated gateways, either the legacy Global Gateway, Payeezy
	 * Gateway, or Payeezy
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_active_gateways() {

		$gateways = array();

		if ( $this->is_global_gateway_active() ) {

			$gateways = array(
				self::GLOBAL_GATEWAY_ID => self::GLOBAL_GATEWAY_CLASS_NAME,
			);

		} elseif ( $this->is_payeezy_gateway_active() ) {

			$gateways = array(
				self::PAYEEZY_GATEWAY_CREDIT_CARD_ID => self::PAYEEZY_GATEWAY_CREDIT_CARD_CLASS_NAME,
				self::PAYEEZY_GATEWAY_ECHECK_ID      => self::PAYEEZY_GATEWAY_ECHECK_CLASS_NAME,
			);

		} elseif ( $this->is_payeezy_active() ) {

			$gateways = array(
				self::PAYEEZY_CREDIT_CARD_GATEWAY_ID => self::PAYEEZY_CREDIT_CARD_CLASS_NAME,
				self::PAYEEZY_ECHECK_GATEWAY_ID      => self::PAYEEZY_ECHECK_CLASS_NAME,
			);
		}

		return $gateways;
	}


	/**
	 * Return the active gateway ID
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_active_gateway() {

		return get_option( 'wc_first_data_active_gateway', self::PAYEEZY_GATEWAY_CREDIT_CARD_ID );
	}


	/**
	 * Returns true if legacy global gateway is active
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function is_global_gateway_active() {

		return self::GLOBAL_GATEWAY_ID === $this->get_active_gateway();
	}


	/**
	 * Returns true if Payeezy Gateway is active
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function is_payeezy_gateway_active() {

		return self::PAYEEZY_GATEWAY_CREDIT_CARD_ID === $this->get_active_gateway();
	}


	/**
	 * Returns true if Payeezy is active
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function is_payeezy_active() {

		return self::PAYEEZY_CREDIT_CARD_GATEWAY_ID === $this->get_active_gateway();
	}


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 3.2.0
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {

		$custom_actions = array();

		$gateways = array(
			self::GLOBAL_GATEWAY_ID, self::PAYEEZY_GATEWAY_CREDIT_CARD_ID, self::PAYEEZY_CREDIT_CARD_GATEWAY_ID
		);

		// use <gateway> links
		foreach ( $gateways as $gateway ) {
			$custom_actions[ "change_gateway_{$gateway}" ] = $this->get_change_gateway_link( $gateway );
		}

		unset( $custom_actions[ 'change_gateway_' . $this->get_active_gateway() ] );

		// add custom links to the front
		return array_merge( $custom_actions, SV_WC_Plugin::plugin_action_links( $actions ) );
	}


	/**
	 * Return the link for changing the active gateway
	 *
	 * @since 4.0.0
	 * @param string $gateway gateway ID
	 * @return string
	 */
	protected function get_change_gateway_link( $gateway ) {

		$params = array(
			'action' => 'wc_first_data_change_gateway',
			'gateway' => $gateway,
		);

		$url = wp_nonce_url( add_query_arg( $params, 'admin.php' ), $this->get_file() );

		switch ( $gateway ) {

			case self::GLOBAL_GATEWAY_ID:
				$gateway_name = esc_html__( 'Use Global Gateway', 'woocommerce-gateway-firstdata' );
			break;

			case self::PAYEEZY_GATEWAY_CREDIT_CARD_ID:
				$gateway_name = esc_html__( 'Use Payeezy Gateway', 'woocommerce-gateway-firstdata' );
			break;

			case self::PAYEEZY_CREDIT_CARD_GATEWAY_ID:
				$gateway_name = esc_html__( 'Use Payeezy JS', 'woocommerce-gateway-firstdata' );
			break;
		}

		return sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', esc_url( $url ), $gateway_name );
	}


	/**
	 * Adds a JS confirmation when changing to the Payeezy JS gateway.
	 *
	 * @since 4.1.9
	 */
	public function add_change_gateway_js() {

		ob_start(); ?>

		$( document ).on( 'click', '.change_gateway_<?php echo esc_js( self::PAYEEZY_CREDIT_CARD_GATEWAY_ID ); ?>', function( e ) {

			var message = '<?php echo esc_js( __( 'This will enable the Payeezy JS gateway. You don\'t need to switch to this if you\'re using Payeezy Global Gateway e4.', 'woocommerce-gateway-firstdata' ) ); ?>';

			if ( ! confirm( message ) ) {
				e.preventDefault();
			}

		} );

		<?php $js = ob_get_clean();

		wc_enqueue_js( $js );
	}


	/**
	 * Handles switching activated gateways from First Data Global Gateway and
	 * Payeezy Gateway/Payeezy, and vice-versa
	 *
	 * @since 3.0.0
	 */
	public function change_gateway() {

		// security check
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], $this->get_file() ) || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_redirect( wp_get_referer() );
			exit;
		}

		$valid_gateways = array(
			self::GLOBAL_GATEWAY_ID, self::PAYEEZY_GATEWAY_CREDIT_CARD_ID, self::PAYEEZY_CREDIT_CARD_GATEWAY_ID
		);

		if ( empty( $_GET['gateway'] ) || ! in_array( $_GET['gateway'], $valid_gateways, true ) ) {
			wp_redirect( wp_get_referer() );
			exit;
		}

		// switch the gateway
		update_option( 'wc_first_data_active_gateway', $_GET['gateway'] );

		$return_url = add_query_arg( array( 'gateway_switched' => 1 ), 'plugins.php' );

		// back to whence we came
		wp_redirect( $return_url );
		exit;
	}


	/** Admin methods *********************************************************/


	/**
	 * Adds a notice when gateways are switched
	 *
	 * @since 3.4.2
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// show a notice when switching between the gateways
		$this->add_gateway_switch_admin_notice();
	}


	/**
	 * Adds admin notices that are delayed under gateway settings can be loaded
	 *
	 * @since 4.0.0
	 * @see SV_WC_Payment_Gateway_Plugin::add_delayed_admin_notices()
	 */
	public function add_delayed_admin_notices() {

		parent::add_delayed_admin_notices();

		$this->add_settings_admin_notices();
	}


	/**
	 * Render a notice when switching between the gateways
	 *
	 * @since 3.4.2
	 */
	private function add_gateway_switch_admin_notice() {

		if ( isset( $_GET['gateway_switched'] ) ) {

			if ( $this->is_global_gateway_active() ) {
				$message = __( 'First Data Global Gateway is now active.', 'woocommerce-gateway-firstdata' );

			} elseif ( $this->is_payeezy_gateway_active() ) {
				$message = __( 'First Data Payeezy Gateway is now active.', 'woocommerce-gateway-firstdata' );

			} elseif ( $this->is_payeezy_active() ) {
				$message = __( 'First Data Payeezy is now active.', 'woocommerce-gateway-firstdata' );
			}

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'gateway-switched', array( 'dismissible' => false ) );
		}
	}


	/**
	 * Render settings-related admin notices, currently:
	 *
	 * + Global Gateway is PEM file readable
	 * + Payeezy Gateway Key ID/HMAC Key settings required
	 *
	 * @since 3.4.2
	 */
	private function add_settings_admin_notices() {

		if ( $this->is_global_gateway_active() ) {

			// check if the PEM file path entered is readable and render a notice if not
			if ( $this->is_payment_gateway_configuration_page( self::GLOBAL_GATEWAY_CLASS_NAME ) ) {

				$global_gateway_settings = $this->get_gateway_settings( self::GLOBAL_GATEWAY_ID );

				// check after store number and PEM file path have been entered
				if ( ! empty( $global_gateway_settings['store_number'] ) && ! empty( $global_gateway_settings['pem_file_path'] ) &&
					 'production' === $global_gateway_settings['environment'] && ! is_readable( $global_gateway_settings['pem_file_path'] ) )
				{
					$message = sprintf( __( '%1$sWooCommerce First Data Global Gateway requires additional configuration!%2$s The path entered for the First Data PEM file is either invalid or unreadable. Please ask your hosting provider for assistance with the correct file path. Need help? %3$sRead the documentation%4$s.', 'woocommerce-gateway-first-data' ),
						'<strong>', '</strong>',
						'<a href="http://docs.woocommerce.com/document/firstdata">', '</a>'
					);

					$this->get_admin_notice_handler()->add_admin_notice( $message, 'pem-file-path', array( 'dismissible' => false, 'notice_class' => 'error' ) );
				}
			}

		} elseif ( $this->is_payeezy_gateway_active() ) {

			// payeezy/payeezy gateway notices
			$payeezy_gateway_settings = $this->get_gateway_settings( self::PAYEEZY_GATEWAY_CREDIT_CARD_ID );

			// TODO: prevent this from showing when in demo mode
			if ( ! empty( $payeezy_gateway_settings['gateway_id'] ) && ! empty( $payeezy_gateway_settings['gateway_password'] ) &&
				 ( empty( $payeezy_gateway_settings['key_id'] ) || empty( $payeezy_gateway_settings['hmac_key'] ) ) ) {

				$message = sprintf( __( '%1$sWooCommerce First Data Payeezy Gateway requires additional configuration!%2$s You must %3$sconfigure the Key ID and HMAC Key settings%4$s for transaction security. %5$sRead the documentation%6$s to learn how.', 'woocommerce-gateway-first-data' ),
					'<strong>', '</strong>',
					'<a href="' . esc_url( $this->get_settings_url( self::PAYEEZY_GATEWAY_CREDIT_CARD_ID ) ) . '">', '</a>',
					'<a href="http://docs.woocommerce.com/document/firstdata#api-security">', '</a>'
				);

				$this->get_admin_notice_handler()->add_admin_notice( $message, 'key-hmac-upgrade', array( 'dismissible' => false, 'notice_class' => 'error' ) );
			}
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Main First Data Instance, ensures only one instance is/can be loaded
	 *
	 * @since 3.6.0
	 * @see wc_firstdata()
	 * @return WC_First_Data
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Gets the plugin documentation url, which for First Data is non-standard
	 *
	 * @since 3.2
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/firstdata/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 3.7.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/tickets/';
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.2
	 * @see SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce First Data', 'woocommerce-gateway-firstdata' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.2
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Handle installation tasks
	 *
	 * @since 4.0.0
	 */
	protected function install() {

		update_option( 'wc_first_data_active_gateway', self::PAYEEZY_GATEWAY_CREDIT_CARD_ID );

		// handle upgrades from pre v4.0.0 versions, as the plugin ID changed then
		// and the upgrade routine won't be triggered automatically
		if ( $old_version = get_option( 'wc_firstdata_version' ) ) {

			$this->upgrade( $old_version );
		}
	}


	/**
	 * Handles upgrades
	 *
	 * @since 3.0
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to v3.0.0
		$settings = get_option( 'woocommerce_firstdata-global-gateway_settings' );

		if ( ! $installed_version && $settings ) {
			// upgrading from the pre-rewrite version, need to adjust the settings array

			if ( isset( $settings['pemfile'] ) ) {
				// Global Gateway: the new global gateway id is firstdata-global-gateway, so
				//  we'll make that change and set the Global Gateway as the active version
				//  for a seamless "upgrade" from the previous standalone Global Gateway plugin

				// sandbox -> environment
				if ( isset( $settings['sandbox'] ) && 'yes' == $settings['sandbox'] ) {
					$settings['environment'] = 'sandbox';
				} else {
					$settings['environment'] = 'production';
				}
				unset( $settings['sandbox'] );

				// rename the settings option
				delete_option( 'woocommerce_firstdata-global-gateway_settings' );
				update_option( 'woocommerce_firstdata_settings', $settings );

				// Make the Global Gateway version active
				update_option( 'wc_firstdata_gateway', 'WC_Gateway_FirstData_Global_Gateway' );

			} else {
				// GGe4

				// logger -> debug_mode
				if ( ! isset( $settings['logger'] ) || 'no' == $settings['logger'] ) {
					$settings['debug_mode'] = 'off';
				} elseif ( isset( $settings['logger'] ) && 'yes' == $settings['logger'] ) {
					$settings['debug_mode'] = 'log';
				}
				unset( $settings['logger'] );

				// set demo fields
				if ( isset( $settings['environment'] ) && 'demo' == $settings['environment'] ) {
					$settings['demo_gateway_id']       = $settings['gateway_id'];
					$settings['demo_gateway_password'] = $settings['gateway_password'];

					$settings['gateway_id']       = '';
					$settings['gateway_password'] = '';
				}

				// set the updated options array
				update_option( 'woocommerce_firstdata_settings', $settings );
			}
		}

		// upgrade to v3.1.1
		if ( -1 === version_compare( $installed_version, '3.1.1' ) && $settings ) {

			// standardize transaction type setting: '00' => 'purchase', '01' => 'authorization'
			if ( isset( $settings['transaction_type'] ) ) {
				if ( '01' == $settings['transaction_type'] ) {
					$settings['transaction_type'] = 'authorization';
				} else {
					$settings['transaction_type'] = 'charge';
				}
			}

			// set the updated options array
			update_option( 'woocommerce_firstdata_settings', $settings );

		}


		// upgrade to v4.0.0
		if ( version_compare( $installed_version, '4.0.0', '<' ) ) {
			global $wpdb;

			$this->log( 'Starting upgrade to v4.0.0' );

			if ( 'WC_Gateway_FirstData_Global_Gateway' === get_option( 'wc_firstdata_gateway' ) ) {

				/** Upgrade Global Gateway */
				$this->log( 'Starting Global Gateway upgrade.' );


				// update switcher option
				update_option( 'wc_first_data_active_gateway', self::GLOBAL_GATEWAY_ID );
				delete_option( 'wc_firstdata_gateway' );

				$old_settings = get_option( 'woocommerce_firstdata-global-gateway_settings' );

				if ( $old_settings ) {

					$new_settings = array(
						'enabled'               => ( isset( $old_settings['enabled'] ) && 'yes' === $old_settings['enabled'] ) ? 'yes' : 'no',
						'title'                 => ( ! empty( $old_settings['title'] ) ) ? $old_settings['title'] : __( 'Credit Card', 'woocommerce-gateway-firstdata' ),
						'description'           => ( ! empty( $old_settings['description'] ) ) ? $old_settings['description'] : __( 'Pay securely using your credit card.', 'woocommerce-gateway-firstdata' ),
						'enable_csc'            => 'yes',
						'transaction_type'      => 'charge',
						'card_types'            => array( 'VISA', 'MC', 'AMEX', 'DISC' ),
						'debug_mode'            => 'off',
						'environment'           => ( isset( $old_settings['environment'] ) && 'sandbox' === $old_settings['environment'] ) ? 'staging' : 'production',
						'store_number'          => ( ! empty( $old_settings['storenum'] ) ) ? $old_settings['storenum'] : '',
						'pem_file_path'         => ( ! empty( $old_settings['pemfile'] ) ) ? $old_settings['pemfile'] : '',
						'staging_store_number'  => ( isset( $old_settings['environment'] ) && 'sandbox' === $old_settings['environment'] && ! empty( $old_settings['storenum'] ) ) ? $old_settings['storenum'] : '',
						'staging_pem_file_path' => ( isset( $old_settings['environment'] ) && 'sandbox' === $old_settings['environment'] && ! empty( $old_settings['pemfile'] ) ) ? $old_settings['pemfile'] : '',
					);

					// save new settings, remove old ones
					update_option( 'woocommerce_first_data_global_gateway_settings', $new_settings );
					delete_option( 'woocommerce_firstdata-global-gateway_settings' );

					$this->log( 'Settings upgraded.' );
				}

			} else {

				/** Upgrade GGe4/Payeezy Gateway */
				$this->log( 'Starting Payeezy Gateway upgrade.' );

				// remote old switcher option
				delete_option( 'wc_firstdata_gateway' );

				$old_settings = get_option( 'woocommerce_firstdata_settings' );

				if ( $old_settings ) {

					$new_settings = array(
						'enabled'                               => ( isset( $old_settings['enabled'] ) && 'yes' === $old_settings['enabled'] ) ? 'yes' : 'no',
						'title'                                 => ( ! empty( $old_settings['title'] ) ) ? $old_settings['title'] : __( 'Credit Card', 'woocommerce-gateway-firstdata' ),
						'description'                           => ( ! empty( $old_settings['description'] ) ) ? $old_settings['description'] : __( 'Pay securely using your credit card.', 'woocommerce-gateway-firstdata' ),
						'enable_csc'                            => 'yes', // old version required it by default, with no option to disable
						'transaction_type'                      => ( isset( $old_settings['transaction_type'] ) && 'authorization' === $old_settings['transaction_type'] ) ? 'authorization' : 'charge',
						'partial_redemption'                    => ( isset( $old_settings['partial_redemption'] ) && 'yes' === $old_settings['partial_redemption'] ) ? 'yes' : 'no',
						'card_types'                            => ( isset( $old_settings['card_types'] ) && is_array( $old_settings['card_types'] ) ) ? $old_settings['card_types'] : array( 'VISA', 'MC', 'AMEX', 'DISC', ),
						'tokenization'                          => ( isset( $old_settings['tokenization'] ) && 'yes' === $old_settings['tokenization'] ) ? 'yes' : 'no',
						'enable_customer_decline_messages'      => 'no',
						'debug_mode'                            => ( ! empty( $old_settings['debug_mode'] ) ) ? $old_settings['debug_mode'] : 'off',
						'environment'                           => ( isset( $old_settings['environment'] ) && 'demo' === $old_settings['environment'] ) ? 'demo' : 'production',
						'inherit_settings'                      => 'no',
						'gateway_id'                            => ( ! empty( $old_settings['gateway_id'] ) ) ? $old_settings['gateway_id'] : '',
						'gateway_password'                      => ( ! empty( $old_settings['gateway_password'] ) ) ? $old_settings['gateway_password'] : '',
						'key_id'                                => ( ! empty( $old_settings['key_id'] ) ) ? $old_settings['key_id'] : '',
						'hmac_key'                              => ( ! empty( $old_settings['hmac_key'] ) ) ? $old_settings['hmac_key'] : '',
						'demo_gateway_id'                       => ( ! empty( $old_settings['demo_gateway_id'] ) ) ? $old_settings['demo_gateway_id'] : '',
						'demo_gateway_password'                 => ( ! empty( $old_settings['demo_gateway_password'] ) ) ? $old_settings['demo_gateway_password'] : '',
						'demo_key_id'                           => ( ! empty( $old_settings['demo_key_id'] ) ) ? $old_settings['demo_key_id'] : '',
						'demo_hmac_key'                         => ( ! empty( $old_settings['demo_hmac_key'] ) ) ? $old_settings['demo_hmac_key'] : '',
						'soft_descriptors_enabled'              => ( isset( $old_settings['soft_descriptors_enabled'] ) && 'yes' === $old_settings['soft_descriptors_enabled'] ) ? 'yes' : 'no',
						'soft_descriptor_dba_name'              => ( ! empty( $old_settings['soft_descriptor_dba_name'] ) ) ? $old_settings['soft_descriptor_dba_name'] : '',
						'soft_descriptor_street'                => ( ! empty( $old_settings['soft_descriptor_street'] ) ) ? $old_settings['soft_descriptor_street'] : '',
						'soft_descriptor_city'                  => ( ! empty( $old_settings['soft_descriptor_city'] ) ) ? $old_settings['soft_descriptor_city'] : '',
						'soft_descriptor_region'                => ( ! empty( $old_settings['soft_descriptor_region'] ) ) ? $old_settings['soft_descriptor_region'] : '',
						'soft_descriptor_postal_code'           => ( ! empty( $old_settings['soft_descriptor_postal_code'] ) ) ? $old_settings['soft_descriptor_postal_code'] : '',
						'soft_descriptor_country_code'          => ( ! empty( $old_settings['soft_descriptor_country_code'] ) ) ? $old_settings['soft_descriptor_country_code'] : '',
						'soft_descriptor_mid'                   => ( ! empty( $old_settings['soft_descriptor_mid'] ) ) ? $old_settings['soft_descriptor_mid'] : '',
						'soft_descriptor_mcc'                   => ( ! empty( $old_settings['soft_descriptor_mcc'] ) ) ? $old_settings['soft_descriptor_mcc'] : '',
						'soft_descriptor_merchant_contact_info' => ( ! empty( $old_settings['soft_descriptor_merchant_contact_info'] ) ) ? $old_settings['soft_descriptor_merchant_contact_info'] : '',
					);

					// save new settings, remove old ones
					update_option( 'woocommerce_first_data_payeezy_gateway_credit_card_settings', $new_settings );
					delete_option( 'woocommerce_firstdata_settings' );

					$this->log( 'Settings upgraded.' );
				}

				/** Update meta values for order payment method & recurring payment method */

				// meta key: _payment_method
				// old value: firstdata
				// new value: first_data_payeezy_gateway_credit_card
				$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'first_data_payeezy_gateway_credit_card' ), array( 'meta_key' => '_payment_method', 'meta_value' => 'firstdata' ) );

				$this->log( sprintf( '%d orders updated for payment method meta', $rows ) );

				// meta key: _recurring_payment_method
				// old value: firstdata
				// new value: first_data_payeezy_gateway_credit_card
				$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'first_data_payeezy_gateway_credit_card' ), array( 'meta_key' => '_recurring_payment_method', 'meta_value' => 'firstdata' ) );

				$this->log( sprintf( '%d orders updated for recurring payment method meta', $rows ) );

				/** Convert tokens stored in legacy format to framework payment token format */

				$this->log( 'Starting legacy token upgrade.' );

				$user_ids = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '_wc_firstdata_credit_card_tokens'" );

				if ( $user_ids ) {

					// iterate through each user with tokens
					foreach ( $user_ids as $user_id ) {

						$old_tokens = get_user_meta( $user_id, '_wc_firstdata_credit_card_tokens', true );

						$new_tokens = array();

						// iterate through each token
						foreach ( $old_tokens as $token_id => $token ) {

							// sanity check
							if ( ! $token_id || empty( $token ) ) {
								continue;
							}

							// parse expiry date
							if ( ! empty( $token['exp_date'] ) && 4 === strlen( $token['exp_date'] ) ) {
								$exp_month = substr( $token['exp_date'], 0, 2 );
								$exp_year  = substr( $token['exp_date'], 2, 2 );
							} else {
								$exp_month = $exp_year = '';
							}

							// parse card type
							switch ( $token['type'] ) {
								case 'Visa':             $card_type = 'visa';     break;
								case 'American Express': $card_type = 'amex';     break;
								case 'Mastercard':       $card_type = 'mc';       break;
								case 'Discover':         $card_type = 'discover'; break;
								case 'Diners Club':      $card_type = 'diners';   break;
								case 'JCB':              $card_type = 'jcb';      break;
								default:                 $card_type = '';
							}

							// setup new token
							$new_tokens[ $token_id ] = array(
								'type'                => 'credit_card',
								'last_four'           => ! empty( $token['last_four'] ) ? $token['last_four'] : '',
								'card_type'           => $card_type,
								'exp_month'           => $exp_month,
								'exp_year'            => $exp_year,
								'default'             => ( ! empty( $token['active'] ) && $token['active'] ),
							);
						}

						// save new tokens
						if ( ! empty( $new_tokens ) ) {
							update_user_meta( $user_id, '_wc_first_data_payeezy_gateway_credit_card_payment_tokens', $new_tokens );
						}

						// save the legacy tokens in case we need them later
						// TODO: the legacy tokens can be removed in future version, say September 2016 @MR 2016-02-10
						update_user_meta( $user_id, '_wc_first_data_payeezy_gateway_legacy_payment_tokens', $old_tokens );
						delete_user_meta( $user_id, '_wc_firstdata_credit_card_tokens' );

						$this->log( sprintf( 'Converted legacy payment tokens for user ID: %d', absint( $user_id ) ) ) ;
					}

					$this->log( 'Completed legacy payment token upgrade.' );
				}
			}

			$this->log( 'Completed upgrade for v4.0.0' );
		}
	}


} // end WC_FirstData


/**
 * Returns the One True Instance of First Data, deprecated due to the incorrect
 * naming
 *
 * @since 3.6.0
 * @deprecated 4.0.0
 * @return \WC_First_Data
 */
function wc_firstdata() {

	_deprecated_function( __FUNCTION__, '4.0.0', 'wc_first_data()' );

	return wc_first_data();
}

/**
 * Returns the One True Instance of First Data
 *
 * @since 4.0.0
 * @return \WC_First_Data
 */
function wc_first_data() {
	return WC_First_Data::instance();
}

// fire it up!
wc_first_data();

} // init_woocommerce_gateway_first_data()
