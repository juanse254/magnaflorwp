jQuery( document ).ready(function() {
   jQuery('.dropdown-toggle').dropdown('toggle');
   var checkmodule = document.getElementById('checkmodule').value;
        if(checkmodule != 'dashboard' && checkmodule !='settings') {
                var get_log = document.getElementById('log').innerHTML;
                   if (!jQuery.trim(jQuery('#log').html()).length) {      
                  document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">'+ import_woocom_translate.dashchartMsg+'</p>';
                }
 }
  if (checkmodule != 'settings') {
	 var checkfile = jQuery('#checkfile').val();
	 var dir_path = jQuery('#dirpathval').val();
	 var uploadedFile = jQuery('#uploadedFile').val();
	 var noncekey = jQuery('#nonceKey').val();
	 var get_log = jQuery('#log').val();
	 var checkmodule = jQuery('#checkmodule').val();
	 if (!jQuery.trim(jQuery('#log').html()).length) {
		 if(checkmodule != 'dashboard') 
			 document.getElementById('log').innerHTML = '<p style="margin:15px;color:red;">'+ import_woocom_translate.dashchartMsg+'</p>';
	 }
	 
 }
});

function prepareUpload(){
        var check_upload_dir = document.getElementById('is_uploadfound').value;
        if(check_upload_dir == 'notfound'){
                document.getElementById('browsefile').style.display = 'none';
                jQuery('#defaultpanel').css('visibility','hidden');
                jQuery('<p/>').text("").appendTo('#warning');
                jQuery( "#warning" ).empty();
                jQuery('#warning').css('display','inline');
                jQuery('<p/>').text("Warning:   Sorry. There is no uploads directory Please create it with write permission.").appendTo('#warning');
                jQuery('#warning').css('color','red');
                jQuery('#warning').css('font-weight','bold');
                jQuery('#progress .progress-bar').css('visibility','hidden');
        }
        else{
                        var uploadPath = document.getElementById('uploaddir').value;
                        var curraction = document.getElementById('current_module').value;
                        var frmdata = new FormData();
                        var uploadfile_data = jQuery('#fileupload').prop('files')[0];
                        frmdata.append('files', uploadfile_data);
                        frmdata.append('action', 'woocom_uploadfilehandle');
                        frmdata.append('curr_action', curraction);
                        frmdata.append('uploadPath', uploadPath);
			frmdata.append('securekey',import_woocom_translate.woocom_secureKey);
                        jQuery.ajax({
                                url: ajaxurl,
                                type: 'post',
                                data: frmdata,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                        var fileobj = JSON.parse(data);
                                        jQuery.each(fileobj, function (objkey, objval) {
                                                jQuery.each(objval, function (o_key, file) {
                                                        document.getElementById('uploadFileName').value = file.name;
                                                        var filewithmodule = file.uploadedname.split(".");
                                                        var check_file = filewithmodule[filewithmodule.length - 1];
                                                        if (check_file != "csv" && check_file != "txt") {
                                                                alert(import_woocom_translate.fileformat_Msg);
                                                                return false;
                                                        }
                                                        if (check_file == "csv") {
                                                                var filenamecsv = file.uploadedname.split(".csv");
                                                                file.uploadedname = filenamecsv[0] + "-" + curraction + ".csv";
                                                        }
                                                        if (check_file == "txt") {
                                                                var filenametxt = file.uploadedname.split(".txt");
                                                                file.uploadedname = filenametxt[0] + "-" + curraction + ".txt";
                                                        }
                                                        document.getElementById('upload_csv_realname').value = file.uploadedname;
                                                        document.getElementById('progressbar').value = '100';

                                                        var get_version1 = file.name.split("-" + curraction);
                                                        var get_version2 = get_version1[1].split(".csv");
                                                        var get_version3 = get_version2[0].split("-");
                                                        document.getElementById('current_file_version').value = get_version3[1];
                                                        jQuery('#uploadedfilename').val(file.uploadedname);
                                                        jQuery("#filenamedisplay").empty();
                                                        if (file.size > 1024 && file.size < (1024 * 1024)) {
                                                                var fileSize = (file.size / 1024).toFixed(2) + ' kb';
                                                        }
                                                        else if (file.size > (1024 * 1024)) {
                                                                var fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' mb';
                                                        }
                                                        else {
                                                                var fileSize = (file.size) + ' byte';
                                                        }
                                                        jQuery('<p/>').text((file.name) + ' - ' + fileSize).appendTo('#filenamedisplay');
                                                        jQuery('#importfile').attr('disabled', false);
                                               });
                                       });
                                }
                        });
        }
}

