<div id="monthly_visas"></div>

<script type="text/javascript">
    
    google.charts.setOnLoadCallback(drawExpatMap);

    function drawExpatMap() {
        var request = new isarray_request();
        
        request.init({
        url: '/admin/get-monthly-visa-breakdown',
        type: 'access',
        loading: true,
        success: function(response) {

            var data = google.visualization.arrayToDataTable(response.data);
            
             var options = {
                legend: { position: 'bottom' }
            };
            
            var chart = new google.visualization.LineChart(document.getElementById('monthly_visas'));
            chart.draw(data, options);
        }
        
    });
        
        
    }
</script>