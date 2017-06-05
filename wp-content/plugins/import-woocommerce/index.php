<?php
/******************************
 * Plugin Name: Import Woocommerce
 * Description: Import your WordPress Post, Page and Simple WooCommerce Product with Import Woocommerce.
 * Version: 1.3
 * Author: smackcoders.com
 * Text Domain: import-woocommerce
 * Domain Path: /languages
 * Plugin URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 * Author URI: http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html
 */

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
$get_debug_mode = get_option('woocomcsvfreesettings');
$debug_mode = isset($get_debug_mode['debug_mode']) ? $get_debug_mode['debug_mode'] : '';
if($debug_mode != 'enable_debug'){
	ini_set('display_errors', 'Off');
	error_reporting(0);
}

@ob_start();
add_action('init', 'WoocomimpStartSession', 1);
add_action('wp_logout', 'WoocomimpEndSession');
add_action('wp_login', 'WoocomimpEndSession');
/**
 * To Start Session
 */
function WoocomimpStartSession() {
	if (!session_id()) {
		session_start();
	}
}
/**
 * To Destroy session
 */
function WoocomimpEndSession() {
	session_destroy();
}
if ( empty( $GLOBALS['wp_rewrite'] ) )
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();

define('Woocom_CONST_CSV_IMP_URL', 'http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html');
define('Woocom_CONST_CSV_IMP_NAME', 'Import Woocommerce');
define('Woocom_CONST_CSV_IMP_SLUG', 'import-woocommerce');
define('Woocom_CONST_CSV_IMP_SETTINGS', 'Import Woocommerce');
define('Woocom_CONST_CSV_IMP_VERSION', '1.3');
define('Woocom_CONST_CSV_IMP_DIR', WP_PLUGIN_URL . '/' . Woocom_CONST_CSV_IMP_SLUG . '/');
define('Woocom_CONST_CSV_IMP_DIRECTORY', plugin_dir_path(__FILE__));
define('Woocom_CSVIMP_PLUGIN_BASE', Woocom_CONST_CSV_IMP_DIRECTORY);
if (!class_exists('SkinnyControllerWoocomCsvFree')) {
	require_once('lib/skinnymvc/controller/SkinnyController.php');
}

/* add_action('plugins_loaded','load_langfiles');

function load_langfiles(){
$csv_importer_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
load_plugin_textdomain( 'import-woocommerce', false, $csv_importer_dir);
} */

require_once('plugins/class.inlineimages.php');
require_once('includes/WoocomImporter_includes_helper.php');

# Activation & Deactivation 
register_activation_hook(__FILE__, array('WoocomImporter_includes_helper', 'activate'));
register_deactivation_hook(__FILE__, array('WoocomImporter_includes_helper', 'deactivate'));

function woocom_csv_imp_admin_menu() {
	if(is_multisite()) {
		if ( current_user_can( 'administrator' ) ) { 
			add_menu_page(Woocom_CONST_CSV_IMP_SETTINGS, Woocom_CONST_CSV_IMP_NAME, 'manage_options', __FILE__, array('WoocomImporter_includes_helper', 'output_fd_page'), Woocom_CONST_CSV_IMP_DIR . "images/icon.png");
		} else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WoocomImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			if(isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
				add_menu_page(Woocom_CONST_CSV_IMP_SETTINGS, Woocom_CONST_CSV_IMP_NAME, '2', __FILE__, array('WoocomImporter_includes_helper', 'output_fd_page'), Woocom_CONST_CSV_IMP_DIR . "images/icon.png");
			}
		}
	}
	else {
		if ( current_user_can( 'administrator' ) ) {
                        add_menu_page(Woocom_CONST_CSV_IMP_SETTINGS, Woocom_CONST_CSV_IMP_NAME, 'manage_options', __FILE__, array('WoocomImporter_includes_helper', 'output_fd_page'), Woocom_CONST_CSV_IMP_DIR . "images/icon.png");
                }
		else if ( current_user_can( 'author' ) || current_user_can( 'editor' ) ) {
			$HelperObj = new WoocomImporter_includes_helper();
			$settings = $HelperObj->getSettings();
			if(isset($settings['enable_plugin_access_for_author']) && $settings['enable_plugin_access_for_author'] == 'enable_plugin_access_for_author') {
				add_menu_page(Woocom_CONST_CSV_IMP_SETTINGS, Woocom_CONST_CSV_IMP_NAME, '2', __FILE__, array('WoocomImporter_includes_helper', 'output_fd_page'), Woocom_CONST_CSV_IMP_DIR . "images/icon.png");
			}
		}
	}
}
add_action("admin_menu" , "woocom_csv_imp_admin_menu"); 

function woocom_csv_imp_admin_init() {
	if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'import-woocommerce/index.php' || $_REQUEST['page'] == 'page')) {
		wp_register_script('import-woocommerce-js', plugins_url('/js/import-woocommerce.js', __FILE__));
		wp_enqueue_script('import-woocommerce-js');
		wp_enqueue_style('style', plugins_url('/css/style.css', __FILE__));
		wp_enqueue_style('bootstrap-css', plugins_url('/css/bootstrap.css', __FILE__));
		wp_enqueue_style('ultimate-importer-css', plugins_url('/css/main.css', __FILE__));
		wp_enqueue_style('morris-css', plugins_url('/css/morris.css', __FILE__));
		// For chart js
		wp_register_script('dropdown', plugins_url('/js/dropdown.js', __FILE__));
		wp_enqueue_script('dropdown');
		wp_register_script('raphael-min-js', plugins_url('/js/raphael-min.js', __FILE__));
		wp_enqueue_script('raphael-min-js');
		wp_register_script('morris-min-js', plugins_url('/js/morris.min.js', __FILE__));
		wp_enqueue_script('morris-min-js');
		wp_register_script('data', plugins_url('/js/dashchart.js', __FILE__));
		wp_enqueue_script('data');
		wp_localize_script('import-woocommerce-js','import_woocom_translate',TranslateReqString());
	}
}

