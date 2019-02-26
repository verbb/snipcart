---
meta:
    - name: description
      content: Reference of Snipcart plugin's Craft Services.
---

# Services

The Snipcart plugin exposes just about everything it can do with Services. Each is accessible everywhere via `Snipcart::$plugin->handle`, where `handle` is the "camelCase" name of the class. If you wanted to interact with the Snipcart REST API, for example, you could use `$someData = Snipcart::$plugin->api->get('pretend-endpoint')` to grab data from a GET request.

## [Api](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Api.php)

Interacts directly with the Snipcart API via `get()` or `post()`. Just specify your desired endpoint and an optional array of parameters.

### getClient()

Returns a configured instance of `GuzzleHttp\Client` ready to interact with the Snipcart REST API.

### get()

Perform a GET request with the Snipcart API.

| Argument               | Required | Description                             |
| ---------------------- | -------- | --------------------------------------- |
| **\$endpoint** string  | yes      | Snipcart API endpoint to be queried     |
| **\$parameters** array |          | array of parameters to be URL formatted |
| **\$useCache** bool    |          | whether or not to cache responses       |

Returns valid response data as single object or array of objects—otherwise `null`.

### post()

Perform post request to the Snipcart API.

| Argument              | Required | Description                             |
| --------------------- | -------- | --------------------------------------- |
| **\$endpoint** string | yes      | Snipcart API endpoint to be queried     |
| **\$data** array      |          | array of parameters to be URL formatted |

Returns decoded response data.

### put()

Perform put request to the Snipcart API.

| Argument              | Required | Description                             |
| --------------------- | -------- | --------------------------------------- |
| **\$endpoint** string | yes      | Snipcart API endpoint to be queried     |
| **\$data** array      |          | array of parameters to be URL formatted |

Returns decoded response data.

### delete()

Perform delete request to the Snipcart API.

| Argument              | Required | Description                             |
| --------------------- | -------- | --------------------------------------- |
| **\$endpoint** string | yes      | Snipcart API endpoint to be queried     |
| **\$data** array      |          | array of parameters to be URL formatted |

Returns decoded response data.

### tokenIsValid()

Ask Snipcart whether its provided token is genuine.

Used for webhook requests (except when `devMode` is true) to verify they were sent by Snipcart.

Tokens are deleted after this call, so it can only be used once to verify and tokens expire in one hour. Expect a 404 if the token is deleted or expired.

| Argument           | Required | Description                                                                       |
| ------------------ | -------- | --------------------------------------------------------------------------------- |
| **\$token** string | yes      | token to be validated, probably from `HTTP_X_SNIPCART_REQUESTTOKEN` post variable |

Returns `true` if valid, otherwise `false`.

### invalidateCache()

Invalidate any of the plugin's cached GET requests.

Doesn't return anything.

## [Carts](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Carts.php)

Gets abandoned cart info.

### listAbandonedCarts()

Lists the first page of abandoned cart objects.

::: warning
The plugin's interface matches others with pagination, but Snipcart's API follows a different pagination scheme here and returns `hasMoreResults` and `continuationToken`. `limit` and `offset` are effectively ignored.

This method will most likely be changed or deprecated in the future.
:::

Returns an object with the following properties.

-   **items** [AbandonedCart[]](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/AbandonedCart.php)
-   **totalItems** int
-   **offset** int
-   **continuationToken** string|null
-   **hasMoreResults** bool
-   **limit** int

### getAbandonedCart()

| Argument           | Required | Description                    |
| ------------------ | -------- | ------------------------------ |
| **\$token** string | yes      | unique ID of an abandoned cart |

Returns an [AbandonedCart model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/AbandonedCart.php) or `null`.

## [Customers](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Customers.php)

Lists and searches customer data.

### listCustomers()

List Snipcart customers.

