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

Currently lists abandoned carts to be displayed in the control panel.

### listAbandonedCarts()

### getAbandonedCart()

## [Customers](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Customers.php)

Lists and searches customer data.

### listCustomers()

### getCustomer()

### getCustomerOrders()

## [Data](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Data.php)

Returns store statistics.

### getOrderCount()

### getPerformance()

### getSales()

## [Discounts](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Discounts.php)

Lists, modifies, and creates store discounts.

### listDiscounts()

### createDiscount()

### getDiscount()

### deleteDiscountById()

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
