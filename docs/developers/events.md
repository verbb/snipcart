# Events
Once you've configured Snipcart to post to your webhook URL, each of the following will be triggered by the relevant customer action.

## Shipping Rate Request
`Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/ShippingRateEvent.php) with `order` and `package` properties.

Triggered in response to a `shippingrates.fetch` webhook event, but _before_ shipping rates are requested from a shipping provider. This offers a chance to modify the order before shipping rates are requested, or do some validation and throw an error by setting the event's `isValid` property to `false` and optionally setting the `errors` property to an array of `['key' => 'error key', 'message' => 'error message']` associative arrays.

## Shipping Rate Response
`Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/ShippingRateEvent.php) with `order`, `package` and `rates` properties.

Triggered before any custom shipping rates are returned to Snipcart so that they may be filtered or modified. You can also do validation and throw an error (for instance if no `rates` were returned) by setting the event's `isValid` property to `false` and optionally setting the `errors` property to an array of `['key' => 'error key', 'message' => 'error message']` associative arrays.

## Completed Order
`Webhooks::EVENT_BEFORE_PROCESS_COMPLETED_ORDER`

Receives [OrderEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderEvent.php) with an `order` property.

Triggered by the `order.completed` webhook immediately after an order is completed and dispatched before any further action is taken, like sending to ShipStation or making inventory adjustments.

## Product Inventory Change
`Products::EVENT_PRODUCT_INVENTORY_CHANGE`

Receives [InventoryEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/InventoryEvent.php) with `element` and `quantity` properties.

Triggered after an order has been completed, and contains references to each relevant Element and a numeric value that can be used to adjust its quantity.

## Order Status Change
`Webhooks::EVENT_ON_ORDER_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

Triggered when an order status has changed.

## Order Payment Status Change
`Webhooks::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

Triggered when an order's payment status has changed from the dashboard or API.

## Order Tracking Number Change
`Webhooks::EVENT_ON_ORDER_TRACKING_CHANGED`

Receives [OrderTrackingEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderTrackingEvent.php) with `order`, `trackingNumber`, and `trackingUrl` properties.

Triggered when an order's tracking number has changed.

## Order Refund Created
`Webhooks::EVENT_ON_ORDER_REFUND_CREATED`

Receives [OrderRefundEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderRefundEvent.php) with a `refund` property.

## Order Notification Created
`Webhooks::EVENT_ON_ORDER_NOTIFICATION_CREATED`

Receives [OrderNotificationEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/OrderNotificationEvent.php) with a `notification` property.

## Subscription Created
`Webhooks::EVENT_ON_SUBSCRIPTION_CREATED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been created.

## Subscription Cancelled
`Webhooks::EVENT_ON_SUBSCRIPTION_CANCELLED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been cancelled.

## Subscription Paused
`Webhooks::EVENT_ON_SUBSCRIPTION_PAUSED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been paused.

## Subscription Resumed
`Webhooks::EVENT_ON_SUBSCRIPTION_RESUMED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been resumed.

## Subscription Invoice Created
`Webhooks::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription invoice has been created.

## Tax Calculation
`Webhooks::EVENT_ON_TAXES_CALCULATE`

Receives [TaxesEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/TaxesEvent.php) with `order` and `taxes` properties.

Triggered when an order is ready for tax calculation, and only called if you've explicitly enabled the _Webhook Taxes Provider_ option in the Snipcart control panel.

## Customer Updated
`Webhooks::EVENT_ON_CUSTOMER_UPDATE`

Receives [CustomerEvent](https://github.com/verbb/snipcart/blob/craft-4/src/events/CustomerEvent.php) with `customer` property.