| Argument           | Required | Description                                                                                                                                                                  |
| ------------------ | -------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **\$page** int     |          | page of results (default: `1`)                                                                                                                                               |
| **\$limit** array  |          | number of results per page (default: `20`)                                                                                                                                   |
| **\$params** array |          | additional [parameters](https://docs.snipcart.com/api-reference/customers) to pass with the REST request; page and limit _method_ parameters will be honored (default: `[]`) |

Returns an array of [Customer models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Customer.php) or `null`.

### getCustomer()

Get a specific customer by the related Snipcart ID.

| Argument                | Required | Description          |
| ----------------------- | -------- | -------------------- |
| **\$customerId** string | yes      | Snipcart customer ID |

Returns a [Customer model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Customer.php) or `null`.

### getCustomerOrders()

Get a customer's complete order history, sorted by descending date.

| Argument                | Required | Description          |
| ----------------------- | -------- | -------------------- |
| **\$customerId** string | yes      | Snipcart customer ID |

Returns an array of [Order models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php).

## [Data](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Data.php)

Returns store statistics.

### getOrderCount()

Get the number of orders by day between two dates.

| Argument                 | Required | Description                                                                 |
| ------------------------ | -------- | --------------------------------------------------------------------------- |
| **\$from** DateTime\|int | yes      | DateTime object or Unix timestamp that describes the beginning of the range |
| **\$to** DateTime\|int   | yes      | DateTime object or Unix timestamp that describes the end of the range       |

Returns an object decoded from the following JSON format:

```
{
  "labels": [
    "Number of orders"
  ],
  "data": [
    {
      "name": "2017-04-04",
      "value": 12
    },
    ...
  ],
  "to": 1493881200,
  "from": 1491289200,
  "currency": null
}
```

### getPerformance()

Get store performance stats between two dates.

| Argument                 | Required | Description                                                                 |
| ------------------------ | -------- | --------------------------------------------------------------------------- |
| **\$from** DateTime\|int | yes      | DateTime object or Unix timestamp that describes the beginning of the range |
| **\$to** DateTime\|int   | yes      | DateTime object or Unix timestamp that describes the end of the range       |

Returns an object decoded from the following JSON format:

```
{
  "ordersSales": 100.00,
  "ordersCount": 10,
  "averageCustomerValue": 10.000000,
  "taxesCollected": 10.00,
  "shippingCollected": 10.00,
  "customers": {
    "newCustomers": 10,
    "returningCustomers": 10
  },
  "averageOrdersValue": 0.000000000000000000000000000,
  "totalRecovered": 0.0
}
```

### getSales()

Get store saltes totals between two dates.

| Argument                 | Required | Description                                                                 |
| ------------------------ | -------- | --------------------------------------------------------------------------- |
| **\$from** DateTime\|int | yes      | DateTime object or Unix timestamp that describes the beginning of the range |
| **\$to** DateTime\|int   | yes      | DateTime object or Unix timestamp that describes the end of the range       |

Returns an object decoded from the following JSON format:

```
{
  "labels": [
    "Total sales"
  ],
  "data": [
    {
      "name": "2017-04-04",
      "value": 120.13
    },
    ...
  ],
  "to": 1493881200,
  "from": 1491289200,
  "currency": null
}
```

## [Discounts](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Discounts.php)

Lists, modifies, and creates store discounts.

### listDiscounts()

Returns an array of [Discount](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Discount.php) models.

### createDiscount()

Creates a new discount.

| Argument                                                                                                                        | Required | Description          |
| ------------------------------------------------------------------------------------------------------------------------------- | -------- | -------------------- |
| **\$discount** [Discount](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Discount.php) | yes      | discount to be saved |

Returns the Snipcart API's decoded response.

### getDiscount()

Gets an existing discount.

| Argument            | Required | Description                   |
| ------------------- | -------- | ----------------------------- |
| **\$discountToken** | yes      | unique ID of desired discount |

Returns a [Discount model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Discount.php) or `null`.

### deleteDiscountById()

Deletes a discount by the supplied ID.

| Argument            | Required | Description                   |
| ------------------- | -------- | ----------------------------- |
| **\$discountToken** | yes      | unique ID of desired discount |

Returns the Snipcart API's decoded response.

## [Fields](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Fields.php)

Gets and saves Product Details field data. (Used by included Field Type.)

### saveProductDetailsField()

### getProductDetailsField()

## [Orders](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Orders.php)

Largest service, interacts with Order data.

### getOrder()

### getOrders()

### getAllOrders()

### getOrderNotifications()

### getOrderRefunds()

### listOrders()

### updateElementsFromOrder()

### getOrderPackaging()

### sendOrderEmailNotification()

### refundOrder()

## [Products](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Products.php)

Handles local product data related to Snipcart Orders.

### reduceProductInventory()

## [Shipments](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Shipments.php)

Facilitates optional interaction with shipping providers.

### getShipStation()

### collectRatesForOrder()

### handleCompletedOrder()

### getQuoteLogForOrder()

## [Subscriptions](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Subscriptions.php)

Interacts with Snipcart subscriptions.

### listSubscriptions()

### getSubscription()

### cancel()

### pause()

### resume()
