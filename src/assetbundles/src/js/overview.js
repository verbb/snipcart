/* global Craft */

import ApexCharts from 'apexcharts'

const statPanels = document.getElementById('stat-panels');
//const ordersTableBody = document.querySelector('#stat-orders tbody');
//const customersTableBody = document.querySelector('#stat-customers tbody');

if (statPanels) {
    fetchStatPanels();
    //fetchOrderAndCustomerSummary();
}

function fetchStatPanels() {
    Craft.postActionRequest(
        'snipcart/overview/get-stats',
        {},
        function(response, textStatus) {
            if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                const ordersCount = document.getElementById('stat-ordersCount');
                ordersCount.innerHTML = response.stats.ordersCount;

                const ordersSales = document.getElementById('stat-ordersSales');
                ordersSales.innerHTML = response.stats.ordersSales;

                const averageOrdersValue = document.getElementById('stat-averageOrdersValue');
                averageOrdersValue.innerHTML = response.stats.averageOrdersValue;

                const newCustomers = document.getElementById('stat-newCustomers');
                newCustomers.innerHTML = response.stats.customers.newCustomers;

                const returningCustomers = document.getElementById('stat-returningCustomers');
                returningCustomers.innerHTML = response.stats.customers.returningCustomers;

                const averageCustomerValue = document.getElementById('stat-averageCustomerValue');
                averageCustomerValue.innerHTML = response.stats.averageCustomerValue;
            }
        }
    );
}

/*
function fetchOrderAndCustomerSummary() {
    Craft.postActionRequest(
        'snipcart/overview/get-orders-customers',
        {},
        function(response, textStatus) {
            if (textStatus === 'success' && typeof (response.error) === 'undefined') {

                response.orders.items.forEach(function(order){
                    const row = document.createElement('tr');

                    row.setAttribute('data-id', order.token);
                    row.setAttribute('data-name', order.email);

                    const invoiceColumn = document.createElement('td');
                    invoiceColumn.innerHTML = `<a href="${order.cpUrl}">${order.invoiceNumber}</a>`;

                    const dateColumn = document.createElement('td');
                    dateColumn.innerHTML = order.creationDate;
                    
                    const nameColumn = document.createElement('td');
                    nameColumn.innerHTML = order.billingAddressName;

                    const totalColumn = document.createElement('td');
                    totalColumn.innerHTML = order.finalGrandTotal;

                    row.appendChild(invoiceColumn);
                    row.appendChild(dateColumn);
                    row.appendChild(nameColumn);
                    row.appendChild(totalColumn);

                    ordersTableBody.appendChild(row);
                });

                response.customers.items.forEach(function(customer){
                    const row = document.createElement('tr');

                    row.setAttribute('data-id', customer.token);
                    row.setAttribute('data-name', customer.email);

                    const nameColumn = document.createElement('td');
                    nameColumn.innerHTML = `<a href="${customer.cpUrl}">${customer.billingAddressName}</a>`;

                    const ordersColumn = document.createElement('td');
                    ordersColumn.innerHTML = customer.statistics.ordersCount;
                    
                    const totalColumn = document.createElement('td');
                    totalColumn.innerHTML = customer.statistics.ordersAmount;

                    row.appendChild(nameColumn);
                    row.appendChild(ordersColumn);
                    row.appendChild(totalColumn);

                    customersTableBody.appendChild(row);
                });
            }
        }
    );
}
*/

const chartContainer = document.getElementById('overview-chart');


initChart();

Array.prototype.max = function() {
    return Math.max.apply(null, this);
};

function initChart() {
    chartContainer.classList.add('spinner');

    Craft.postActionRequest(
        'snipcart/charts/get-combined-data',
        {
            type: 'totalSales',
            range: 'monthly',
        },
        function(response, textStatus) {
            // TODO: gracefully handle error
            chartContainer.classList.remove('spinner');

            if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                const maxOrders = response.series[0].data.max();
                //const maxSales = response.series[1].data.max();
                //console.log(response);

                const options = {
                    chart: {
                        fontFamily: "system-ui, BlinkMacSystemFont, -apple-system, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif",
                        height: 220,
                        type: 'line',
                        toolbar: {
                            show: true
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
                    fill: {
                        type: 'solid'
                    },
                    series: response.series,
                    xaxis: {
                        categories: response.columns,
                        labels: {
                            show: false,
                            formatter: function (val) {
                                let date = new Date(val);
                                return date.getMonth() + '/' + date.getDate();
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
                            min: 0,
                            max: maxOrders * 2,
                            seriesName: 'Orders',
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
                            // min: 0,
                            // max: Math.ceil(maxSales),
                            seriesName: 'Sales',
                            forceNiceScale: true,
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false,
                            },
                            labels: {
                                show: true,
                                offsetX: -25,
                                style: {
                                    color: '#8f98a3',
                                },
                                formatter: function(val) {
                                    if (response.formats.currencySymbol !== undefined) {
                                        return response.formats.currencySymbol + val;
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
                            left: -15,
                        },
                    },
                    stroke: {
                        width: 2,
                        show: true,
                        curve: 'smooth',
                        lineCap: 'round',
                    },
                    legend: {
                        horizontalAlign: 'right'
                    }
                }

                var chart = new ApexCharts(
                    chartContainer,
                    options
                );

                chart.render();
            }
        }
    );
}