function selectpoststatus()
{
	var poststate = document.getElementById('wpfields').value;
        var importer = document.getElementById('selectedImporter').value;
        var ps = document.getElementById("importallwithps");
        var selectedpsindex = ps.options[ps.selectedIndex].value;
        if(selectedpsindex == 6){
                document.getElementById('globalpassword_label').style.display = "block";
                document.getElementById('globalpassword_text').style.display = "block";
                document.getElementById('globalpassword_txt').focus();
        }
        else{
                document.getElementById('globalpassword_label').style.display = "none";
                document.getElementById('globalpassword_text').style.display = "none";
        }
        var totdropdown= document.getElementById('h2').value;
        var total = parseInt(totdropdown);
        if(selectedpsindex != 0)
        {
                for(var i=0;i < poststate;i++)
                {
                        dropdown = document.getElementById("fieldname"+i);
                        if(dropdown.value == "post_status"){
                                document.getElementById("mapping"+i).selectedIndex = "0";
                        }

                }
        }
}

function changefield()
{
        var importer = document.getElementById('selectedImporter').value;
	var poststate = document.getElementById('wpfields').value;
        for(var i=0;i < poststate;i++)
                {
                        dropdown = document.getElementById("fieldname"+i);
                        if(dropdown.value == "post_status"){

                        if(document.getElementById("mapping"+i).selectedIndex != 0)
                                document.getElementById("importallwithps").selectedIndex = "0";
                        }
        }
        var ps = document.getElementById("importallwithps");
        var selectedpsindex = ps.options[ps.selectedIndex].value;
        if(selectedpsindex == 0){
                document.getElementById('globalpassword_label').style.display = "none";
                document.getElementById('globalpassword_text').style.display = "none";
        }
}	

// Function for add customfield
function addcorecustomfield(id){
var table_id = id;
var newrow = table_id.insertRow(-1);
var count = document.getElementById('basic_count').value;
count = parseInt(count)+1;
newrow.id = 'custrow'+count;
var filename = document.getElementById('uploadedFile').value;
var row_count = document.getElementById('corecustomcount').value;
        jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                        'filename' : filename,
                        'corecount' : count,
                        'action' : 'addcustomfield',
			'securekey' : import_woocom_translate.woocom_secureKey,
                },
                success: function (response) {
                        newrow.innerHTML = response;
                        row_count = parseInt(row_count) + 1;
                        document.getElementById('corecustomcount').value = row_count;
                        document.getElementById('basic_count').value = count;
                }
        });

}

function clearMapping()
{
	var importer = document.getElementById('selectedImporter').value;
	var wpfield = document.getElementById('wpfields').value;
	var termfield = 0;
	if(document.getElementById('termfields')){
		termfield = document.getElementById('termfields').value;
	}
	for(var j=0;j<wpfield;j++){
		document.getElementById('mapping'+j).selectedIndex = "0";
	}
	var customfield = document.getElementById('customfields').value;
	for(var j=wpfield;j<parseInt(customfield);j++) {
		document.getElementById('coremapping'+j).selectedIndex = "0";
	}
	for(var j = customfield; j < termfield; j++) {
		document.getElementById('term_mapping'+j).selectedIndex = "0";
	}
	if(document.getElementById("seofields") && document.getElementById("addcorecustomfields")){
		var seofield = document.getElementById('seofields').value;
		var addcorecustomfield= document.getElementById('basic_count').value;
	}
	if(seofield != null && addcorecustomfield != null){
		if(termfield != 0){
			for(var j=termfield;j<seofield;j++) {
				document.getElementById('seomapping'+j).selectedIndex = "0";
			}
		}
		else {
			for(var j=customfield;j<seofield;j++) {
				document.getElementById('seomapping'+j).selectedIndex = "0";
			}
		}
		for(var j=seofield;j<=addcorecustomfield;j++) {
			document.getElementById('addcoremapping'+j).selectedIndex = "0";
		}
	}
	else if(document.getElementById("seofields")){
		var seofield = document.getElementById('seofields').value;
		if(seofield != null){
			for(var j=termfield;j<seofield;j++) {
				document.getElementById('seomapping'+j).selectedIndex = "0";
			}
		}
	}
	else if(document.getElementById("addcorecustomfields")){
		var addcorecustomfield= document.getElementById('basic_count').value;
		if(addcorecustomfield != null){
			for(var j=customfield;j<=addcorecustomfield;j++) {
				document.getElementById('addcoremapping'+j).selectedIndex = "0";
			}
		}
	}
}

