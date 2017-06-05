jQuery( document ).ready(function() {
var get_module = document.getElementById('checkmodule').value;
if(get_module == 'dashboard') {
	piechart();
	linechart();
}
});

function piechart()
{
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'firstcsvchart',
                    'postdata' : 'firstchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(data) {
                var val = JSON.parse(data);
                if (val['label'] == 'No Imports Yet') {
                document.getElementById('pieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
                return false;
                }
                Morris.Donut({
                        element: 'pieStats',
                        data: val,
               });
        }
});
}

function linechart() {
jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
                    'action'   : 'secondcsvchart',
                    'postdata' : 'secondchartdata',
                },
          dataType: 'json',
          cache: false,
          success: function(result) {
                var val = JSON.parse(result);
                var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                 Morris.Line({
                        element: 'lineStats',
                        data   : val,
                        xkey: 'year',
                        ykeys: ['post', 'page','woocommerce'],
                        labels: ['post', 'page','woocommerce'],
                        lineColors:['green','red','blue'],
                        xLabelFormat: function(x) { // <--- x.getMonth() returns valid index
                                var month = months[x.getMonth()];
                                return month;
                        },
                        dateFormat: function(x) {
                                var month = months[new Date(x).getMonth()];
                                return month;
                        },

                });
        }
});
}

 
