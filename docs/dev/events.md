---
meta:
  - name: description
    content: Reference of Snipcart plugin's Craft Events.
---

# Events

Once you've configured Snipcart to post to your webhook URL, each of the following will be triggered by the relevant customer action.

## Shipping Rate Request

`Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/ShippingRateEvent.php) with `order` and `package` properties.

Triggered in response to a `shippingrates.fetch` webhook event, but _before_ shipping rates are requested from a shipping provider. This offers a chance to modify the order before shipping rates are requested.

## Shipping Rate Response

`Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/ShippingRateEvent.php) with `order`, `package` and `rates` properties.

Triggered before any custom shipping rates are returned to Snipcart so that they may be filtered or modified.

## Completed Order

`WebhooksController::EVENT_BEFORE_PROCESS_COMPLETED_ORDER`

Receives [OrderEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/OrderEvent.php) with an `order` property.

Triggered by the `order.completed` webhook immediately after an order is completed and dispatched before any further action is taken, like sending to ShipStation or making inventory adjustments.

## Product Inventory Change

`Products::EVENT_PRODUCT_INVENTORY_CHANGE`

Receives [InventoryEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/InventoryEvent.php) with `entry` and `quantity` properties.

Triggered after an order has been completed, and contains references to each relevant Entry and a numeric value that can be used to adjust its quantity.

## Order Status Change

`WebhooksController::EVENT_ON_ORDER_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

Triggered when an order status has changed.

## Order Payment Status Change

`WebhooksController::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

Triggered when an order's payment status has changed from the dashboard or API.

## Order Tracking Number Change

`WebhooksController::EVENT_ON_ORDER_TRACKING_CHANGED`

Receives [OrderTrackingEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/OrderTrackingEvent.php) with `order`, `trackingNumber`, and `trackingUrl` properties.

Triggered when an order's tracking number has changed.

## Subscription Created

`WebhooksController::EVENT_ON_SUBSCRIPTION_CREATED`

Receives [SubscriptionEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been created.

## Subscription Cancelled

`WebhooksController::EVENT_ON_SUBSCRIPTION_CANCELLED`

Receives [SubscriptionEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been cancelled.

## Subscription Paused

`WebhooksController::EVENT_ON_SUBSCRIPTION_PAUSED`

Receives [SubscriptionEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been paused.

## Subscription Resumed

`WebhooksController::EVENT_ON_SUBSCRIPTION_RESUMED`

Receives [SubscriptionEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription has been resumed.

## Subscription Invoice Created

`WebhooksController::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED`

Receives [SubscriptionEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/SubscriptionEvent.php) with a `subscription` property.

Triggered when a subscription invoice has been created.

## Tax Calculation

`WebhooksController::EVENT_ON_TAXES_CALCULATE`

Receives [TaxesEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/TaxesEvent.php) with `order` and `taxes` properties.

Triggered when an order is ready for tax calculation, and only called if you've explicitly enabled the _Webhook Taxes Provider_ option in the Snipcart control panel.

## Customer Updated

`WebhooksController::EVENT_ON_CUSTOMER_UPDATE`

Receives [CustomerEvent](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/events/CustomerEvent.php) with `customer` property.