function import_csv() 
{
    var ps = document.getElementById("importallwithps");
    var selectedpsindex = ps.options[ps.selectedIndex].value;
    var total = document.getElementById('wpfields').value;
    var importer = document.getElementById('selectedImporter').value;
    var header_count = document.getElementById('h2').value;
    var csvarray = new Array();
    var wparray = new Array();
    var val1, val2, error_msg, chk_status_in_csv, post_status_msg;
    val1 = val2 = post_status_msg = 'Off';
    for (var i = 0; i < total; i++) {
        var csvvalue = document.getElementById("mapping" + i).value;
        var wpvalue = document.getElementById("fieldname" + i).value;
        csvarray[i] = csvvalue;
        wparray[i] = wpvalue;
    }
    if (importer == 'post' || importer == 'page' || importer == 'woocommerce') { 
        chk_status_in_csv = document.getElementById('importallwithps').value;
        if (chk_status_in_csv != 0)
            post_status_msg = 'On';

	if (selectedpsindex == 6){
                var checkpwd = document.getElementById('globalpassword_txt').value;
                if( checkpwd != '')
                        val2='On';
        }

	for (var j = 0; j < wparray.length; j++) {
            if (wparray[j] == 'post_title' && csvarray[j] != '-- Select --') {
            	   val1 = 'On';
            }
            if (post_status_msg == 'Off') {
                if (wparray[j] == 'post_status' && csvarray[j] != '-- Select --')
                    post_status_msg = 'On';
            }
        }

	if (selectedpsindex == 6){
		if (val1 == 'On' && post_status_msg == 'On' && val2 == 'On') {
                        return true;
	        }
        	else {
            		error_msg = '';
	    		if (val2 == 'Off')
                		error_msg += "password,";
            		if (val1 == 'Off'){
				if(importer != 'woocommerce')
                  			error_msg += " post_title,";
				else
		  			error_msg += " product_name,";
	    		}
            		if (post_status_msg == 'Off'){
                		error_msg += " post_status";
	    		}
            		showMapMessages('error', 'Error: ' + error_msg + import_woocom_translate.mandatory_fieldmsg);
            		return false;
        	}
	}
	else {
                if (val1 == 'On' && post_status_msg == 'On') {
                        return true;
                }
                else {
                        error_msg = '';
                        if (val1 == 'Off'){
                                if(importer != 'woocommerce')
                                        error_msg += " post_title,";
                                else
                                        error_msg += " product_name,";
                        }
                        if (post_status_msg == 'Off'){
                                error_msg += " post_status";
                        }
                        showMapMessages('error', 'Error: ' + error_msg + import_woocom_translate.mandatory_fieldmsg);
                        return false;
                }
        }

    }

}


function showMapMessages(alerttype, msg) {
    jQuery("#showMsg").addClass("maperror");
    document.getElementById('showMsg').innerHTML = msg;
    document.getElementById('showMsg').className += ' ' + alerttype;
    document.getElementById('showMsg').style.display = '';
    jQuery("#showMsg").fadeOut(10000);
}

function filezipopen()
{
var advancemedia = document.getElementById('advance_media_handling').checked;
if(advancemedia == true)
        document.getElementById('filezipup').style.display = '';
else
        document.getElementById('filezipup').style.display = 'none';

}

