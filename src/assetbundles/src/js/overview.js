/* global Craft */

import ApexCharts from 'apexcharts'

const statPanels = document.getElementById('stat-panels');
const updateStatsBtn = document.getElementById('update-stats-button');
const startDateField = document.querySelector('input[name="startDate[date]"]');
const endDateField = document.querySelector('input[name="endDate[date]"]');
const chartContainer = document.getElementById('overview-chart');

var chart;

if (statPanels) {
    fetchStatPanels();
    updateChart();

    updateStatsBtn.onclick = function (e) {
        e.preventDefault();
        fetchStatPanels();
        updateChart();
    }
}

function fetchStatPanels()
{
    const ordersCount = document.getElementById('stat-ordersCount');
    const ordersSales = document.getElementById('stat-ordersSales');
    const averageOrdersValue = document.getElementById('stat-averageOrdersValue');
    const newCustomers = document.getElementById('stat-newCustomers');
    const returningCustomers = document.getElementById('stat-returningCustomers');
    const averageCustomerValue = document.getElementById('stat-averageCustomerValue');

    const spinnerMarkup = '<div class="spinner"></div>';

    ordersCount.innerHTML = spinnerMarkup;
    ordersSales.innerHTML = spinnerMarkup;
    averageOrdersValue.innerHTML = spinnerMarkup;
    newCustomers.innerHTML = spinnerMarkup;
    returningCustomers.innerHTML = spinnerMarkup;
    averageCustomerValue.innerHTML = spinnerMarkup;

    Craft.postActionRequest(
        'snipcart/overview/get-stats',
        {
            startDate: startDateField.value,
            endDate: endDateField.value,
        },
        function (response, textStatus) {
            if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                ordersCount.innerHTML = response.stats.ordersCount;
                ordersSales.innerHTML = response.stats.ordersSales;
                averageOrdersValue.innerHTML = response.stats.averageOrdersValue;
                newCustomers.innerHTML = response.stats.customers.newCustomers;
                returningCustomers.innerHTML = response.stats.customers.returningCustomers;
                averageCustomerValue.innerHTML = response.stats.averageCustomerValue;
            }
        }
    );
}

Array.prototype.max = function () {
    return Math.max.apply(null, this);
};

function updateChart()
{
    chartContainer.classList.add('spinner');

    Craft.postActionRequest(
        'snipcart/charts/get-combined-data',
        {
            startDate: startDateField.value,
            endDate: endDateField.value,
        },
        function (response, textStatus) {
            // TODO: gracefully handle error
            chartContainer.classList.remove('spinner');

            if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                const maxOrders = response.series[0].data.max();
                const maxSales = response.series[1].data.max();

                const options = {
                    chart: {
                        fontFamily: "system-ui, BlinkMacSystemFont, -apple-system, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif",
                        height: 220,
                        type: 'line',
                        toolbar: {
                            show: true,
                            offsetX: 0,
                            offsetY: 15,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false,
                            },
                        },
                        animations: {
                            speed: 350,
                            easing: 'easeout',
                            animateGradually: {
                                enabled: false
                            }
                        }
                    },
                    colors: ['#8f98a3', '#0d78f2'],
                    dataLabels: {
                        enabled: false
                    },
                    markers: {
                        size: 2,
                        hover: {
                            size: 4,
                        },
                        strokeWidth: 0,
                        fillOpacity: 0.5,
                    },
                    fill: {
                        type: ['solid', 'gradient'],
                        colors: ['#8f98a3', '#0d78f2'],
                        opacity: 1,
                        gradient: {
                            type: "vertical",
                            shadeIntensity: 0.3,
                            opacityFrom: 0.5,
                            opacityTo: 0,
                            stops: [0, 90],
                            colorStops: []
                        },
                      
                    },
                    series: response.series,
                    xaxis: {
                        categories: response.columns,
                        labels: {
                            show: false,
                            formatter: function (val) {
                                if (val === undefined) {
                                    return val;
                                }
                                const datePieces = val.split('-'); // YYYY-MM-DD
                                const year = parseInt(datePieces[0]);
                                const month = parseInt(datePieces[1]);
                                const day = parseInt(datePieces[2]);

                                if (year && month && ! day) {
                                    return `${month}/${year}`;
                                }

                                return `${month}/${day}`;
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                    },
                    yaxis: [
                        {
                            seriesName: 'Orders',
                            min: 0,
                            max: maxOrders * 2,
                            decimalsInFloat: 0,
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: true,
                            },
                            labels: {
                                show: false,
                            }
                        },
                        {
                            min: 0,
                            max: getRoundedMaxForChart(maxSales),
                            seriesName: 'Sales',
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false,
                            },
                            labels: {
                                show: true,
                                offsetX: -20,
                                style: {
                                    color: '#8f98a3',
                                },
                                formatter: function(val) {
                                    if (response.formats.currencySymbol !== undefined) {
                                        return formatCurrencyValue(response.formats.currencySymbol, val);
                                    }

                                    return val;
                                }
                            }
                        }
                    ],
                    tooltip: {
                        enabled: true,
                        x: {
                            show: false
                        },
                        y: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: '#e3e5e8',
                        strokeDashArray: 1,
                        padding: {
                            top: 10,
                            left: -10,
                        },
                    },
                    stroke: {
                        width: 2,
                        show: true,
                        curve: 'straight',
                        lineCap: 'round',
                    },
                    legend: {
                        horizontalAlign: 'right'
                    }
                }

                if (chart) {
                    chart.updateOptions(options);
                } else {
                    chart = new ApexCharts(
                        chartContainer,
                        options
                    );

                    chart.render();
                }
            }
        }
    );
}

function formatCurrencyValue(symbol, value)
{
    const floatValue = parseFloat(value);
    const formattedNumber = floatValue.toLocaleString(undefined, {maximumFractionDigits:2}).replace('.00', '');

    return symbol + formattedNumber;
}

function getRoundedMaxForChart(value)
{
    const intValue = parseInt(value);
    let roundString = '1';

    // round to the nearest second digit
    const roundTarget = getNumberOfDigits(intValue) - 1;

    while (roundString.length < roundTarget) {
        roundString += '0';
    }

    const roundAdjuster = parseInt(roundString);
    const rounded = Math.ceil(value / roundAdjuster) * roundAdjuster;

    return rounded;
}

function getNumberOfDigits(n)
{
    if (n < 0) { return 0; }
    if (n < 10) { return 1; }
    if (n < 100) { return 2; }
    if (n < 1000) { return 3; }
    if (n < 10000) { return 4; }
    if (n < 100000) { return 5; }
    if (n < 1000000) { return 6; }
    if (n < 10000000) { return 7; }
    if (n < 100000000) { return 8; }
    if (n < 1000000000) { return 9; }
    /*      2147483647 is 2^31-1 - add more ifs as needed
       and adjust this final return as well. */
    return 10;
}