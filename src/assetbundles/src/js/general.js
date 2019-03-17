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
