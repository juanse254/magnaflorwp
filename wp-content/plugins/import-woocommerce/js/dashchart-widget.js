jQuery( document ).ready(function() {
pie_chart();
line_chart();
});


function pie_chart()
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
                document.getElementById('woocompieStats').innerHTML = "<h2 style='color: red;text-align: center;padding-top: 100px;' >No Imports Yet</h2>";
                return false;
                }
                Morris.Donut({
                        element: 'woocompieStats',
                        data: val,
                });
        }
});
}

function line_chart() {
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
                        element: 'woocomlineStats',
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