function checkextension(filename)
{
var allowedextension ={ '.zip' : 1 };
var match = /\..+$/;
          var ext = filename.match(match);
          if (allowedextension[ext])
          {
                return true;
          }
          else
          {
                alert(import_woocom_translate.validate_zipfile);
                //will clear the file input box.
                location.reload();
                return false;
          }

}

function importRecordsbySettings(siteurl)
{
	var importlimit = document.getElementById('importlimit').value;
	var noncekey = document.getElementById('wpnoncekey').value; 
	var get_requested_count = importlimit; 
	var tot_no_of_records = document.getElementById('checktotal').value;
	var importas = document.getElementById('selectedImporter').value;
	var uploadedFile = document.getElementById('checkfile').value;
	var step = document.getElementById('stepstatus').value;
	var dupContent = document.getElementById('duplicatecontent').checked;
	var dupTitle = document.getElementById('duplicatetitle').checked;
	var currentlimit = document.getElementById('currentlimit').value;
	var tmpCnt = document.getElementById('tmpcount').value;
	var no_of_tot_records = document.getElementById('tot_records').value;
	var importinlineimage = false;
	var imagehandling = false;
	var inline_image_location = false;
	var currentModule = document.getElementById('current_module').value;
	if(currentModule != 'users' && currentModule != 'comments') {
		importinlineimage = document.getElementById('multiimage').checked;
		imagehandling = document.getElementById('inlineimagevalue').value;
		inline_image_location = document.getElementById('inline_image_location').value;
	}
	var get_log = document.getElementById('log').innerHTML;
	document.getElementById('reportLog').style.display = '';
	document.getElementById('terminatenow').style.display = '';
	if(get_requested_count != '') {
		//return true;
	} else {
		document.getElementById('showMsg').style.display = "";
		document.getElementById('showMsg').innerHTML = '<p id="warning-msg" class="alert alert-warning">'+import_woocom_translate.reqfieldmsg+'</p>';			jQuery("#showMsg").fadeOut(10000);
		return false;
	}
	if(parseInt(get_requested_count) <= parseInt(no_of_tot_records)) {
		document.getElementById('server_request_warning').style.display = 'none';
	} else {
		document.getElementById('server_request_warning').style.display = '';
		return false;
	}
	if(get_log == '<p style="margin:15px;color:red;">'+import_woocom_translate.dashchartMsg+'</p>'){
		document.getElementById('log').innerHTML = '<p style="margin-left:10px;color:red;">'+import_woocom_translate.importProgress_msg+'</p>';
		document.getElementById('startbutton').disabled = true;
	}
	document.getElementById('ajaxloader').style.display="";
	var tempCount = parseInt(tmpCnt);
	var totalCount = parseInt(tot_no_of_records);
	if(tempCount >= totalCount){
		document.getElementById('ajaxloader').style.display="none";
		document.getElementById('startbutton').style.display="none";
		document.getElementById('importagain').style.display="";
		document.getElementById('terminatenow').style.display = "none";
		return false;
	}
	var advancemedia = "";
	if(importas == 'post' || importas == 'page' || importas == 'woocommerce')
		advancemedia = document.getElementById('advance_media_handling').checked;
	var postdata = new Array();
	postdata = {'dupContent':dupContent,'dupTitle':dupTitle,'importlimit':importlimit,'limit':currentlimit,'totRecords':tot_no_of_records,'selectedImporter':importas,'uploadedFile':uploadedFile,'tmpcount':tmpCnt,'importinlineimage':importinlineimage,'inlineimagehandling':imagehandling,'inline_image_location':inline_image_location,'advance_media':advancemedia,'wpnonce':noncekey}

	var tmpLoc = document.getElementById('tmpLoc').value;
	jQuery.ajax({
type: 'POST',
url: ajaxurl,
data: {
'action'   : 'importcsvByRequest',
'postdata' : postdata,
'siteurl'  : siteurl,
},
success:function(data) {
if (parseInt(tmpCnt) == parseInt(tot_no_of_records)) {
document.getElementById('terminatenow').style.display = "none";
}
if (parseInt(tmpCnt) < parseInt(tot_no_of_records)) {
var terminate_action = document.getElementById('terminateaction').value;
currentlimit = parseInt(currentlimit) + parseInt(importlimit);
document.getElementById('currentlimit').value = currentlimit;
console.log('impLmt: ' + importlimit + 'totRecds: ' + tot_no_of_records);
document.getElementById('tmpcount').value = parseInt(tmpCnt) + parseInt(importlimit);
if (terminate_action == 'continue') {
setTimeout(function () {
	importRecordsbySettings()
	}, 0);
} else {
	document.getElementById('log').innerHTML += data + '<br/>';
	if (parseInt(tmpCnt) < parseInt(tot_no_of_records) - 1)
		document.getElementById('log').innerHTML += "<p style='margin-left:10px;color:red;'>" + import_woocom_translate.terminateImport_msg + "</p>";
	document.getElementById('ajaxloader').style.display = "none";
	document.getElementById('startbutton').style.display = "none";
	document.getElementById('terminatenow').style.display = "none";
	document.getElementById('continuebutton').style.display = "";
	return false;
}
} else {
	document.getElementById('ajaxloader').style.display = "none";
	document.getElementById('startbutton').style.display = "none";
	document.getElementById('importagain').style.display = "";
	return false;
}
document.getElementById('log').innerHTML += data + '<br/>';

},
error: function(errorThrown){
	       console.log(errorThrown);
       }
});
}

