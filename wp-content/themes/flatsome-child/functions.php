<?php
// Add custom Theme Functions here

/**
 * @snippet       Disable Variable Product Price Range
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/disable-variable-product-price-range-woocommerce/
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.7
 */
 
add_filter( 'woocommerce_variable_sale_price_html', 'bbloomer_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'bbloomer_variation_price_format', 10, 2 );
 
function bbloomer_variation_price_format( $price, $product ) {
 
// Main Price
$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
$price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
// Sale Price
$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
sort( $prices );
$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
if ( $price !== $saleprice ) {
$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
}
return $price;
}

// Remove passworx strenght
function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 1;
}
add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' );


//rearrange Products woocommerce

function shuffle_variable_product_elements(){
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );
        if ( $product->is_type( 'variable' ) ) {
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
            add_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation', 20 );
        }
    }
}
add_action( 'woocommerce_before_single_product', 'shuffle_variable_product_elements' );

//Validate Date is selected
function so_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {

    // do your validation, if not met switch $passed to false
    if (empty($_POST['delivery_calender_lite']) ){
        $passed = false;
        wc_add_notice( __( 'Please select a Delivery Date', 'textdomain' ), 'error' ); //TODO Quitar la notificacion de wc y poner una decente en javascript (con hooks).
        print('<script type="text/javascript"> 
            alert("Please Select a Delivery Date");
            </script>');
    }
    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 5 );

//Add disscount for first time visitors

function popup_first_time(){
    wp_enqueue_style( 'jqueryuicss', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.min.css', array(), '1.11.3' );
    wp_enqueue_script( 'jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), 3.3, true);
    wp_enqueue_script('popup', '/wp-content/themes/flatsome-child/js/popup.js', array(), '');
}

add_action('wp_enqueue_scripts', 'popup_first_time');

// async javascript loads

function add_async_attribute($tag, $handle) {
    // agregar los handles de los scripts en el array
    $scripts_to_async = array('jqueryui', 'popup','jquery', 'jquery-migrate');

    foreach($scripts_to_async as $async_script) {
        if ($async_script === $handle) {
            return str_replace(' src', ' async defer src', $tag);
        }
    }
    return $tag;
}
add_filter('script_loader_tag', 'add_async_attribute', 10, 2);


//ADD FAVICONS

add_action('wp_head', 'favicons_add');

function favicons_add(){

}
