jQuery(document).ready(function () {

	jQuery("body").on("input", "#wplc_enable_sn_webhook", function() {
        var res = jQuery(this).val();
        res = res.substring(0, 5);
        var res2 = jQuery(this).val();
        res2 = res2.substring(0, 6);

        if (res === "http:") {
           newurl = jQuery(this).val().substr(5);     
           jQuery(this).val(newurl);
        } else if (res2 === "https:") {
           newurl = jQuery(this).val().substr(6);     
           jQuery(this).val(newurl);
        }
        



    });

});
