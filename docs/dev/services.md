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
| **\$data** array      |          | array of parameters to be sent          |

Returns decoded response data.

### put()

Perform put request to the Snipcart API.

| Argument              | Required | Description                             |
| --------------------- | -------- | --------------------------------------- |
| **\$endpoint** string | yes      | Snipcart API endpoint to be queried     |
| **\$data** array      |          | array of parameters to be sent          |

Returns decoded response data.

### delete()

Perform delete request to the Snipcart API.

| Argument              | Required | Description                             |
| --------------------- | -------- | --------------------------------------- |
| **\$endpoint** string | yes      | Snipcart API endpoint to be queried     |
| **\$data** array      |          | array of parameters to be sent          |

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
| **\$cartId** string | yes      | unique ID of an abandoned cart |

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

| Argument                   | Required | Description                   |
| -------------------------- | -------- | ----------------------------- |
| **\$discountId** string    | yes      | unique ID of desired discount |

Returns a [Discount model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Discount.php) or `null`.

### deleteDiscountById()

Deletes a discount by the supplied ID.

| Argument            | Required | Description                   |
| ------------------- | -------- | ----------------------------- |
| **\$discountId**    | yes      | unique ID of desired discount |

Returns the Snipcart API's decoded response.

## [Fields](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Fields.php)

Gets and saves Product Details field data. (Used by included Field Type.)

### saveProductDetailsField()

Saves data (in a record) for a Product Details field.

| Argument                                                                                                                        | Required | Description       |
| ------------------------------------------------------------------------------------------------------------------------------- | -------- | ----------------- |
| **\$field** [ProductDetails](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/fields/ProductDetails.php) | yes      | field to be saved |
| **\$element** ElementInterface                                                                                                  | yes      | relevant Element  |

Returns `true` if successful or `null` if the field's data is empty.

### getProductDetailsField()

Fetches the data for a Product Details field.

| Argument                                                                                                                        | Required | Description                                                                                                                             |
| ------------------------------------------------------------------------------------------------------------------------------- | -------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| **\$field** [ProductDetails](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/fields/ProductDetails.php) | yes      | related Field                                                                                                                           |
| **\$element** ElementInterface                                                                                                  |          | relevant Element                                                                                                                        |
| **\$value**                                                                                                                     |          | optional value that should be used to populate the model, otherwise it will be populated with record data or field defaults accordingly |

Returns [ProductDetails model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/ProductDetails.php) or `null`.

## [Notifications](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Notifications.php)

Sends notifications as things happen. Currently just email.

::: warning
This service is likely to change as parts are abstracted to a future Notification model for validation and broader notification options (Slack, etc.).
:::

### setNotificationVars()

Sets template variables for the notification. Should be called before `sendEmail()`.

| Argument                 | Required | Description                                        |
| ------------------------ | -------- | -------------------------------------------------- |
| **\$data** array\|object | yes      | Twig variables meant for the notification template |

Doesn't return anything.

### getNotificationVars()

Gets template variables for the notification.

Returns an array, object, or `null`.

### setErrors()

Sets notification errors.

| Argument         | Required | Description                                                   |
| ---------------- | -------- | ------------------------------------------------------------- |
| **\$data** array | yes      | array of error strings compatible with parent's `setErrors()` |

Doesn't return anything.

### setEmailTemplate()

Sets notification email template.

| Argument                  | Required | Description                                                                                                                                   |
| ------------------------- | -------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| **\$htmlTemplate** string | yes      | Twig template path to be used for the HTML email notification                                                                                 |
| **\$textTemplate** string |          | Twig template path to be used for an alternate, plain text email notification                                                                 |
| **\$frontend** boolean    |          | whether the supplied path is on the front (site) end or the back (plugin) end, which matters for setting the template mode (default: `false`) |

Doesn't return anything.

### sendEmail()

Sends an email notification.

| Argument             | Required | Description                    |
| -------------------- | -------- | ------------------------------ |
| **\$to** array       | yes      | array of valid email addresses |
| **\$subject** string | yes      | message subject                |

Returns `true` if successful, otherwise `false`. If it didn't go well, use `getErrors()` to find out why.

## [Orders](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Orders.php)

Interacts with Orders.

### getOrder()

Gets a Snipcart order by ID.

| Argument                | Required | Description            |
| ----------------------- | -------- | ---------------------- |
| **\$orderToken** string | yes      | unique ID of the order |

Returns [Order model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) or `null`.

### getOrders()