// Terminate import process
function terminateProcess(){
	document.getElementById('terminateaction').value = 'terminate';
}

function continueprocess() {
    var tot_no_of_records = document.getElementById('checktotal').value;
    var tmpCnt = document.getElementById('tmpcount').value;
    var currentlimit = document.getElementById('currentlimit').value;
            var importlimit = document.getElementById('importlimit').value;
        var tot_no_of_records = document.getElementById('checktotal').value;

    if (parseInt(tmpCnt) > parseInt(tot_no_of_records)) {
        document.getElementById('terminatenow').style.display = "none";
    } else {
        document.getElementById('terminatenow').style.display = "";
    }
    if (parseInt(tmpCnt) < parseInt(tot_no_of_records))
	 document.getElementById('log').innerHTML += "<div style='margin-left:10px;color:green;'>"+import_woocom_translate.continueImport_msg+"</div></br>";
    document.getElementById('ajaxloader').style.display = "";
    document.getElementById('startbutton').style.display = "";
    document.getElementById('continuebutton').style.display = "none";
    document.getElementById('terminateaction').value = 'continue';
    setTimeout(function () {
        importRecordsbySettings()
    }, 0);
}

function saveSettings(){
      jQuery('#ShowMsg').css("display", "");
      jQuery('#ShowMsg').delay(2000).fadeOut();
}

function Reload(){
jQuery('#importfile').attr('disabled', true);
jQuery('#advance_media_handling').prop('checked', false);
window.location.reload();
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(typeof haystack[i] == 'object') {
            if(arrayCompare(haystack[i], needle)) {
		 return true;
	    }
        } else {
            if(haystack[i] == needle) {
		 return true;
	    }
        }
    }
    return false;
}

function import_again(){
	var get_current_url = document.getElementById('current_url').value;
	window.location.assign(get_current_url);
}

function check_allnumeric(inputtxt)  
{  
	var numbers = /^[0-9]+$/;  
	if(inputtxt.match(numbers))  
	{  
		return true;
	}  
	else  
	{  
		if(inputtxt == '')
			alert(import_woocom_translate.reqfieldmsg);
		else
			alert(import_woocom_translate.validate_Recordnum);  
		return false;  
	}  
}

function choose_import_method(id) {
        if(id == 'uploadfilefromcomputer') {
                document.getElementById('boxmethod1').style.border = "1px solid #ccc";
                document.getElementById('method1').style.display = '';
                document.getElementById('method1').style.height = '40px';
}
}
function choose_import_mode(id) {
        if(id == 'importNow') {
               document.getElementById('importrightaway').style.display='';
               document.getElementById('reportLog').style.display='';
               document.getElementById('schedule').style.display='none';
         }
        if(id == 'scheduleNow') {
               document.getElementById('schedule').style.display='';
               document.getElementById('importrightaway').style.display='none';
               document.getElementById('reportLog').style.display='none';
         }
}

