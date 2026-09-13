# Events
Once you've configured Snipcart to post to your webhook URL, each of the following will be triggered by the relevant customer action.


## Register a Listener

Register listeners from a custom module or plugin that is bootstrapped for the requests where the event occurs. Put the `use` imports at the top of its PHP file and the `Event::on(...)` call inside its `init()` method, after `parent::init()`. Do not place the listener in a Twig template or modify this plugin's source to register it.

Choose a hook whose timing matches your task. Cancellation depends on the particular event and emitter, as described for each hook below. Test a listener on the operation it affects, including any relevant queue or console path.

For example, this listener logs when the plugin begins processing a completed order. Place it in your own bootstrapped module, keeping your existing namespace and bootstrap configuration:

```php
<?php

namespace modules;

use Craft;
use verbb\snipcart\events\OrderEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;
use yii\base\Module;

class SnipcartModule extends Module
{
    public function init(): void
    {
        parent::init();

        Event::on(Webhooks::class, Webhooks::EVENT_BEFORE_PROCESS_COMPLETED_ORDER, function(OrderEvent $event) {
            Craft::info('Processing Snipcart order ' . $event->order->invoiceNumber, __METHOD__);
        });
    }
}
```

Complete a test order with webhooks configured, then check the application log for its invoice number. This order hook is observational: the emitter does not provide an `isValid` cancellation check. The two shipping-rate hooks below do support rejecting the rate response.

<span id="shipping-rate-request"></span>

## The `beforeRequestShippingRates` Event
`Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/ShippingRateEvent.php) with `order` and `package` properties.

The event that is triggered in response to a `shippingrates.fetch` webhook event, but _before_ shipping rates are requested from a shipping provider. This offers a chance to modify the order before shipping rates are requested, or do some validation and throw an error by setting the event's `isValid` property to `false` and optionally setting the `errors` property to an array of `['key' => 'error key', 'message' => 'error message']` associative arrays.

For a rate validation failure, set both the validity flag and an actionable error. This listener belongs inside the module's `init()` method; add its imports at the top of the file:

```php
use verbb\snipcart\events\ShippingRateEvent;
use verbb\snipcart\services\Orders;
use yii\base\Event;

Event::on(Orders::class, Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES, function(ShippingRateEvent $event) {
    $event->isValid = false;
    $event->errors = [
        ['key' => 'shipping-unavailable', 'message' => 'Shipping quotes are temporarily unavailable.'],
    ];
});
```

This example deliberately rejects every quote while installed. Use it on a test installation to inspect the checkout error, then replace the unconditional rejection with your actual validation condition.

<span id="shipping-rate-response"></span>

## The `beforeReturnShippingRates` Event
`Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES`

Receives [ShippingRateEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/ShippingRateEvent.php) with `order`, `package` and `rates` properties.

The event that is triggered before any custom shipping rates are returned to Snipcart so that they may be filtered or modified. You can also do validation and throw an error (for instance if no `rates` were returned) by setting the event's `isValid` property to `false` and optionally setting the `errors` property to an array of `['key' => 'error key', 'message' => 'error message']` associative arrays.

<span id="completed-order"></span>

## The `beforeProcessCompletedOrder` Event
`Webhooks::EVENT_BEFORE_PROCESS_COMPLETED_ORDER`

Receives [OrderEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderEvent.php) with an `order` property.

The event that is triggered by the `order.completed` webhook immediately after an order is completed and dispatched before any further action is taken, like sending to ShipStation or making inventory adjustments.

## Product Inventory Change
`Products::EVENT_PRODUCT_INVENTORY_CHANGE`

Receives [InventoryEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/InventoryEvent.php) with `element` and `quantity` properties.

The event that is triggered after an order has been completed, and contains references to each relevant Element and a numeric value that can be used to adjust its quantity.

## Order Status Change
`Webhooks::EVENT_ON_ORDER_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

The event that is triggered when an order status has changed.

## Order Payment Status Change
`Webhooks::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED`

Receives [OrderStatusEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderStatusEvent.php) with `order`, `fromStatus`, and `toStatus` properties.

The event that is triggered when an order's payment status has changed from the dashboard or API.

## Order Tracking Number Change
`Webhooks::EVENT_ON_ORDER_TRACKING_CHANGED`

Receives [OrderTrackingEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderTrackingEvent.php) with `order`, `trackingNumber`, and `trackingUrl` properties.

The event that is triggered when an order's tracking number has changed.

## Order Refund Created
`Webhooks::EVENT_ON_ORDER_REFUND_CREATED`

Receives [OrderRefundEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderRefundEvent.php) with a `refund` property.

## Order Notification Created
`Webhooks::EVENT_ON_ORDER_NOTIFICATION_CREATED`

Receives [OrderNotificationEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/OrderNotificationEvent.php) with a `notification` property.

## Subscription Created
`Webhooks::EVENT_ON_SUBSCRIPTION_CREATED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/SubscriptionEvent.php) with a `subscription` property.

The event that is triggered when a subscription has been created.

## Subscription Cancelled
`Webhooks::EVENT_ON_SUBSCRIPTION_CANCELLED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/SubscriptionEvent.php) with a `subscription` property.

The event that is triggered when a subscription has been cancelled.

## Subscription Paused
`Webhooks::EVENT_ON_SUBSCRIPTION_PAUSED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/SubscriptionEvent.php) with a `subscription` property.

The event that is triggered when a subscription has been paused.

## Subscription Resumed
`Webhooks::EVENT_ON_SUBSCRIPTION_RESUMED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/SubscriptionEvent.php) with a `subscription` property.

The event that is triggered when a subscription has been resumed.

## Subscription Invoice Created
`Webhooks::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED`

Receives [SubscriptionEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/SubscriptionEvent.php) with a `subscription` property.

The event that is triggered when a subscription invoice has been created.

## Tax Calculation
`Webhooks::EVENT_ON_TAXES_CALCULATE`

Receives [TaxesEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/TaxesEvent.php) with `order` and `taxes` properties.

The event that is triggered when an order is ready for tax calculation, and only called if you've explicitly enabled the _Webhook Taxes Provider_ option in the Snipcart control panel.

## Customer Updated
`Webhooks::EVENT_ON_CUSTOMER_UPDATE`

Receives [CustomerEvent](https://github.com/verbb/snipcart/blob/craft-5/src/events/CustomerEvent.php) with `customer` property.

