/* global Craft */
/* global Garnish */
/* global jQuery */

(function($) {
    Craft.OrdersWidget = Garnish.Base.extend({
        settings: null,
        $widget: null,
        $chartContainer: null,

        init: function(widgetId) {
            const widgetSelector = '#widget' + widgetId;
            this.$widget = $(widgetSelector);
            this.$chartContainer = this.$widget.find('.orders-chart');
            this.updateChart();
        },

        updateChart: function() {
            this.$chartContainer.addClass('spinner');
            const self = this;

            Craft.postActionRequest(
                'snipcart/charts/get-orders-data',
                {
                    type: this.$chartContainer.data('chart-type'),
                    range: this.$chartContainer.data('chart-range'),
                },
                $.proxy(function(response, textStatus) {
                    // TODO: gracefully handle error
                    self.$chartContainer.removeClass('spinner');

                    if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                        const chart = new Craft.charts.Area(self.$chartContainer);
                        const chartDataTable = new Craft.charts.DataTable(response.dataTable);

                        const chartSettings = {
                            orientation: response.orientation,
                            dataScale: response.scale,
                            formats: response.formats
                        };

                        chart.draw(chartDataTable, chartSettings);
                    }
                })
            );
        },
    });
})(jQuery);