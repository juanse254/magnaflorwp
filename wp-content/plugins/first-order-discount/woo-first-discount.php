<?php
/**
 * Plugin Name: First Order Discount
 * Plugin URI: http://fmrfox.com/first-order-discount/
 * Description: This plugin add some discount for authorized customer when they make first order
 * Version: 1.0.5
 * Author: Andriyan Anton
 * Author URI: http://fmrfox.com
 * Text Domain: woo-first-discount
 * Domain Path: /lang
 * License: GPL2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'first_order_add_add_admin_menu' );
add_action( 'admin_init', 'first_order_add_settings_init' );

/* Include translations */
function first_order_add_load_textdomain() {
	load_plugin_textdomain( 'woo-first-discount', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action('plugins_loaded', 'first_order_add_load_textdomain');

/* Admin sttings page */
function first_order_add_add_admin_menu(  ) { 
	add_submenu_page( 'woocommerce', 'First Order Discount', 'First Order Discount', 'manage_options', 'woocomerce_first_order_discount', 'first_order_add_options_page' );
}

function first_order_add_settings_init(  ) { 
	register_setting( 'pluginPage', 'first_order_add_settings' );
	add_settings_section( 'first_order_add_pluginPage_section', __( 'Choose your setting', 'woo-first-discount' ), '', 'pluginPage' );
	add_settings_field( 'first_order_choose', __( 'Choose discount type', 'woo-first-discount' ), 'first_order_choose_render', 'pluginPage', 'first_order_add_pluginPage_section' );
	add_settings_field( 'first_order_add_value',  __( 'Choose discount value', 'woo-first-discount' ), 'first_order_add_value_render', 'pluginPage',	'first_order_add_pluginPage_section' );
}

function first_order_choose_render(  ) { 
	$options = get_option( 'first_order_add_settings' );
	?>
	<input id="off" type='radio' name='first_order_add_settings[first_order_choose]' <?php checked( $options['first_order_choose'], 'off' ); ?> value='off'>
	<label for="off"><?php echo __( 'Disable first order discount', 'woo-first-discount' ); ?></label>
	<br>
	<input id="fixed" type='radio' name='first_order_add_settings[first_order_choose]' <?php checked( $options['first_order_choose'], 'fixed' ); ?> value='fixed'>
	<label for="fixed"><?php echo __( 'Fixed discount', 'woo-first-discount' ); ?></label>
	<br>
	<input id="percent" type='radio' name='first_order_add_settings[first_order_choose]' <?php checked( $options['first_order_choose'], 'percent' ); ?> value='percent'>
	<label for="percent"><?php echo __( 'Percent discount', 'woo-first-discount' ); ?></label>
	<?php
}

function first_order_add_value_render(  ) { 
	$options = get_option( 'first_order_add_settings' );
	?>
	<input type='number' min="0" name='first_order_add_settings[first_order_add_value]' value='<?php echo $options['first_order_add_value']; ?>'>
	<?php
}

function first_order_add_options_page(  ) { 
	?>
	<form action='options.php' method='post'>
		<h2><?php echo __( 'First Order Discount', 'woo-first-discount' ); ?></h2>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
	<?php
}

/* Discount */
function first_order_add_fee() {
	global $wpdb, $woocommerce;
	if ( is_user_logged_in() ) {
		$customer_id = get_current_user_id();
		$orderNumCheck = wc_get_customer_order_count( $customer_id ); // count orders by current customer
		$options = get_option( 'first_order_add_settings' );
		$discountType = $options['first_order_choose'];
		$discountValue = $options['first_order_add_value'];

		if ($orderNumCheck == 0 and $discountType != 'off') { // if first order by user
			$subtotal = WC()->cart->cart_contents_total;
			if ($discountType == 'fixed') {
				WC()->cart->add_fee( 'Fee', -$discountValue );
			} else {
				$discount = $discountValue/100;
	    		WC()->cart->add_fee( 'Fee', -$subtotal*$discount );
			}
		} else {
			WC()->cart->add_fee( 'Fee', 0 );
		}
	}
}

add_action( 'woocommerce_cart_calculate_fees','first_order_add_fee' );
?>