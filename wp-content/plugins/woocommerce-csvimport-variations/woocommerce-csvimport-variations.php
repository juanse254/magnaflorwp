<?php
/*
 	Plugin Name:			Woocommerce CSV import variable products
 	Plugin URI:				https://allaerd.org/shop/woocommerce-import-variable-products/
 	Description:			Import variable products into Woocommerce

 	Author:					Allaerd Mensonides
 	Author URI:				https://allaerd.org
 	
 	Version:				3.1.5
 	Version:				3.1.5
	Requires at least: 		4.0
	Tested up to: 			4.4
	
	Text Domain: woocommerce-csvimport-variations
	Domain Path: /languages
	 
	This plugin is part of the free woocommerce csv importer. It must be used in conjunction with it.
*/


// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// hook into woocommerce csv import 
add_action('woocsv_after_init', 'woocsv_import_variations_init');


function woocsv_import_variations_init()
{
    new woocsv_import_variations();
}

load_plugin_textdomain('woocommerce-csvimport-variations', false, dirname(plugin_basename(__FILE__)) . '/languages/');

class woocsv_import_variations
{

    public $version;
    public $name;
    public $remote_slug;
    public $url;

    public function __construct()
    {
        global $woocsv_import;

        $this->version = '3.1.5';
        $this->name = 'Import variations';
        $this->url = 'https://allaerd.org/shop/woocommerce-import-variable-products/';
        $this->remote_slug = 'woocsv-variations';

        $this->registerActions();

        $woocsv_import->addons[ $this->remote_slug ] = $this;


        //populate the fields for the dropdowns in the header section
        $this->fields();
    }

    public function registerActions()
    {

        add_action('admin_init', array ($this, 'settings'));

        add_action('woocsv_documentation', array ($this, 'content'));

        add_action('woocsv_product_after_body_save', array ($this, 'save'), 200);

        add_action('woocsv_product_after_fill_in_data', array ($this, 'check_product_type'), 200);
    }

