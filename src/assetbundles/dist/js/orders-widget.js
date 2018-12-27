(function($) {
    "use strict";

    Craft.postActionRequest('snipcart/charts/get-orders-data', {}, $.proxy(function(response, textStatus) {
        if (textStatus === 'success' && typeof (response.error) === 'undefined') {
            console.log(response);

            var chart = new Craft.charts.Area($('#snipcart-orders-chart'));

            var chartDataTable = new Craft.charts.DataTable(response.dataTable);

            var chartSettings = {
                orientation: response.orientation,
                dataScale: response.scale,
                formats: response.formats
            };

            chart.draw(chartDataTable, chartSettings);

            window.dashboard.grid.on('refreshCols', $.proxy(this, 'handleGridRefresh'));
        }
    }));
})(jQuery);
