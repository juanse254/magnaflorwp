<?php
/*********************************************************************************
 * Import Woocommerce is a Tool for importing CSV for the Wordpress
 * plugin developed by Smackcoder. Copyright (C) 2014 Smackcoders.
 *
 * Import Woocommerce is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY Import
 * Woocommerce, Import Woocommerce DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * Import Woocommerce is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * Import Woocommerce copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2014. All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class WoocommerceActions extends SkinnyActions {

    public function __construct()
    {
    }

  /**
   * The actions index method
   * @param array $request
   * @return array
   */
    public function executeIndex($request)
    {
        // return an array of name value pairs to send data to the template
        $data = array();
	$get_importer_settings = get_option('woocomcsvfreesettings');
	if (in_array('woocommerce', $get_importer_settings)) {
                   $data['is_enable'] = 'on';
        } else {
                   $data['is_enable'] = 'off';
        }

        return $data;
    }
    // @var boolean post title check
        public $titleDupCheck = false;

        // @var boolean content title check
        public $conDupCheck = false;

        // @var boolean for post flag
        public $postFlag = true;

        // @var int duplicate post count
        public $dupPostCount = 0;

        // @var int inserted post count
        public $insPostCount = 0;

        // @var int no post author count
        public $noPostAuthCount = 0;

        // @var int updated post count
        public $updatedPostCount = 0;

        // @var array wp field keys
        public $keys = array();

        // @var Multi images
        public $MultiImages = false;

        public $detailedLog = array();

	public $defCols = array('post_title' => 'product_name', 'post_content' => 'product_content', 'post_excerpt' => 'product_excerpt', 'post_date' => 'product_date', 'post_slug' => 'product_slug', 'post_status' => 'product_status', 'post_author' => 'product_author', 'post_parent' => 'product_parent', 'comment_status' => 'comment_status', 'ping_status' => 'ping_status', 'visibility' => 'visibility','stock_status' => 'stock_status','stock_qty' => 'stock_qty','downloadable' => 'downloadable','downloadable_files' => 'downloadable_files','download_expiry'=>'download_expiry','download_limit'=>'download_limit','virtual' => 'virtual','regular_price' => 'regular_price','sale_price' => 'sale_price', 'purchase_note' => 'purchase_note','weight' => 'weight','length' => 'length', 'width' => 'width','height' => 'height','sku' => 'sku','upsell_ids' => 'upsell_ids','crosssell_ids' => 'crosssell_ids','price' => 'price','sold_individually' => 'sold_individually','manage_stock' => 'manage_stock','backorders' => 'backorders','stock' => 'stock','product_attribute_name'=>'product_attribute_name', 'product_attribute_value'=>'product_attribute_value','product_attribute_visible'=>'product_attribute_visible', 'featured_image' => 'featured_image', 'featured_product' => 'featured_product', 'product_image_gallery' => 'product_image_gallery', 'menu_order' => 'menu_order','download_type' => 'download_type');

	public function isplugin() {
                $allplugins = get_plugins();
                $allpluginskey = array();
                foreach ($allplugins as $key => $value) {
                        $allpluginskey[] = $key;
                }

                if ((!in_array('woocommerce/woocommerce.php', $allpluginskey))) {
                        $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_avail'] = 'not_avail';
                } else {
                        $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_avail'] = 'avail';
                }
                $allactiveplugins = get_option('active_plugins');
                $allactiveplugins_value = array();
                foreach ($allactiveplugins as $key => $value) {
                        $allactiveplugins_value[] = $value;
                }
                if ((!in_array('woocommerce/woocommerce.php', $allactiveplugins_value))) {
                        $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_activ'] = 'not_activ';
                } else {
                        $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_activ'] = 'activ';
                }
      }
}
