/* global Craft */

const chartContainer = document.getElementById('snipcart-orders-chart');

Craft.postActionRequest(
    'snipcart/charts/get-orders-data', 
    {
        type: chartContainer.getAttribute('data-chart-type'),
        range: chartContainer.getAttribute('data-chart-range'),
    }, 
    $.proxy(function(response, textStatus) {
        if (textStatus === 'success' && typeof (response.error) === 'undefined') {

            // TODO: add spinner
            var chart = new Craft.charts.Area(chartContainer);
            var chartDataTable = new Craft.charts.DataTable(response.dataTable);

            var chartSettings = {
                orientation: response.orientation,
                dataScale: response.scale,
                formats: response.formats
            };

            chart.draw(chartDataTable, chartSettings);

            window.dashboard.grid.on('refreshCols', $.proxy(this, 'handleGridRefresh'));
        }
    })
);
