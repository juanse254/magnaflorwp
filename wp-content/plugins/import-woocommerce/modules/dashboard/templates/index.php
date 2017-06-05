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
if(! $_SERVER['HTTP_REFERER']){
                        die('Your requested url were wrong! Please contact your admin.');
                }

global $wpdb;
$impCE = new WoocomImporter_includes_helper(); 

$dashObj = new DashboardActions();
$ret_arr=array();
?>
<div class="box-one">
	<div class="top-right-box">
		<h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(Woocom_CONST_CSV_IMP_DIR.'images/chart_bar.png');?>" /></span><?php echo __('Importers Activity','import-woocommerce'); ?></h3>
		<div class="top-right-content">
			<div id='dispLabel'></div>
			<div class='lineStats' id='lineStats' style='height: 250px;width:100%;margin-top:15px; margin-bottom:15px;'></div>
		</div>
	</div>
	<div class="top-right-box">
		<h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(Woocom_CONST_CSV_IMP_DIR.'images/stat_icon.png');?>"></span><?php echo __('Import Statistics','import-woocommerce'); ?></h3>
		<div class="top-right-content">
			<div id='dispLabel'></div>
			<div class='pieStats' id='pieStats' style='float:left;height:250px;width:100%;margin-top:15px;margin-bottom:15px;'></div>
		</div>
	</div>
</div>
