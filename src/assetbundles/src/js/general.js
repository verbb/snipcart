/* global Craft */

import '../css/general.css';


/**
 * @todo Get serious and rebuild this with Vue
 */
const loadCartsBtn = document.getElementById('load-carts');
const cartsTable = document.getElementById('carts');

if (loadCartsBtn !== null) {
    loadCartsBtn.onclick = fetchCarts;
}

function fetchCarts() {
    Craft.postActionRequest(
        'snipcart/carts/get-next-carts',
        { continuationToken: loadCartsBtn.getAttribute('data-continuation-token') },
        function(response, textStatus) {
            if (textStatus === 'success' && typeof (response.error) === 'undefined') {
                if (response.hasMoreResults) {
                    loadCartsBtn.setAttribute('data-continuation-token', response.continuationToken);
                } else {
                    loadCartsBtn.classList.add('hidden');
                }

                const cartsTableBody = cartsTable.querySelector('tbody');

                response.items.forEach(function(cart){
                    const row = document.createElement('tr');

                    row.setAttribute('data-id', cart.token);
                    row.setAttribute('data-name', cart.email);

                    const nameColumn = document.createElement('td');
                    nameColumn.innerHTML = `<a href="${cart.cpUrl}">${cart.billingAddress.name}</a>`;

                    const emailColumn = document.createElement('td');
                    emailColumn.innerHTML = cart.email;

                    const statusColumn = document.createElement('td');
                    statusColumn.innerHTML = cart.status;

                    const dateColumn = document.createElement('td');
                    dateColumn.innerHTML = cart.modificationDate;

                    const totalColumn = document.createElement('td');
                    totalColumn.innerHTML = cart.total;

                    row.appendChild(nameColumn);
                    row.appendChild(emailColumn);
                    row.appendChild(statusColumn);
                    row.appendChild(dateColumn);
                    row.appendChild(totalColumn);

                    cartsTableBody.appendChild(row);
                });
            }
        }
    );
}

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
