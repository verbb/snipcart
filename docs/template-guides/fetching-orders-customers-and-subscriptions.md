# Fetching Orders, Customers and Subscriptions

Snipcart stores orders, customers and subscriptions on its own servers, not in Craft. When a template needs one of them, such as an order summary page you link to from your own emails, fetch it from Snipcart's API by its ID. Each call uses your secret API key (or the secret test key while [test mode](docs:feature-tour/test-mode) is on), so set that up first in the plugin's settings.

## Getting an Order

`craft.snipcart.getOrder(token)` takes an order's token, the long unique ID Snipcart gives every order, and returns the order, or `null` when Snipcart has no order with that token. This example assumes a route that passes the token from the URL to the template as `token`:

```twig
{% set order = craft.snipcart.getOrder(token) %}

{% if order %}
    <h1>Order {{ order.invoiceNumber }}</h1>
    <p>Placed on {{ order.creationDate | date('long') }}. Status: {{ order.status }}</p>

    <ul>
        {% for item in order.items %}
            <li>{{ item.quantity }} × {{ item.name }}, {{ craft.snipcart.formatCurrency(item.totalPrice, order.currency) }}</li>
        {% endfor %}
    </ul>

    <p>Total: {{ craft.snipcart.formatCurrency(order.finalGrandTotal, order.currency) }}</p>

    {% if order.trackingUrl %}
        <p><a href="{{ order.trackingUrl }}">Track your parcel</a></p>
    {% endif %}
{% else %}
    <p>We couldn't find that order.</p>
{% endif %}
```

Anyone who has an order's token can see that order on this page, so only share the link with the customer who placed it, and don't use an invoice number or other guessable value in its place. The order holds the customer's email address, addresses and payment details, so only output what the customer needs to see.

## Getting a Customer

`craft.snipcart.getCustomer(id)` takes a Snipcart customer ID and returns the customer, or `null`. A customer holds their `email`, billing and shipping address details such as `billingAddressName`, and `statistics`, with the number of orders they've placed and the total they've spent:

```twig
{% set customer = craft.snipcart.getCustomer(customerId) %}

{% if customer %}
    <p>{{ customer.billingAddressName }} has placed {{ customer.statistics.ordersCount }} orders, totalling {{ craft.snipcart.formatCurrency(customer.statistics.ordersAmount, craft.snipcart.defaultCurrency) }}.</p>
{% endif %}
```

## Getting a Subscription

`craft.snipcart.getSubscription(id)` takes a Snipcart subscription ID and returns the subscription, or `null`. A subscription has the plan's `name`, a `status` of `Active`, `Paused` or `Canceled`, and the `nextBillingDate`:

```twig
{% set subscription = craft.snipcart.getSubscription(subscriptionId) %}

{% if subscription and subscription.status == 'Active' %}
    <p>Your {{ subscription.name }} subscription renews on {{ subscription.nextBillingDate | date('long') }}.</p>
{% endif %}
```

## How Responses Are Cached

Every call is a request to Snipcart's API, so the plugin caches the responses for five minutes by default. The [`cacheResponses`](docs:get-started/configuration#cacheresponses) setting turns this off, and [`cacheDurationLimit`](docs:get-started/configuration#cachedurationlimit) changes how long a response is kept, in seconds. Changes made in Snipcart, such as an order being marked as shipped, can take that long to appear.

If Snipcart rejects your secret key or responds with a server error, the call throws an error rather than returning `null`, so check your keys if a page that fetches orders stops loading.