Gets Snipcart orders, optionally by parameters supported by [Snipcart's REST API](https://docs.snipcart.com/api-reference/orders).

| Argument           | Required | Description                          |
| ------------------ | -------- | ------------------------------------ |
| **\$params** array |          | parameters used for fetching results |

Returns an array of [Order models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php).

### getAllOrders()

Gets Snipcart orders, optionally by parameters supported by [Snipcart's REST API](https://docs.snipcart.com/api-reference/orders), quietly aggreggating results across pagination from the REST API.

::: warning
Careful using this as long result sets could require a long wait!
:::

| Argument           | Required | Description                          |
| ------------------ | -------- | ------------------------------------ |
| **\$params** array |          | parameters used for fetching results |

Returns an array of [Order models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php).

### getOrderNotifications()

Gets the notifications Snipcart has sent regarding a specific order.

| Argument                | Required | Description            |
| ----------------------- | -------- | ---------------------- |
| **\$orderToken** string | yes      | unique ID of the order |

Returns an array of [Notification models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Notification.php).

### getOrderRefunds()

Gets a Snipcart order's refunds.

| Argument                | Required | Description            |
| ----------------------- | -------- | ---------------------- |
| **\$orderToken** string | yes      | unique ID of the order |

Returns an array of [Refund models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Refund.php).

### listOrders()

Gets Snipcart orders specifically including pagination info.

| Argument           | Required | Description                                |
| ------------------ | -------- | ------------------------------------------ |
| **\$page** int     |          | desired page of results (default: `1`)     |
| **\$limit** int    |          | number of results per page (default: `25`) |
| **\$params** array |          | parameters used for fetching results       |

Returns an object with the following keys:

-   **items**: array of [Order models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) up to the pagination limit
-   **totalItems**: int representing the result set's total number of orders
-   **offset**: int pagination offset
-   **limit**: pagination limit

### updateElementsFromOrder()

Get Craft Elements that relate to order items, updating quantities if that field setting is enabled and the item has a non-zero quantity value.

| Argument                                                                                                               | Required | Description                         |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | ----------------------------------- |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | order whose items should be checked |

Returns `true` if successful, or an array of errors if a resulting notification failed.

### getOrderPackaging()

Triggers [`EVENT_BEFORE_REQUEST_SHIPPING_RATES`](/dev/events.md#shipping-rate-request) to allow another plugin or module to provide packaging details for an order before shipping rates are requested.

| Argument                                                                                                               | Required | Description                                      |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | ------------------------------------------------ |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | order for which shipping rates will be requested |

Returns the [Package](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Package.php) model, populated by an event hook or instantiated but literally and symbolically empty.

### sendOrderEmailNotification()

Have Craft email an order notification.

| Argument                                                                                                               | Required | Description                                                |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | ---------------------------------------------------------- |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | the relevant Snipcart order                                |
| **\$extra** array                                                                                                      |          | extra variables meant for the Twig notification template   |
| **\$type** string                                                                                                      |          | `notifyAdmin` or `notifyCustomer` (default: `notifyAdmin`) |

Returns `true` if successful, otherwise an array of error strings.

### refundOrder()

Applies a refund to an order.

| Argument                                                                                                               | Required | Description                                                                |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | -------------------------------------------------------------------------- |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | the relevant Snipcart order                                                |
| **\$amount** float                                                                                                     | yes      | the amount to be refunded                                                  |
| **\$comment** string                                                                                                   |          | refund comment                                                             |
| **\$notifyCustomer** bool                                                                                              |          | whether to send a Snipcart notification to the customer (default: `false`) |

Returns the decoded response from the Snipcart REST API.

## [Products](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Products.php)

Handles local product data related to Snipcart Orders.

### reduceProductInventory()

Adjusts the supplied Entry's product inventory by the quantity value if...

-   it uses the Product Details field and
-   its inventory value exists and is greater than zero

[`EVENT_PRODUCT_INVENTORY_CHANGE`](/dev/events.md#product-inventory-change) will also be fired before the adjustment so an Event hook can modifies the `quantity` property prior to the adjustment.

| Argument           | Required | Description                                                        |
| ------------------ | -------- | ------------------------------------------------------------------ |
| **\$entry** Entry  | yes      | the Entry related to an order item by a Product Details field      |
| **\$quantity** int | yes      | a whole number, usually negative, representing the quantity change |

Doesn't return anything.

## [Shipments](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Shipments.php)

Facilitates optional interaction with ShipStation.

### getShipStation()

Returns an instance of the [ShipStation provider](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/providers/shipstation/ShipStation.php).

### collectRatesForOrder()

Collects shipping rate options for a Snipcart order. Triggers `EVENT_BEFORE_RETURN_SHIPPING_RATES` so that existing rate, order, or packaging details can be modified by an Event hook before querying ShipStation's REST API for rates.

| Argument                                                                                                               | Required | Description                                      |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | ------------------------------------------------ |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | order for which shipping rate options are needed |

Returns an array with two keys:

-   `rates`: array [ShippingRate[]](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/ShippingRate.php)
-   `package`: [Package](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Package.php)

### handleCompletedOrder()

Handles an order that's been completed, normally sent after receiving a webhook post from Snipcart.

| Argument                                                                                                               | Required | Description                      |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | -------------------------------- |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | order that's just been completed |

Returns an array with the following keys:

-   `orders`: array containing a [ShipStation Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/shipstation/Order.php) if one was created
-   `errors`: array of error strings encountered attempting to send the Snipcart order to ShipStation

### getQuoteLogForOrder()

Get the last shipping rate quote that was returned for the given order.

| Argument                                                                                                               | Required | Description                               |
| ---------------------------------------------------------------------------------------------------------------------- | -------- | ----------------------------------------- |
| **\$order** [Order](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Order.php) | yes      | order whose rate quotes we're looking for |

Returns most recent [ShippingQuoteLog]() that matches the Snipcart order ID, if found.

## [Subscriptions](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Subscriptions.php)

Interacts with Snipcart subscriptions.

### listSubscriptions()

Returns Snipcart subscriptions.

| Argument           | Required | Description                                |
| ------------------ | -------- | ------------------------------------------ |
| **\$page** int     |          | desired page of results (default: `1`)     |
| **\$limit** int    |          | number of results per page (default: `25`) |
| **\$params** array |          | parameters used for fetching results       |

Returns an object with the following keys:

-   **items**: array of [Subscription models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Subscription.php) up to the pagination limit
-   **totalItems**: int representing the result set's total number of subscriptions
-   **offset**: int pagination offset
-   **limit**: pagination limit

### getSubscription()

Gets a Snipcart subscription.

| Argument                    | Required | Description              |
| --------------------------- | -------- | ------------------------ |
| **\$subscriptionId** string | yes      | Snipcart subscription ID |

Returns a [Subscription model](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Subscription.php) or `null`.

### cancel()

Cancels a subscription.

| Argument                    | Required | Description              |
| --------------------------- | -------- | ------------------------ |
| **\$subscriptionId** string | yes      | Snipcart subscription ID |

Returns decoded response data from the Snipcart REST API.

### pause()

Pauses a subscription.

| Argument                    | Required | Description              |
| --------------------------- | -------- | ------------------------ |
| **\$subscriptionId** string | yes      | Snipcart subscription ID |

Returns decoded response data from the Snipcart REST API.

### resume()

Resumes a subscription.

| Argument                    | Required | Description              |
| --------------------------- | -------- | ------------------------ |
| **\$subscriptionId** string | yes      | Snipcart subscription ID |

Returns decoded response data from the Snipcart REST API.

## [Webhooks](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/services/Webhooks.php)

Handles posts sent to the [Webhooks controller](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/controllers/WebhooksController.php) by prepping models and dispatching [Events](/dev/events.md).

### setData()

Sets the payload data and derived mode to be utilized within the service and quietly logs the request before processing if logging is enabled.

| Argument            | Required | Description                                              |
| ------------------- | -------- | -------------------------------------------------------- |
| **\$payload** mixed | yes      | decoded request body that Snipcart posted to the webhook |

Doesn't return anything.

### getData()

Returns payload data in whatever format it was received. Or `null`.

### setMode()

Explicitly sets the webhook mode, which should be either `Live` or `Test`. Returns that mode.

### getMode()

Returns the current webhook mode, which should be either `Live` or `Test`.

### handleOrderCompleted()

Handles a completed order. Returns an array with the following keys:

-   `success`: boolean, `true` if everything went as expected with no errors
-   `errors`: an array of strings that represent errors encountered handling the order

### handleShippingRatesFetch()

Handles a shipping rate request by calling [`shipments->collectRatesForOrder()`](/dev/services.md#collectratesfororder), logging any custom rates, and returning that array of [ShippingRate models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/ShippingRate.php).

### handleOrderStatusChange()

Handles an order status change. Returns `[ 'success' => true ]`.

### handleOrderPaymentStatusChange()

Handles an order payment status change. Returns `[ 'success' => true ]`.

### handleOrderTrackingNumberChange()

Handles an order tracking number change. Returns `[ 'success' => true ]`.

### handleSubscriptionCreated()

Handles a created subscription. Returns `[ 'success' => true ]`.

### handleSubscriptionCancelled()

Handles a cancelled subscription. Returns `[ 'success' => true ]`.

### handleSubscriptionPaused()

Handles a paused subscription. Returns `[ 'success' => true ]`.

### handleSubscriptionResumed()

Handles a resumed subscription. Returns `[ 'success' => true ]`.

### handleSubscriptionInvoiceCreated()

Handles a new subscription invoice. Returns `[ 'success' => true ]`.

### handleTaxesCalculate()

Handles a tax calculation request. Returns array with `taxes` key (required by [Snipcart REST API](https://docs.snipcart.com/configuration/taxes)):

-   `taxes`: array of [Tax models](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/models/snipcart/Tax.php)

### handleCustomerUpdated()

Handles updated customer details. Returns `[ 'success' => true ]`.