add_action('admin_init', 'woocom_csv_imp_admin_init');

function TranslateReqString(){
        $woocomObj = new WoocomImporter_includes_helper();
        $woocom_import_msg = array(
                        'dashchartMsg' => __('NO LOGS YET NOW.','import-woocommerce'),
                        'emptyTemplate' => __('Template name is empty','import-woocommerce'),
                        'exist_Template' => __('Template Name already exists','import-woocommerce'),
                        'woocom_errorMsg' => __('Error: ','import-woocommerce'),
                        'mandatory_fieldmsg' => __(' - Mandatory fields. Please map the fields to proceed.','import-woocommerce'),
                        'generalMsgtext' => __(' should be mapped.','import-woocommerce'),
                        'reqfieldRole' => __('role','import-woocommerce'),
                        'validate_zipfile' => __('File must be .zip!','import-woocommerce'),
                        'reqfieldmsg' => __('Fill all mandatory fields.','import-woocommerce'),
			'file_existMsg' => __('The files does not exist','import-woocommerce'),
                        'importProgress_msg' => __('Your Import Is In Progress...','import-woocommerce'),
                        'terminateImport_msg' => __('Import process has been terminated.','import-woocommerce'),
                        'continueImport_msg' => __(' Import process has been continued.','import-woocommerce'),
                        'ultimate_proMsg' => __(' Feature is available only for PRO!.','import-woocommerce'),
                        'validate_Recordnum' => __('Please enter numeric characters only','import-woocommerce'),
                        'validate_Exportmsg' => __('Please choose one module to export the records!','import-woocommerce'),
                        'customList_Msg' => __('Please choose anyone of Custompost Lists'),
                        'customtaxonomyMsg' => __('Please choose anyone of Customtaxonomy Lists'),
                        'fileformat_Msg' => __('Un Supported File Format','import-woocommerce'),
                        'woocom_secureKey' => $woocomObj->create_nonce_key(),
        );
        return $woocom_import_msg;
}

// Move Pages above Media
function smackcsv_menu_order( $menu_order ) {
   return array(
       'index.php',
       'edit.php',
       'edit.php?post_type=page',
       'upload.php',
       'import-woocommerce/index.php',
   );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'smackcsv_menu_order' );

function firstcsvchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_firstcsvchart', 'firstcsvchart');

function secondcsvchart() {
	require_once("modules/dashboard/actions/chartone.php");
	die();
}

add_action('wp_ajax_secondcsvchart', 'secondcsvchart');

function addcustomfield() {
	check_ajax_referer( 'smack_importwoocom_nonce', 'securekey' );
        require_once("templates/Addcustomfields.php");
        die();
}

add_action('wp_ajax_addcustomfield', 'addcustomfield');


function roundcsvchart() {
	global $wpdb;
	ob_flush();
	$myObj = new WoocomImporter_includes_helper(); 
	$content = "<form name='piechart'> <div id ='woocompieStats' style='height:250px;'>";
	$myObj->piechart();
	$content .= "</div></form>"; 
	echo $content;
}

function linetwocsvStats() {
	global $wpdb;
	ob_flush();
	$myObj = new WoocomImporter_includes_helper(); 
	$content = "<form name='piechart'> <div id ='woocomlineStats' style='height:250px'>";
	$myObj->getStatsWithDate();
	$content .= "</div></form>"; 
	echo $content;
}

function woocomcsvimporter_add_dashboard_widgets() {
	wp_enqueue_style('morris-woocomcss', plugins_url('css/morris.css', __FILE__));
	wp_enqueue_script('woocomdashchart', plugins_url('js/dashchart-widget.js', __FILE__));
	wp_enqueue_script('woocomraphael-js', plugins_url('js/raphael-min.js', __FILE__));
	wp_enqueue_script('woocom-morris-js', plugins_url('js/morris.min.js', __FILE__));
	wp_add_dashboard_widget('woocomcsvimporter_dashboard_piehart', 'Woocom-CSV-Importer-Statistics', 'roundcsvchart',$screen = get_current_screen() , 'advanced' ,'high' );
	wp_add_dashboard_widget('woocomcsvimporter_dashboard_linechart', 'Woocom-CSV-Importer-Activity', 'linetwocsvStats',$screen = get_current_screen(),'advanced','high');
}

add_action('wp_dashboard_setup', 'woocomcsvimporter_add_dashboard_widgets');

/**
 * To Process the Import
 */
function importcsvByRequest() {
	require_once("templates/import.php");
	die;
}
add_action('wp_ajax_importcsvByRequest', 'importcsvByRequest');

function woocom_uploadfilehandle() {
	check_ajax_referer( 'smack_importwoocom_nonce', 'securekey' );
        require_once("lib/jquery-plugins/uploader.php");
        die();
}
add_action('wp_ajax_woocom_uploadfilehandle','woocom_uploadfilehandle');

