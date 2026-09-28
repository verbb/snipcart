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
                    const nameLink = document.createElement('a');

                    nameLink.href = cart.cpUrl;
                    nameLink.textContent = cart.billingAddress.name;
                    nameColumn.appendChild(nameLink);

                    const emailColumn = document.createElement('td');
                    emailColumn.textContent = cart.email;

                    const statusColumn = document.createElement('td');
                    statusColumn.textContent = cart.status;

                    const dateColumn = document.createElement('td');
                    dateColumn.textContent = cart.modificationDate;

                    const totalColumn = document.createElement('td');
                    totalColumn.textContent = cart.total;

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
