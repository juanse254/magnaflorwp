<?php
/*
 	* Plugin Name:			Woocommerce CSV import downloadable, external, grouped products
 	* Plugin URI:			http://allaerd.org/shop/import-downloadable-external-grouped-products/
 	* Description:			Import downloadable, external, grouped products into Woocommerce

 	* Author:				Allaerd Mensonides
 	* Author URI:			https://allaerd.org
 	
 	* Version:				3.0.1
	* Requires at least: 	4.0
	* Tested up to: 		4.2
	
	* Text Domain: woocsv
	* Domain Path: /i18n/languages/
	 
	This plugin is part of the free woocommerce csv importer. It must be used in conjunction with it.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//add old hook
add_action('woocsvAfterInit', 	'woocsv_import_product_types_init_old');

// hook into woocommerce csv import 
add_action('woocsv_after_init', 'woocsv_import_product_types_init');

function woocsv_import_product_types_init () {
	new woocsv_import_product_types ();
}

function woocsv_import_product_types_init_old () {
	include_once 'old/class-woocsv-premium-old.php';
	new woocsvPremium_old ();
}

class woocsv_import_product_types {
	
	public $version;
	public $name;
	public $remote_slug;
	public $url;
	
	public $extra_fields = array (
		'product_type', // simple, grouped, external
		'post_parent', // SKU of group parent
		'button_text', // button text for external product
		'product_url', // URL for external product
		'sold_individually', //yes,no
		'sale_price_dates_to', // YYYYMMDD
		'sale_price_dates_from', // YYYYMMDD
		'downloadable', // yes,no
		'download_limit', //number of downloads
		'download_expiry', // number of days download link is available
		'crosssell_sku', // a:2:{i:0;s:5:"28643";i:1;s:5:"28644";}
		'upsell_sku', // a:2:{i:0;s:5:"28643";i:1;s:5:"28644";}
		'purchase_note',
		'file_names', //file name
		'file_urls', //absolute or URL
		'download_type', // '', music, application
		'virtual', //yes, no
	);

	public function __construct() {
		global $woocsv_import;
			
		/* register add-on */
		$this->version  = '3.0.1';
		$this->name = 'Import downloadable, external, grouped products';
		$this->url = 'http://allaerd.org/shop/import-downloadable-external-grouped-products/';
		$this->remote_slug = 'woocsv-product-types';
		$woocsv_import->addons[$this->remote_slug] = $this;

		/* save hook */		
		add_action('woocsv_product_after_fill_in_data',array($this,'save'));

		/* docs */
		add_action ('woocsv_documentation',array ( $this, 'content' ) );

		/* add fields to dropdown */
		$this->fields();
	}

	public function fields() {
		global $woocsv_import;

		foreach ($this->extra_fields as $value) {
			$woocsv_import->fields[] = $value;
		}
	}

	public function save() {
		global $wpdb,$woocsv_product,$woocsv_import;
	
		/* ! Downloadable products */
		$key = array_search( 'downloadable' , $woocsv_import->header );
		if ($key !== FALSE ) {
			$value = $woocsv_product->raw_data[$key];
			if ( in_array( $value, array( 'yes', 'no' ) ) ) {
				$woocsv_import->meta['_downloadable'] = $value;
			} else {
				$woocsv_import->import_log[] = "downloadable changed from $value to no";
			}
		}

		$key = array_search( 'download_type' , $woocsv_import->header ); 
		if ($key !== FALSE) {
			$value = $woocsv_product->raw_data[$key];
			if (in_array($value, array ('','application','music'))) {
				$woocsv_import->import_log[]= "download_type changed from $value to standard";
				$value = '';
			}
			$woocsv_product->meta['_download_type'] = $woocsv_product->raw_data[$key];
		}
		
		$key = array_search( 'file_names' , $woocsv_import->header ); 
		if ( $key !== FALSE && !empty ( $woocsv_product->raw_data[$key]) ) {
			$file_names    = explode('|',$woocsv_product->raw_data[$key]);
		} else {
			$file_names = array ();
		}
		
		$key = array_search( 'file_urls' , $woocsv_import->header ); 
		if ( $key !== FALSE && !empty ( $woocsv_product->raw_data[$key] ) ) {
			$file_urls    = explode('|',$woocsv_product->raw_data[$key]);
		} else {
			$file_urls = array ();
		}
		
		//which is the longest?
		$loop_count =  ( count( $file_names ) <= count ( $file_urls ) ) ? count($file_names) : count ( $file_urls );
		$files = array ();
		
		//loop throuh both and fill the array
		for ( $i = 0; $i < $loop_count; $i ++ ) {
			if ( ! empty( $file_urls[ $i ] ) )
				$files[ md5( $file_urls[ $i ] ) ] = array(
					'name' => $file_names[ $i ],
					'file' => $file_urls[ $i ]
				);
		}

				
		$woocsv_product->meta['_downloadable_files'] =  $files ;	


		/* ! Grouped products */

		$key = array_search( 'product_type' , $woocsv_import->header );
		if ($key !== FALSE && $woocsv_product->raw_data[$key] == 'grouped_master' ) {
			$woocsv_product->product_type = 'grouped';
		}

		$key = array_search( 'post_parent' , $woocsv_import->header );
		if ( $key !== FALSE ) {
			$parent_sku = $woocsv_product->raw_data[$key];
			$parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id
						FROM $wpdb->postmeta
						WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $parent_sku )
			);
			
			if ($parent_id) {
				$woocsv_product->body['post_parent'] = $parent_id;
			}
		}		


		/* ! External product */
		
		$key = array_search( 'product_type' , $woocsv_import->header );
		if ($key !== FALSE && $woocsv_product->raw_data[$key] == 'external' ) {
			$woocsv_product->product_type = 'external';
		}
		
		//button text
		$key = array_search( 'button_text' , $woocsv_import->header );
		if ($key !== FALSE) {
			$woocsv_product->meta['_button_text'] = $woocsv_product->raw_data[$key];
		}
						
		//product URL
		$key = array_search( 'product_url' , $woocsv_import->header );
		if ($key !== FALSE) {
			$woocsv_product->meta['_product_url'] = $woocsv_product->raw_data[$key];
		}

		/* ! other fields */ 
		
		// sales dates
		$key = array_search( 'sale_price_dates_from' , $woocsv_import->header );
		if ($key !== FALSE ) {
			$woocsv_product->meta['_sale_price_dates_from'] = @strtotime( $woocsv_product->raw_data[$key] );
		}

		$key = array_search( 'sale_price_dates_to' , $woocsv_import->header );
		if ($key !== FALSE ) {
			$woocsv_product->meta['_sale_price_dates_to'] = @strtotime( $woocsv_product->raw_data[$key] );	
		}	
		
		// cross sell
		$key = array_search( 'crosssell_sku' , $woocsv_import->header );
		$cross_ids = '';
		if ($key !== FALSE && !empty ( $woocsv_product->raw_data[ $key ] ) ) {
			$cross_skus = explode ('|',$woocsv_product->raw_data[ $key ]);
			
			foreach ($cross_skus as $cross_sku) {
				$parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id
						FROM $wpdb->postmeta
						WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $cross_sku )
						);
				if ($parent_id)
					$cross_ids[] = $parent_id;
			}
			$woocsv_product->meta['_crosssell_ids'] = $cross_ids;			
		}

		//up sell
		$key = array_search( 'upsell_sku' , $woocsv_import->header );
		$upsell_ids = array();
		if ($key !== FALSE && !empty ( $woocsv_product->raw_data[ $key ] ) ) {
			$upsell_skus = explode ('|',$woocsv_product->raw_data[ $key ]);
			
			foreach ($upsell_skus as $upsell_sku) {
				$parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id
						FROM $wpdb->postmeta
						WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $upsell_sku )
						);
				if ($parent_id)
					$upsell_ids[] = $parent_id;
			}
			$woocsv_product->meta['_upsell_ids'] = $upsell_ids;			
		}
		
		// purchase note
		$key = array_search( 'purchase_note' , $woocsv_import->header );
		if ($key !== FALSE) {
			$woocsv_product->meta['_purchase_note'] = $woocsv_product->raw_data[$key];
		}
		
		$key = array_search( 'sold_individually' , $woocsv_import->header );
		if ($key !== FALSE) {
			$value = $woocsv_product->raw_data[$key];
			if (!in_array($value, array ('yes','no'))) {
				$woocsv_import->import_log[]= "sold_individually changed from $value to no";
				$value = 'no';
			}
			$woocsv_product->meta['_sold_individually'] = $woocsv_product->raw_data[$key];
		}
		
		$key = array_search( 'virtual' , $woocsv_import->header );
		if ($key !== FALSE) {
			$value = $woocsv_product->raw_data[$key];
			if (!in_array($value, array ('yes','no'))) {
				$woocsv_import->import_log[]= "virtual changed from $value to no";
				$value = 'no';
			}
			$woocsv_product->meta['_virtual'] = $woocsv_product->raw_data[$key];
		}

	}

	function content () {
?>
		<h2>Import downloadable, external, grouped products</h2>
		<p class="description">
		With this add-on you can import additional fields and product types. The fields and product types you can import are:
		</p>

		<h4>Downloadable products</h4>
			<p class="description">downloadable <code>yes,no</code></p>
			<p class="description">download_limit, number of times the user can download the file</p>
			<p class="description">download_expiry, number of days the download link is valid</p>
			<p class="description">file_names, pipe separated list of filenames <code>name1|name2</code> works together with file_urls</p>
			<p class="description">file_urls, pipe separated list of file paths, may contain urls's or absolute paths <code>/var/www/path1/file1.zip|http://www.example.nl/file2.zip</code></p>
			<p class="description">
				download_type can have the following values: <code> empty string for standard, application ,music</code>
			</p>
		<h4>Grouped Products</h4>
			<p class="description">product_type, the product types used for grouped products <code>grouped_master</code></p>
			<p class="description">post_parent, the sku of the grouped master used to attaches simple products to grouped products.</p>
		<h4>External/Affiliate products</h4>
			<p class="description">product_type the product type for external products<code>external</code></p>
			<p class="description">button_text, button text for external product</p>
			<p class="description">product_url, URL for external product</p>
		<h4>Other fields</h4>
			<p class="description">sold_individually, if the product is sold individual or not <code>yes,no</code></p>
			<p class="description">sale_price_dates_to, the from date for sales <code>20140115 (YYYYMMDD)</code></p>
			<p class="description">sale_price_dates_from, the till date for sales <code>20140130 (YYYYMMDD)</code></p>
			<p class="description">crossell_sku, pipe separated list of SKU's of the cros sell products <code>sku1|sku2|sku3</code>. They must already exists!</p>
			<p class="description">upsell_sku pipe separated list of SKU's of the upsell products <code>sku1|sku2|sku3</code>. They must already exists!</p>
			<p class="description">purchase_note, custom purchase note for this product</p>
		<?php
	}
}

