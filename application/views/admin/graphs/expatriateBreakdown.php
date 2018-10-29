

<div id="regions_div"></div>


<script type="text/javascript">
    
    google.charts.setOnLoadCallback(drawExpatMap);

    function drawExpatMap() {
        var request = new isarray_request();
        
        request.init({
        url: '/admin/get-expatriate-data',
        type: 'access',
        loading: true,
        success: function(response) {

            var data = google.visualization.arrayToDataTable(response.data);
            var options = {
                colorAxis: {colors: ['#edc79e', '#3C4C6C']}
            };
            var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
            chart.draw(data, options);
        }
        
    });
        
        
    }
</script>