    public function fields()
    {
        global $wpdb, $woocsv_import;

        //default fields
        $woocsv_import->fields[] = 'product_type';
        $woocsv_import->fields[] = 'post_parent';
        $woocsv_import->fields[] = 'variations';
        $woocsv_import->fields[] = 'default_attributes';
        $woocsv_import->fields[] = 'variation_description';

        //attributes
        $attributes = $wpdb->get_results("select * from {$wpdb->prefix}woocommerce_attribute_taxonomies");
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $woocsv_import->fields[] = 'pa_' . $attribute->attribute_name;
            }
        }

        //manual attributes
        $woocsv_import->fields[] = 'manual_variations';
        $woocsv_import->fields[] = 'manual_default_attributes';

        //get the manual attributes
        $temp_manual_attributes = get_option('woocsv_manual_variations');
        if ($temp_manual_attributes) {
            $manual_attributes = explode(',', $temp_manual_attributes);
            foreach ($manual_attributes as $manual_attribute) {
                $woocsv_import->fields[] = 'ma_' . sanitize_title($manual_attribute);
            }
        }

        //
        $woocsv_import->fields[] = 'identifier';
        $woocsv_import->fields[] = 'parent_identifier';
    }

    /* settings */

    public function check_product_type()
    {
        global $woocsv_product;

        //Do we have a product_type column?
        $key = array_search('product_type', $woocsv_product->header);
        if ($key === false) {
            return;
        }

        $type = trim($woocsv_product->raw_data[ $key ]);

        if ($type == 'product_variation') {
            $woocsv_product->body[ 'post_type' ] = 'product_variation';
            $woocsv_product->body[ 'post_title' ] = '';
        }

    }

    function settings()
    {
        add_settings_field('woocsv_manual_variations', __('Manual variation attributes', 'woocommerce-csvimport'), array ($this, 'manual_variations'), 'woocsv-settings', 'woocsv-settings');
        register_setting('woocsv-settings', 'woocsv_manual_variations', array ($this, 'options_validate'));

        add_settings_field('woocsv_parent_selector', __('How do we find the variation parent', 'woocommerce-csvimport'), array ($this, 'parent_selector'), 'woocsv-settings', 'woocsv-settings');
        register_setting('woocsv-settings', 'woocsv_parent_selector', array ($this, 'options_validate'));
    }

    public function parent_selector()
    {
        $parent_selector = get_option('woocsv_parent_selector');
        echo '<select id="debug" name="woocsv_parent_selector">';
        echo '<option ' . selected("0", $parent_selector) . ' value="sku">' . __('SKU', 'woocommerce-csvimport') . '</option>';
        echo '<option ' . selected("1", $parent_selector) . ' value="identifier">' . __('Own Identifier', 'woocommerce-csvimport') . '</option>';
        echo '</select>';
        echo '<p class="description">' . __('In the case where you do not want you master products to have a SKU, you can select your own identifier.', 'woocommerce-csvimport') . '</p>';
    }


    // validation
    function manual_variations()
    {
        $custom_fields = get_option('woocsv_manual_variations');
        echo '<input type="text" class="large-text" id="woocsv_manual_variations" name="woocsv_manual_variations" placeholder="field1,field2,field3" value="' . $custom_fields . '">';
        echo '<p class="description">' . __('Add your manual variation attributes as a comma separated list.', 'woocommerce-csvimport') . '</p>';
    }

    function options_validate($input)
    {
        //no validation yet
        return $input;
    }

    public function save()
    {
        global $woocsv_product;

        //Do we have a product_type column?
        $key = array_search('product_type', $woocsv_product->header);

        //if no retrurn
        if ($key === false) {
            return;
        }

        //we have a product_type, now lets split between variation_master and product_variation
        $type = trim($woocsv_product->raw_data[ $key ]);

        if ($type == 'variation_master') {
            //handle master
            $this->handle_master();
        } elseif ($type == 'product_variation') {
            //handle variation
            $this->handle_variation();
        } else {
            //somehow the product type does not match.
            return;
        }
    }

    public function handle_master()
    {
        global $woocsv_product;
        $woocsv_product->log[] = __('It is a variable master product', 'woocommerce-csvimport');

        //set product type to variable
        $woocsv_product->product_type = 'variable';

        //Handle default attributes
        $this->handle_default_attributes();
        $this->handle_manual_default_attributes();

        // attach the attributes to the master product
        $this->attach_attributes_to_master();
        $this->attach_manual_attributes_to_master();

        //handle unique indetifieres
        //$this->handle_master_unique_identifier ();
    }

    public function handle_master_unique_identifier()
    {
        global $woocsv_product;

        //return if the key is not in
        $key = array_search('identifier', $woocsv_product->header);
        if ($key === false) {
            return;
        }

        //save the identifier as custom field for later use for the childs
        $woocsv_product->meta[ 'identifier' ] = $woocsv_product->raw_data[ $key ];
    }

    public function handle_default_attributes()
    {
        global $woocsv_product;

        //are there default attributes?
        $key = array_search('default_attributes', $woocsv_product->header);
        if ($key === false) {
            $woocsv_product->log[] = __('No default attributes', 'woocommerce-csvimport');

            return;
        }

        //handle the product default values
        $defaults = explode('|', $woocsv_product->raw_data[ $key ]);

        // 	has no values
        if (empty ($woocsv_product->raw_data[ $key ])) {
            return;
        }

        $product_attributes_default = '';


        //loop through them, create them if necessary
        foreach ($defaults as $default) {
            list($key, $value) = array_pad(explode('->', $default), 2, null);

            //check if the taxonomy exists
            $taxonomy = taxonomy_exists('pa_' . $key);

            //check if the term exists
            $term = term_exists($value, 'pa_' . $key);

            //as of 2.0.1 check input on empty value
            if ($term !== 0 && $term !== null) {
                //do nothing
            } else {
                //the term did not exists so create it
                $term = wp_insert_term($value, 'pa_' . $key);
            }

            //if both exists link the them to the product
            if ($taxonomy && $term) {
                $term = get_term($term[ 'term_id' ], 'pa_' . $key);
                $product_attributes_default[ 'pa_' . $key ] = $term->slug;
            }

        }

        $woocsv_product->meta[ '_default_attributes' ] = $product_attributes_default;

    }

    public function handle_manual_default_attributes()
    {
        global $woocsv_product;

        //are there default attributes?
        $key = array_search('manual_default_attributes', $woocsv_product->header);
        if ($key === false) {
            $woocsv_product->log[] = __('No manual default attributes', 'woocommerce-csvimport');

            return;
        }

        // 	has no values
        if (empty ($woocsv_product->raw_data[ $key ])) {
            return;
        }

        //handle the product default values
        $defaults = explode('|', $woocsv_product->raw_data[ $key ]);

        $product_attributes_default = (isset ($woocsv_product->meta[ '_default_attributes' ])) ? $woocsv_product->meta[ '_default_attributes' ] : '';

        //loop through them
        foreach ($defaults as $default) {
            list($key, $value) = explode('->', $default);
            $product_attributes_default[ sanitize_title($key) ] = sanitize_title($value);
        }

        $woocsv_product->meta[ '_default_attributes' ] = $product_attributes_default;
    }

    public function attach_attributes_to_master()
    {
        global $woocsv_import, $woocsv_product;

        //variations column
        $key = array_search('variations', $woocsv_product->header);

        //if the column is not there return and write log
        if ($key === false) {
            $woocsv_product->log[] = __('variations column not found', 'woocommerce-csvimport');

            return;
        }

        //split the variations if there are multiple. var1|var2|var3
        $variations = explode('|', $woocsv_product->raw_data[ $key ]);

        //reset the array and the position
        $product_attributes = array ();
        $pos = 0;

        //loop through the variations
        foreach ($variations as $variation) {

            // @ since 3.0.4 check if variation is empty
            if (!$variation) {
                $woocsv_product->log[] = __('Empty variation found', 'woocommerce-csvimport');
                continue;
            }

            //get the values for visible and is variation else assume it's 1
            list($variation, $is_visible) = array_pad(explode('->', $variation), 2, 1);

            //fill in the array
            $product_attributes[ 'pa_' . $variation ] = array (
                'name'         => 'pa_' . $variation,
                'value'        => '',
                'position'     => "$pos",
                'is_visible'   => $is_visible,
                'is_variation' => 1,
                'is_taxonomy'  => 1,
            );

            //increase the position for the next one
            $pos++;

            //add them to the master if there are values
            $this->attach_attributes_values_to_master($woocsv_product->body[ 'ID' ], $variation);
        }


        $temp_vars = array ();
        $temp_atts = array ();

        if (isset ($woocsv_product->meta[ '_product_attributes' ])) {
            //split vars and atts
            $vars = $woocsv_product->meta[ '_product_attributes' ];
            foreach ($vars as $var) {
                if ($var[ 'is_variation' ] == 1) {
                    $temp_vars[ $var[ 'name' ] ] = $var;
                } else {
                    $temp_atts[ $var[ 'name' ] ] = $var;
                }
            }
        }

        //is merging enabled?
        if ($woocsv_import->options[ 'merge_products' ] == 1) {
            //merge all
            $woocsv_product->meta[ '_product_attributes' ] = array_merge($temp_atts, $temp_vars, $product_attributes);
        } else {
            //merge attributes and new variations
            $woocsv_product->meta[ '_product_attributes' ] = array_merge($temp_atts, $product_attributes);
        }

    }

    public function attach_attributes_values_to_master($post_id, $attribute)
    {
        global $woocsv_product, $woocsv_import;

        //check if the atrribute is in the header
        $key = array_search('pa_' . $attribute, $woocsv_product->header);
        if ($key === false) {
            return;
        }

        //Check if the attributes has values
        if (isset($woocsv_product->raw_data[ $key ])) {
            $value = $woocsv_product->raw_data[ $key ];
        } else {
            $value = '';
        }

        if (empty($value)) {
            return;
        }

        //add them to the product!
        //@TODO clean up globals
        if ($woocsv_import->options[ 'merge_products' ] == 1) {
            //if merging, add them to the existings
            wp_set_object_terms($post_id, explode('|', $value), 'pa_' . $attribute, true);
        } else {
            //no merging do not add them to the exiting but override
            wp_set_object_terms($post_id, explode('|', $value), 'pa_' . $attribute, false);
        }
    }

    public function attach_manual_attributes_to_master()
    {
        global $woocsv_import, $woocsv_product;

        //check if there is a attributes column
        $key = array_search('manual_variations', $woocsv_product->header);
        //return if there are no manual attributes

        if ($key === false) {
            return;
        }

        //explode attrbiutes
        $attributes = explode('|', $woocsv_product->raw_data[ $key ]);

        $product_attributes = (isset($woocsv_product->meta[ '_product_attributes' ])) ? $woocsv_product->meta[ '_product_attributes' ] : '';

        $pos = 0;
        //set the postition to the right value if variations allready toke some places
        if ($product_attributes) {
            foreach ($product_attributes as $x) {
                if ($x[ 'position' ] >= $pos) {
                    $pos = $x[ 'position' ];
                }
            }
        }

        //loop through the attributes
        foreach ($attributes as $attribute) {

            //get the values for visible and is variation else assume it's 1
            list($attribute, $is_visible) = array_pad(explode('->', $attribute), 2, 1);

            $manual_attribute = sanitize_title($attribute);

            //check if attribute value is in the header:  size -> ma_size
            $key = array_search('ma_' . $manual_attribute, $woocsv_product->header);

            $values = '';

            if ($key !== false) {
                $values = $woocsv_product->raw_data[ $key ];
            }

            //fill in the array
            $product_attributes[ $manual_attribute ] = array (
                'name'         => $attribute,
                'value'        => $values,
                'position'     => "$pos",
                'is_visible'   => (int)$is_visible,
                'is_variation' => 1,
                'is_taxonomy'  => 0,
            );

            //increase the position for the next one
            $pos++;
        }

        //save the attributes
        $woocsv_product->meta[ '_product_attributes' ] = $product_attributes;
    }


    public function handle_variation()
    {
        global $woocsv_product, $wpdb;

        $woocsv_parent_selector = $this->getParentSelector();

        if ($woocsv_parent_selector == 'sku') {

            $key = array_search('post_parent', $woocsv_product->header);
            if ($key !== false && !empty ($woocsv_product->raw_data[ $key ])) {
                //get the parent_id based on the SKU
                $parent_id = $wpdb->get_var($wpdb->prepare("SELECT max(post_id)
					FROM $wpdb->postmeta a, $wpdb->posts b
					WHERE a.post_id= b.id and meta_key='_sku' AND meta_value='%s' LIMIT 1", $woocsv_product->raw_data[ $key ]));

                if ($parent_id) {
                    $woocsv_product->body[ 'post_parent' ] = $parent_id;
                }
                $woocsv_product->log[] = 'It is a variation and the master product has ID: ' . $parent_id;
            } else {
                $woocsv_product->log[] = 'No parent found for post_parent: ' . $woocsv_product->raw_data[ $key ];

                return;
            }
        }

        if ($woocsv_parent_selector == 'identifier') {

        }


        $menu_order = (isset($woocsv_product->raw_data[ array_search('menu_order', $woocsv_product->header) ])) ? $woocsv_product->raw_data[ array_search('menu_order', $woocsv_product->header) ] : 0;

        //update the product with the right post_type and post_parent
        wp_update_post(array (
            'ID'          => $woocsv_product->body[ 'ID' ],
            'post_title'  => "variation of #$parent_id",
            'post_type'   => 'product_variation',
            //'post_status' => 'publish',
            'post_parent' => $parent_id,
            'menu_order'  => $menu_order,
        ));

        //get the parent attributes
        $product_attributes = get_post_meta($parent_id, '_product_attributes', true);

        //if there are no attributes return
        if (empty($product_attributes)) {
            $woocsv_product->log[] = 'Parent product does not have any attributes!!';

            return;
        }

        //loop through all attributes
        foreach ($product_attributes as $product_attribute) {

            //check if it is an attribute used for variations else break...
            if ($product_attribute[ 'is_variation' ] != 1) {
                continue;
            }

            //check if the atrribute is in the header
            $key = array_search($product_attribute[ 'name' ], $woocsv_product->header);

            if ($key === false || empty($woocsv_product->raw_data[ $key ])) {
                $woocsv_product->meta[ 'attribute_' . $product_attribute[ 'name' ] ] = '';
                continue;
            }

            //check if the term exist
            $term = term_exists($woocsv_product->raw_data[ $key ], $product_attribute[ 'name' ]);
            if ($term !== 0 && $term !== null) {
                //do nothing
            } else {
                //the term did not exists so create it
                $term = wp_insert_term($woocsv_product->raw_data[ $key ], $product_attribute[ 'name' ]);

                //did it succeed?
                if (is_wp_error($term)) {
                    $woocsv_product->log[] = __('could not make an attribute value and skipped it', 'woocommerce-csvimport');
                    continue;
                }
            }

            // the term is there, now get it so you have the slug etc
            $term = get_term($term[ 'term_id' ], $product_attribute[ 'name' ]);

            //the term is there add it to the parent
            wp_set_object_terms($woocsv_product->body[ 'post_parent' ], $term->slug, $product_attribute[ 'name' ], true);

            //set the right value to the child
            $woocsv_product->meta[ 'attribute_' . $product_attribute[ 'name' ] ] = $term->slug;
        }

        // @since 3.0.6
        // handle _variation_description
        $key = array_search('variation_description', $woocsv_product->header);
        if ($key !== false) {
            $woocsv_product->meta[ '_variation_description' ] = wp_kses_post($woocsv_product->raw_data[ $key ]);
        }

        //run the manual stuff
        $this->handle_manual_variation($product_attributes);

        //sync the parent
        WC_Product_Variable::sync($parent_id);

        $children = get_posts(array (
            'post_parent'    => $parent_id,
            'posts_per_page' => -1,
            'post_type'      => 'product_variation',
            'fields'         => 'ids',
            'post_status'    => 'any',
        ));

        if (!$children) {
            WC_Product_Variable::sync_attributes($parent_id, $children);
        }

    }

    public function handle_manual_variation($product_attributes)
    {
        global $woocsv_product;

        //loop through all attributes
        foreach ($product_attributes as $product_attribute) {

            //check if it is an attribute used for variations else break...
            if ($product_attribute[ 'is_variation' ] != 1) {
                continue;
            }

            //check if the atrribute is in the header
            $key = array_search('ma_' . $product_attribute[ 'name' ], $woocsv_product->header);

            if ($key !== false) {
                $woocsv_product->log[] = sprintf(__('Manual attribute %s found', 'woocommerce-csvimport'), $product_attribute[ 'name' ]);
                $woocsv_product->meta[ 'attribute_' . $product_attribute[ 'name' ] ] = $woocsv_product->raw_data[ $key ];
            }
        }

    }


    function content()
    {
        global $wpdb, $woocommerce;
        $attributes = $wpdb->get_results("select * from {$wpdb->prefix}woocommerce_attribute_taxonomies");

        //create attribute url
        if (str_replace('.', '', $woocommerce->version) >= 210) {
            $attr_url = get_admin_url() . 'edit.php?post_type=product&page=product_attributes';
        } else {
            $attr_url = get_admin_url() . 'edit.php?post_type=product&page=woocommerce_attributes';
        }
        ?>
        <hr>
        <h2><?php echo __('Import Variable products', 'woocommerce-csvimport'); ?> </h2>
        <?php if (empty($attributes)) : ?>
        <div class="error"><p>
                <?php echo sprintf(__('There are no attributes yet. Please goto the <a href="%s">attribute screen</a> to create them.', 'woocommerce-csvimport'), $attr_url);
                ?>
            </p></div>
    <?php else: ?>
        <h4><?php echo __('You have the following attributes:', 'woocommerce-csvimport'); ?></h4>
        <ul>
            <?php foreach ($attributes as $attribute) {
                echo '<li>' . __('Attribute:', 'woocommerce-csvimport') . ' <b><i>' . $attribute->attribute_name . ',</i></b> ' . __('use this header tag in your CSV:',
                        'woocommerce-csvimport') . ' <code>pa_' . $attribute->attribute_name . ' </code></li>';
            }
            ?>
        </ul>
        <p>
            <?php echo sprintf(__('Goto the  <a href="%s">attribute screen</a> to create more!', 'woocommerce-csvimport'), $attr_url); ?>
        </p>
    <?php endif; ?>
        <h3><?php echo __('Usage', 'woocommerce-csvimport'); ?></h3>
        <p>
            <?php echo __('There are several new fields available for you when you create a header:', 'woocommerce-csvimport'); ?>

        <h4><?php echo __('product_type', 'woocommerce-csvimport'); ?></h4>
        <?php echo __('The possible values are: <code>variation_master</code> for the master and <code>product_variation</code> for the product child', 'woocommerce-csvimport'); ?>

        <h4><?php echo __('post_parent', 'woocommerce-csvimport'); ?></h4>
        <?php echo __('In here you enter the SKU of the variation master.', 'woocommerce-csvimport'); ?>
        <h4><?php echo __('identifier and parent_identifier', 'woocommerce-csvimport') ?></h4>
        <?php echo __('If you do not want to use a sku in you master product, use the identifier and the parent_identifier like you would use sku and post_parent', 'woocommerce-csvimport') ?>
        <h4><?php echo __('variations', 'woocommerce-csvimport'); ?></h4>
        <?php echo __('This field is meant to setup which variations are used and if they are visible. If you look at this example <code>color->1|size->0</code>, we have 2 attributes. Color is visible and size is not visible.',
        'woocommerce-csvimport'); ?>
        <h4><?php echo __('default_attributes', 'woocommerce-csvimport'); ?></h4>
        <?php echo __('If you want the predefined values, you can add them using default attributes. If you want blue to be default for color and medium to be default for size you can set it up like this: <code>color->blue|size->medium</code>.',
        'woocommerce-csvimport'); ?>

        </p>
        <p>
            <?php echo __('If you are not sure how to import variable products, check the <a target="_blank" href="http://allaerd.org/knowledgebase">documentations</a>', 'woocommerce-csvimport'); ?>

        </p>
        <h4><?php echo __('variation_description', 'woocommerce-csvimport'); ?></h4>
        <p>
            <?php echo __('In here you can add extra information for the product variation.', 'woocommerce-csvimport'); ?>
        </p>
        <?php
        do_action('woocsv-variations-settings');
    }


    /**
     * @return mixed|string|void
     */
    public function getParentSelector()
    {

        $woocsv_parent_selector = get_option('woocsv_parent_selector');

        if (!$woocsv_parent_selector) {
            update_option('sku', 'woocsv_parent_selector');
            $woocsv_parent_selector = 'sku';
        }

        return $woocsv_parent_selector;
    }

}