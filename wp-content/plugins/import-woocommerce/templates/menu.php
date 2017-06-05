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
$impObj = new WoocomImporter_includes_helper();
$nonceKey = $impObj->create_nonce_key();
if(! wp_verify_nonce($nonceKey, 'smack_importwoocom_nonce'))
die('You are not allowed to do this operation.Please contact your admin.');
$impCheckobj = CallWoocomImporterObj::checkSecurity();
if($impCheckobj != 'true')
die($impCheckobj);

$post = $page =  $woocommerce = $settings =  $dashboard = $module = '';
$impCEM = CallWoocomImporterObj::getInstance();
$get_settings = array();
$get_settings = $impCEM->getSettings();
$mod = isset($_REQUEST['__module']) ? sanitize_text_field($_REQUEST['__module']) : '';
if( is_array($get_settings) && !empty($get_settings) ) {
        foreach ($get_settings as $key) {
                $$key = true;
        }
}
if (isset($_POST['post_csv']) && sanitize_text_field($_POST['post_csv']) == 'Import') {
	$dashboard = 'activate';
} else {
	if (isset($_REQUEST['action'])) {
		$action = sanitize_text_field($_REQUEST['action']);
               
		$$action = 'activate';
	} else {
		if (isset($mod) && !empty($mod)) {
                       $module_array =array('post','page','woocommerce','dashboard');
                  foreach($module_array as $val) {
                       if($val = $mod) { 
			   $$mod = 'activate';
                             if( $mod != 'settings' && $mod != 'dashboard') {
                                $module = 'activate';
                                $manager = 'deactivate';
                                $dashboard = 'deactivate';
                                }
                             else if($mod == 'dashboard') {
                                $manager = 'deactivate';
                                $module = 'deactivate';
                                }
                        }                 
                  }
	        } else {
		      if (!isset($_REQUEST['action'])) {
				$dashboard = 'deactivate';
			}
		}
	}
}
$tab_inc = 1;
$display_module = isset($_REQUEST['__module'])? sanitize_text_field($_REQUEST['__module']):'';
$menuHTML = "<nav class='navbar navbar-default' role='navigation'>
   <div>
      <ul class='nav navbar-nav'>
         <li  class = '".sanitize_html_class($dashboard)."' ><a href='" . esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG . '/index.php','__module' => 'dashboard'),$impObj->baseURL))."'  >".__('Dashboard','import-woocommerce')."</a></li>
         <li class='dropdown ".sanitize_html_class($module)."'>
            <a href='#'  data-toggle='dropdown'>
               ". __('Imports','import-woocommerce')."
               <b class='caret'></b>
            </a>
            <ul class='dropdown-menu'>
               	<li class = '".sanitize_html_class($woocommerce)."'><a href='" . esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG . '/index.php','__module' => 'woocommerce','step' => 'uploadfile'),$impObj->baseURL))."'>". __('WooCommerce','import-woocommerce')."</a></li>
		<li class= '".sanitize_html_class($post)."'><a href= '" . esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG . '/index.php','__module' => 'post','step' => 'uploadfile'),$impObj->baseURL))."'>".__('Post','import-woocommerce')."</a></li>
                <li class = '".sanitize_html_class($page)."'><a href='" . esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG . '/index.php','__module' => 'page','step' => 'uploadfile'),$impObj->baseURL))."'>". __('Page','import-woocommerce')."</a></li>";

$menuHTML .= "</ul>
         </li>";
	 $menuHTML .= "<li class=  '".sanitize_html_class($settings)."'><a href='" . esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG . '/index.php','__module' => 'settings'),$impObj->baseURL))."'  />". __('Settings','import-woocommerce')."</a></li></ul>";
$menuHTML .= "</div>";
$menuHTML .= "<div class='msg' id = 'showMsg' style = 'display:none;'></div>";
$menuHTML .= "<input type='hidden' id='current_url' name='current_url' value='" . get_admin_url() . "admin.php?page=" . Woocom_CONST_CSV_IMP_SLUG . "/index.php&__module=" . $display_module . "&step=uploadfile'/>";
$menuHTML .= "<input type='hidden' name='checkmodule' id='checkmodule' value='" . $display_module . "' />";
$menuHTML .=  "
</nav>";

echo $menuHTML;

