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

$impCE = new WoocomImporter_includes_helper();
$nonce_Key = $impCE->create_nonce_key();
$woo_obj = new WoocommerceActions();
$ex = $woo_obj->isplugin();
?>
	<div style="width:100%;">
	<div id="accordion">
	<table class="table-importer">
	<tr>
	<td>
	<h3><?php echo esc_html__('CSV Import Options','import-woocommerce'); ?></h3>
	<div id='sec-one' <?php if(sanitize_text_field($_REQUEST['step'])!= 'uploadfile') {?> style='display:none;' <?php } ?>>
	<?php if(is_dir($impCE->getUploadDirectory('default'))) { 
		if (!is_writable($impCE->getUploadDirectory('default'))) {
			if (!chmod($impCE->getUploadDirectory('default'), 0777)) { ?>
				<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='notfound' /> <?php
			}
		} else { ?>
			<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='found' />
		<?php }?>
	<?php } else { ?>
		<input type='hidden' id='is_uploadfound' name='is_uploadfound' value='notfound' />
	<?php } ?>
	<div class="warning" id="warning" name="warning" style="display:none;margin: 4% 0 4% 22%;"></div>
	<div align=center>
        <div id="noPlugin" class="warnings" style="display:none"></div>
        </div>
	<form action='<?php echo esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG.'/index.php', '__module' => sanitize_text_field($_REQUEST['__module']), 'step' => 'mapping_settings'), $impCE->baseURL));?>' id='browsefile' enctype="multipart/form-data" method='post' name='browsefile'>
	<div class="importfile" align='center'>
	<?php
        if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_avail'] == 'not_avail') {
        ?>
		<script> var warnings = document.getElementById("noPlugin");
		warnings.innerHTML = '<strong><font size="4" color="red">There is no WooCommerce plugin. Please install WooCommerce plugin.</font></strong>';
		jQuery('#noPlugin').css('display', 'block');
		</script>
        <?php
        }
	else {
		if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_activ'] == 'not_activ')
		{
		?>
		<script> var warnings = document.getElementById("noPlugin");
			warnings.innerHTML = '<strong><font size="4" color="red">Please activate WooCommerce plugin.</font></strong>';
			jQuery('#noPlugin').css('display', 'block');
		</script>
		<?php
		}
	}
	if ($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_avail'] != 'not_avail' && $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['isplugin_activ'] != 'not_activ'){ ?>
	<div id='filenamedisplay'></div>
	<div class="container">
        <?php echo $impCE->smack_csv_import_method(); ?>

	<input type ='hidden' id="pluginurl"value="<?php echo WP_CONTENT_URL;?>">
	<input type='hidden' id='dirpathval' name='dirpathval' value='<?php echo ABSPATH; ?>' /> 
	<?php $uploadDir = wp_upload_dir(); ?>
	<input type="hidden" id="uploaddir" value="<?php if(isset($uploadDir)) { echo $uploadDir['basedir']; }  ?>">
	<input type="hidden" id="uploadFileName" name="uploadfilename" value="">
	<input type = 'hidden' id = 'uploadedfilename' name = 'uploadedfilename' value = ''>
        <input type = 'hidden' id = 'upload_csv_realname' name = 'upload_csv_realname' value =''>
        <input type = 'hidden' id = 'current_file_version' name = 'current_file_version' value = ''>
        <input type = 'hidden' id = 'current_module' name = 'current_module' value = '<?php if(isset($_REQUEST['__module'])) { echo sanitize_text_field($_REQUEST['__module']); }  ?>' >
	</span>
	<!-- The global progress bar -->
          <div class="form-group" style="padding-bottom:20px;">
                                <table>
                                <tr>
                                <div style="float:right;">
                                <input type='button' name='clearform' id='clearform' title = '<?php echo esc_attr__("Clear",'import-woocommerce'); ?>' value='<?php echo esc_attr__("Clear",'import-woocommerce'); ?>' onclick="Reload();" class='btn btn-warning' style="margin-right:15px;"/>
                          
                               <input type='submit' name='importfile' id='importfile' title = '<?php echo esc_attr__("Next",'import-woocommerce'); ?>' value='<?php echo esc_attr__("Next",'import-woocommerce');?> >>' disabled  class='btn btn-primary' style="margin-right:15px;"/>
                                </div>
                                </tr>
                                </table>
                                <!-- The container for the uploaded files -->
                                <div id="files" class="files"></div>
                                   <br>
                                </div>

	</form>
	</div>
	</div>
	<?php } ?>
	</td>
	</tr>
	<tr>
	<td>
	<form name='mappingConfig' action="<?php echo esc_url(add_query_arg(array('page' => Woocom_CONST_CSV_IMP_SLUG.'/index.php', '__module' => sanitize_text_field($_REQUEST['__module']), 'step' => 'importoptions'), $impCE->baseURL));?>"  method="post" onsubmit="return import_csv();" >
	<div class='msg' id = 'showMsg' style = 'display:none;'></div>
	<?php $_SESSION['SMACK_MAPPING_SETTINGS_VALUES'] = $_POST;
	$woocomcsvsettings=array();
        $custom_key = array();
	$woocomcsvsettings=get_option('woocomcsvfreesettings');
	?>
	<h3>Map CSV to WP fields/attributes</h3>
          <?php  if(isset($_REQUEST['step']) && sanitize_text_field($_REQUEST['step']) == 'mapping_settings')  {   ?> 
	<div id='sec-two' <?php if(sanitize_text_field($_REQUEST['step'])!= 'mapping_settings'){ ?> style='display:none;' <?php } ?> >
	<div class='mappingsection'>
	<h2><div class="secondformheader">Import Data Configuration</div></h2>
	<?php  
	if(isset($_FILES['inlineimages'])) {
		if(isset($_POST['uploadfilename']) && sanitize_file_name($_POST['uploadfilename']) != ''){
			$get_file_name = sanitize_file_name($_POST['uploadfilename']);
			$filehashkey = $impCE->convert_string2hash_key($get_file_name);
		}
		$uploaded_compressedFile = $_FILES['inlineimages']['tmp_name'];
		$get_basename_zipfile = explode('.', $_FILES['inlineimages']['name']);
		$basename_zipfile = $get_basename_zipfile[0];
		$location_to_extract = $uploadDir['basedir'] . '/smack_inline_images/' . $filehashkey;
		$extracted_image_location = $uploadDir['baseurl'] . '/smack_inline_images/' . $filehashkey;
		if(class_exists('ZipArchive')){
		$zip = new ZipArchive;
		if(!empty($uploaded_compressedFile)){
			if ($zip->open($uploaded_compressedFile) === TRUE) {
				$zip->extractTo($location_to_extract);
				$zip->close();
				$extracted_status = 1;
			} else {
				$extracted_status = 0;
			}
		}
		}
	}
	?>
			<?php echo $impCE->getImportDataConfiguration(); ?>
			</div>
			<div id='mappingheader' class='mappingheader' >
			<?php  
			$mFieldsArr='';
			$mappingFields_arr =array();
			$filename='';
                        $records = '';
			if(isset($_POST['uploadfilename']) && sanitize_file_name($_POST['uploadfilename']) != ''){
				$file_name = sanitize_file_name($_POST['uploadfilename']);
				$filename = $impCE->convert_string2hash_key($file_name);
			}
                        if(isset($_POST['upload_csv_realname']) && sanitize_file_name($_POST['upload_csv_realname']) != '') {
                                $uploaded_csv_name = sanitize_file_name($_POST['upload_csv_realname']);
                        }

			$getrecords = $impCE->csv_file_data($filename); 
			?>	
			<table style="font-size: 12px;" class = "table table-striped"> 
			<tr>
			<div align='center' style='float:right;'>
			<?php $cnt = count($impCE->defCols) + 2;
			$cnt1 = count($impCE->headers);
                        $records = count($getrecords);
			$imploaded_array = implode(',', $impCE->headers); ?>
			<input type = 'hidden' id = 'imploded_header' name = 'imploded_array' value = '<?php if(isset($imploaded_array)) { echo $imploaded_array;  }  ?>'>
			<input type='hidden' id='h1' name='h1' value="<?php if(isset($cnt)) { echo $cnt; } ?>"/>
			<input type='hidden' id='h2' name='h2' value="<?php if(isset($cnt1)) { echo $cnt1;  } ?>"/>
			<input type='hidden' name='selectedImporter' id='selectedImporter' value="<?php if(isset($_REQUEST['__module'])) { echo sanitize_text_field($_REQUEST['__module']);  }  ?>"/>
			<input type='hidden' id='current_record' name='current_record' value='0' />
			<input type='hidden' id='totRecords' name='totRecords' value='<?php if(isset($records)) { echo $records; }  ?>' />
			<input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo Woocom_CONST_CSV_IMP_DIR; ?>' />
			<input type='hidden' id='nonceKey' name='wpnonce' value='<?php echo $nonce_Key; ?>' />
			<input type='hidden' id='uploadedFile' name='uploadedFile' value="<?php if(isset($filename)) { echo  $filename;  }  ?>" />
                        <!-- real uploaded filename -->
                        <input type='hidden' id='uploaded_csv_name' name='uploaded_csv_name' value="<?php if(isset($uploaded_csv_name)) {   echo $uploaded_csv_name;  }  ?>" />
			<input type='hidden' id='stepstatus' name='stepstatus' value='<?php if(isset($_REQUEST['step'])){ echo sanitize_text_field($_REQUEST['step']); }  ?>' />
			<input type='hidden' id='inline_image_location' name='inline_image_location' value='<?php if(isset($extracted_image_location)){ echo $extracted_image_location;} ?>' />
			</div>
			</tr> 
			<?php
			$count = 0;
			if (isset($_REQUEST['__module']) && sanitize_text_field($_REQUEST['__module']) != 'page') {
				unset($impCE->defCols['menu_order']);
                                unset($impCE->defCols['wp_page_template']);
			}
			?>
			<tr>
                        <td colspan='4' class="left_align columnheader" style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core'><b>WordPress Fields:</b>
                        </div>
                        </td>
                        </tr>
                        <tr><td class="left_align columnheader" style="padding-left:170px;"> <b><?php echo esc_html__('WP HEADER','import-woocommerce'); ?></b> </td><td class="columnheader"> <b><?php echo esc_html__('CSV FIELDS','import-woocommerce'); ?></b> </td><td></td><td></td></tr>
                        <?php
                        foreach ($woo_obj->defCols as $key => $value)
                        {
                                if(!strstr($key,'CF:') && !strstr($key,'SEO:') && !strstr($key,'TERMS:')){?>
                        <tr>
                                <td class="left_align" style="padding-left:150px;" >
                        <input type='hidden' name ='fieldname<?php print($count); ?>' id = 'fieldname<?php print($count); ?>' value = <?php echo $key; ?> />
                        <label class='wpfields'><?php print('<b>'.$key.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$value.']'); ?></label>
                                </td>
				<td>
                                        <?php if($key == 'post_status'){ ?>
                                        <select name="mapping<?php print($count); ?>" id="mapping<?php print($count); ?>" onChange=changefield();>
                                        <?php }else{ ?>
                                        <select name="mapping<?php print($count); ?>" id="mapping<?php print($count);?>">
                                        <?php } ?>
                                        <option>-- Select --</option>
                                        <?php foreach($impCE->headers as $key1 => $value1){?>
                                                <option><?php echo $value1; ?></option>
                                        <?php }?>
                                        </select>
                                <script type="text/javascript">
                                        jQuery("select#mapping<?php print($count); ?>").find('option').each(function() {
                                                        if(jQuery(this).val() == "<?php print($value);?>") {
                                                        jQuery(this).prop('selected', true);
                                                        }
                                        });
                                        </script>

                                </td>
                                <td>

                                </td><td></td>
                                </tr>
                                        <?php
                                        $count++;
                        }
                        }
                ?>
                <input type='hidden' id='wpfields' name='wpfields' value='<?php echo($count) ?>' />
                </table>
		<table style="font-size: 12px;" class = "table table-striped" id='CF_FIELDGRP'>
                        <tr>

                        <td colspan = 5 class='left_align columnheader' style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core'><b>Custom Fields:</b>
                        </div>

                        </td>
                        </tr>
                        <?php
                        foreach($impCE->defCols as $key => $value){
                                if(strstr($key,'CF:')){
                        ?>
                        <tr>
                                <td class="left_align" style='width:53%;padding-left:150px;'>
                                 <input type='hidden' name ='corefieldname<?php print($count); ?>' id = 'corefieldname<?php print($count); ?>' value = '<?php echo $key; ?>' />

                        <label class='wpfields'><?php print('<b>'.$value.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$value.']'); ?></label>
                                </td>
				<td>
                                        <select name="coremapping<?php print($count); ?>" id="coremapping<?php print($count); ?>">
                                        <option>-- Select --</option>
                                        <?php foreach($impCE->headers as $key1 => $value1){?>
                                                <option><?php echo $value1; ?></option>
                                        <?php }?>
                                        </select>

                                        <script type="text/javascript">
                                        jQuery("select#coremapping<?php print($count); ?>").find('option').each(function() {
                                                        if(jQuery(this).val() == "<?php print($value);?>") {
                                                        jQuery(this).prop('selected', true);
                                                        }
                                        });
                                        </script>
                                </td>
                                <td>
                                </td><td></td>
                                </tr>
                                        <?php
                                        $count++;
                        }
                        }?>
                        <input type='hidden' id='customfields' name='customfields' value='<?php echo($count) ?>' />
                        </table>
			<table>
			<tr>
<td colspan= '4'>
<input type='button' class='btn btn-primary' name='addcustomfd' value='Add Custom Field' style='margin-left:85%;margin-bottom:15px;margin-top:20px;' onclick = 'addcorecustomfield(CF_FIELDGRP);'>
<input type='hidden' id='addcorecustomfields' name='addcorecustomfields' value='' />
</td>
</tr>
</table>
<!-- Terms and Taxonomy -->
<table style="font-size: 12px;" class="table table-striped" id='TERMS_FIELDGRP'>
<tr>
<td colspan=5 class='left_align columnheader' style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
<div id='terms_field' style='font-size:18px; font-family:times;'><b><?php echo esc_html__('Terms / Taxonomies Fields:','wp-ultimate-csv-importer'); ?></b>
</div>
</td>
</tr>
<?php
if(!empty($impCE->defCols) && is_array($impCE->defCols)) {
        foreach ($impCE->defCols as $key => $value) {
                if (strstr($key, 'TERMS:')) {
                        $key = str_replace('TERMS:', '', $key);
                        ?>
                                <tr>
                                <td class="left_align" style='width:53%;padding-left:150px'>
                                <input type='hidden' name='termfieldname<?php print($count); ?>'
                                id='termfieldname<?php print($count); ?>'
                                value='<?php echo $value; ?>'/>
                                <label class='wpfields'><?php print('<b>' . $key . '</b></label><br><label class="samptxt" style="padding-left:20px">[Name: ' . $value . ']'); ?></label>
                                </td>
                                <td>
                                <select name="term_mapping<?php print($count); ?>"
                                id="term_mapping<?php print($count); ?>">
                                <option>-- Select --</option>
                                <?php if(is_array($impCE->headers) && !empty($impCE->headers)) {
                                        foreach ($impCE->headers as $key1 => $value1) { ?>
                                                <option><?php echo $value1; ?></option>
                                                        <?php }
                                }?>
                        </select>
                                <script type="text/javascript">
                                jQuery("select#term_mapping<?php echo $count;?>").find('option').each(function () {
                                                if (jQuery(this).val() == "<?php print($value);?>") {
                                                jQuery(this).prop('selected', true);
                                                }
                                                });
                        </script>
                                </td>
                                <td></td>
                                <td></td>
                                </tr>
                                <?php
                                $count++;
                }
        }
}?>
<input type='hidden' id='termfields' name='termfields' value='<?php echo($count) ?>'/>
</table>
<!-- End Terms and Taxonomy -->
		<?php
                        $csvsettings = get_option('csvimportsettings');
                        $active_plugins = get_option('active_plugins');
                                if(in_array('all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins)){
?>
                        <table style="font-size: 12px;" class = "table table-striped" id='SEO_FIELDGRP'>
                        <tr>

                        <td colspan = 5 class='left_align columnheader' style='background-color: #F5F5F5; border: 1px solid #d6e9c6;padding: 10px; width:100%;'>
                        <div id = 'custfield_core'><b>SEO Fields:</b>
                        </div>

                        </td>
                        </tr>
                        <?php
                        foreach($impCE->defCols as $key => $value){
                                if(strstr($key,'SEO:')){
                                $value = str_replace('SEO:','',$value)
                        ?>
		
		<tr>
                                <td class="left_align" style='width:53%;padding-left:150px;'>
                                 <input type='hidden' name ='seofieldname<?php print($count); ?>' id = 'seofieldname<?php print($count); ?>' value = '<?php echo $key; ?>' />

                        <label class='wpfields'><?php print('<b>'.$value.'</b></label><br><label class="samptxt" style="padding-left:20px">[Name: '.$value.']'); ?></label>
                                </td>
                                <td>
                                        <select name="seomapping<?php print($count); ?>" id="seomapping<?php print($count); ?>">
                                        <option>-- Select --</option>
                                        <?php foreach($impCE->headers as $key1 => $value1){?>
                                                <option><?php echo $value1; ?></option>
                                        <?php }?>
                                        </select>

                                        <script type="text/javascript">
                                        jQuery("select#seomapping<?php print($count); ?>").find('option').each(function() {
                                                        if(jQuery(this).val() == "<?php print($value);?>") {
                                                        jQuery(this).prop('selected', true);
                                                        }
                                        });
                                        </script>
                                </td>
                                <td>
                                <td>
                                </td><td></td>
                                </tr>
                                        <?php
                                        $count++;
                        }
                        }?>
                <input type='hidden' id='seofields' name='seofields' value='<?php echo($count) ?>' />
                </table>
                <?php }   ?>
                <?php $basic_count = $count - 1; ?>
                <input type="hidden" id="basic_count" name="basic_count" value="<?php echo $basic_count; ?>" />
                <input type="hidden" id="corecustomcount" name="corecustomcount" value=0 />
		<div>
			<div class="goto_import_options" align=center>
		<div class="mappingactions" style="margin-top:26px;" >
		<input type='button' id='clear_mapping' class='clear_mapping btn btn-warning' name='clear_mapping' title = '<?php echo esc_attr__("Reset",'import-woocommerce'); ?>' value='<?php echo esc_attr__('Reset','import-woocommerce'); ?>' onclick='clearMapping();' style = 'float:left'/>
		</div>
		<div class="mappingactions" >
		<input type='submit' id='goto_importer_setting' title = '<?php echo esc_attr__("Next",'import-woocommerce'); ?>' class='goto_importer_setting btn btn-info' name='goto_importer_setting' value='<?php echo esc_attr__('Next','import-woocommerce'); ?> >>' /> 
		</div>
		</div> 
		</div>
		</div>
             <?php } ?>
		</div>
		</form>
		</td>
                </tr>
                <tr>
                <td>
		<h3><?php echo esc_html__('Settings and Performance','import-woocommerce'); ?></h3>
		<?php if(isset($_REQUEST['step'])  && sanitize_text_field($_REQUEST['step']) == 'importoptions') { ?>
		<div id='sec-three' <?php if(sanitize_text_field($_REQUEST['step'])!= 'importoptions'){ ?> style='display:none;' <?php } ?> >
                <?php if(isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES'])) { ?>
		<input type='hidden' id='current_record' name='current_record' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['current_record']; ?>' />
		<input type='hidden' id='tot_records' name='tot_records' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>' />
		<input type='hidden' id='checktotal' name='checktotal' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?>' />
		<input type='hidden' id='stepstatus' name='stepstatus' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['stepstatus']; ?>' />
		<input type='hidden' id='selectedImporter' name='selectedImporter' value='<?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['selectedImporter']; ?>' />
		<?php } ?>
                <input type='hidden' id='tmpLoc' name='tmpLoc' value='<?php echo Woocom_CONST_CSV_IMP_DIR; ?>' />
               <?php if(isset($_POST)) { ?>
		<input type='hidden' id='checkfile' name='checkfile' value='<?php echo sanitize_text_field($_POST['uploadedFile']); ?>' />
		<input type='hidden' id='uploadedFile' name='uploadedFile1' value='<?php echo sanitize_text_field($_POST['uploadedFile']); ?>' />
		<input type='hidden' id='inline_image_location' name='location_inlineimages' value='<?php echo sanitize_text_field($_POST['inline_image_location']); ?>' />
             <?php } ?>
		<!-- Import settings options -->
		<div class="postbox" id="options" style=" margin-bottom:0px;">
		<div class="inside">
                 <label id="importalign"><input type ='radio' id='importNow' name='importMode' value='' onclick='choose_import_mode(this.id);' checked/> <?php echo esc_html__("Import right away",'import-woocommerce');  ?> </label> 
                                        <label id="importalign"><input type ='radio' id='scheduleNow' name='importMode' value='' onclick='choose_import_mode(this.id);' disabled/> <?php echo esc_html__("Schedule now",'import-woocommerce'); ?><img src="<?php echo esc_url(WP_CONTENT_URL.'/plugins/'.Woocom_CONST_CSV_IMP_SLUG.'/images/pro_icon.gif');?>" title="PRO Feature"/> </label>
                  <div id='schedule' style='display:none'>
                                 <input type ='hidden' id='select_templatename' name='#select_templatename' value = '<?php if(isset($_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'])) { echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['templateid'] ; } ?>'>
                                    </div>
 <div id='importrightaway' style='display:block'>

		<form method="POST" >
		<ul id="settings">
		<li>
		<label id="importalign"><input name='duplicatecontent' id='duplicatecontent' type="checkbox" value=""> <?php echo esc_html__('Detect duplicate post content','import-woocommerce'); ?></label> <br>
		<input type='hidden' name='wpnoncekey' id='wpnoncekey' value='<?php echo $nonce_Key; ?>' />
		<label id="importalign"><input name='duplicatetitle' id='duplicatetitle' type="checkbox" value="" > <?php echo esc_html__('Detect duplicate post title','import-woocommerce'); ?></label> <br>
		 <label id="importalign"><?php echo esc_html__('No. of posts/rows per server request','import-woocommerce'); ?></label><span class="mandatory" style="margin-left:-13px;margin-right:10px">*</span> <input name="importlimit" id="importlimit" type="text" value="1" placeholder="10" onblur="check_allnumeric(this.value);"></label> <?php echo $impCE->helpnotes(); ?><br>
			<span class='msg' id='server_request_warning' style="display:none;color:red;margin-left:-10px;"><?php echo esc_html__('You can set upto','import-woocommerce'); ?> <?php echo $_SESSION['SMACK_MAPPING_SETTINGS_VALUES']['totRecords']; ?> <?php echo esc_html__('per request.','import-woocommerce'); ?></span>	
                <input type="hidden" id="currentlimit" name="currentlimit" value="0"/>
		<input type="hidden" id="tmpcount" name="tmpcount" value="0" />
		<input type="hidden" id="terminateaction" name="terminateaction" value="continue" />
		<label id="innertitle"><?php echo esc_html__('Inline image options','import-woocommerce'); ?></label><br />
		<label id='importalign'> <input type ='checkbox' id='multiimage' name='multiimage' value = ''> <?php echo esc_html__('Insert Inline Images','import-woocommerce'); ?><?php echo $impCE->imagehelpnotes(); ?></label><br>
		<input type='hidden' id='inlineimagevalue' name='inlineimagevalue' value='none' />
		</li>
		</ul>
		<input id="startbutton" class="btn btn-primary" type="button" value="<?php echo esc_attr__('Import Now','import-woocommerce'); ?>" style="color: #ffffff;background:#2E9AFE;" onclick="importRecordsbySettings('<?php echo site_url(); ?>');" >
		<input id="terminatenow" class="btn btn-danger btn-sm" type="button" value="<?php echo esc_attr__('Terminate Now','import-woocommerce'); ?>" style="display:none;" onclick="terminateProcess();" />
		<input class="btn btn-warning" type="button" value="<?php echo esc_attr__('Reload','import-woocommerce'); ?>" id="importagain" style="display:none" onclick="import_again();" />
                <input id="continuebutton" class="btn btn-lg btn-success" type="button" value="<?php echo esc_attr__('Continue','import-woocommerce'); ?>" style="display:none;color: #ffffff;" onclick="continueprocess();">
		<div id="ajaxloader" style="display:none"><img src="<?php echo esc_url(Woocom_CONST_CSV_IMP_DIR.'images/ajax-loader.gif');?>"> <?php echo esc_html__('Processing...','import-woocommerce'); ?></div>
           
		<div class="clear"></div>
		</form>
                 </div>
		<div class="clear"></div>
		<br>
		</div>
		</div>
                <?php } ?>
		<!-- Code Ends Here-->
		</div>
		</td>
		</tr>
		</table>
		</div>
                  <div style="width:100%;">
                                               <div id="accordion">
                                               <table class="table-importer">
                                               <tr>
                                               <td>
                                               <h3><?php echo esc_html__("Summary",'import-woocommerce'); ?></h3>
                                                <div id='reportLog' class='postbox'  style='display:none;'>
                                                <input type='hidden' name = 'csv_version' id = 'csv_version' value = "<?php if(isset($_POST['uploaded_csv_name'])) { echo sanitize_file_name($_POST['uploaded_csv_name']); } ?>">
                                                <div id="logtabs" class="logcontainer">
                                                <div id="log" class='log'>
                                                </div>
                                                </div>
                                                </div>
                                                </td>
                                                </tr>
                                                </table>
                                                </div>
                                                </div>

	</div>
	
