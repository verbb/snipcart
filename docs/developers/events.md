# Events

Snipcart provides events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to change Snipcart’s behaviour.

Most of these events are triggered when Snipcart posts a webhook to your site, so they only fire once you’ve [set up webhooks](docs:webhooks/setup). Register listeners from a module or plugin that’s loaded for webhook requests, and test them by completing a test order with webhooks configured.

## Shipping Rate Events

<span id="shipping-rate-request"></span>

### The `beforeRequestShippingRates` Event
The event that is triggered in response to a `shippingrates.fetch` webhook, before shipping rates are requested from a shipping provider.

The event receives the `order` and the `package` that will be used to request rates. You can change `$event->package`, such as its dimensions or weight, and Snipcart uses your package to request rates.

To reject the request, set `$event->isValid` to `false` and optionally set `$event->errors` to an array of `['key' => 'error key', 'message' => 'error message']` arrays. Snipcart stops before requesting or returning any rates and sends your errors back to Snipcart, which shows them at checkout. Without your own errors, a generic error is returned.

```php
use verbb\snipcart\events\ShippingRateEvent;
use verbb\snipcart\services\Orders;
use yii\base\Event;

Event::on(Orders::class, Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES, function(ShippingRateEvent $event) {
    $order = $event->order;
    $package = $event->package;

    if (!$order->shippingAddress) {
        $event->isValid = false;
        $event->errors = [
            ['key' => 'shipping-unavailable', 'message' => 'Shipping quotes are temporarily unavailable.'],
        ];
    }
});
```

<span id="shipping-rate-response"></span>

### The `beforeReturnShippingRates` Event
The event that is triggered before shipping rates are returned to Snipcart, in response to a `shippingrates.fetch` webhook.

The event receives the `order`, the `package` and the `rates` collected so far, which includes ShipStation rates when they’re enabled. Changes you make to `$event->rates` are returned to Snipcart, so you can add, filter or change rates. See [Customize Shipping Rates at Checkout](docs:webhooks/events#customize-shipping-rates-at-checkout) for a complete example.

To reject the request, set `$event->isValid` to `false` and optionally set `$event->errors`, in the same format as the `beforeRequestShippingRates` event. Snipcart returns your errors instead of any rates.

```php
use verbb\snipcart\events\ShippingRateEvent;
use verbb\snipcart\models\snipcart\ShippingRate;
use verbb\snipcart\services\Shipments;
use yii\base\Event;

Event::on(Shipments::class, Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES, function(ShippingRateEvent $event) {
    $event->rates[] = new ShippingRate([
        'cost' => 10,
        'description' => 'Local delivery',
        'code' => 'LOCAL',
    ]);
});
```

## Order Events

<span id="completed-order"></span>

### The `beforeProcessCompletedOrder` Event
The event that is triggered by the `order.completed` webhook, before any further action is taken, like sending the order to ShipStation, adjusting inventory or sending notification emails.

The event is for reacting to the order and can’t cancel the processing.

```php
use Craft;
use verbb\snipcart\events\OrderEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_BEFORE_PROCESS_COMPLETED_ORDER, function(OrderEvent $event) {
    Craft::info('Processing Snipcart order ' . $event->order->invoiceNumber, __METHOD__);
});
```

### The `onOrderStatusChanged` Event
The event that is triggered by the `order.status.changed` webhook, when an order’s status has changed.

The event receives the `order`, along with the `fromStatus` and `toStatus` values.

```php
use verbb\snipcart\events\OrderStatusEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_ORDER_STATUS_CHANGED, function(OrderStatusEvent $event) {
    $order = $event->order;
    $fromStatus = $event->fromStatus;
    $toStatus = $event->toStatus;
});
```

### The `onOrderPaymentStatusChanged` Event
The event that is triggered by the `order.paymentStatus.changed` webhook, when an order’s payment status has changed from the Snipcart dashboard or API.

The event receives the `order`, along with the `fromStatus` and `toStatus` values.

```php
use verbb\snipcart\events\OrderStatusEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED, function(OrderStatusEvent $event) {
    $order = $event->order;
    $fromStatus = $event->fromStatus;
    $toStatus = $event->toStatus;
});
```

### The `onOrderTrackingChanged` Event
The event that is triggered by the `order.trackingNumber.changed` webhook, when an order’s tracking number has changed.

```php
use verbb\snipcart\events\OrderTrackingEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_ORDER_TRACKING_CHANGED, function(OrderTrackingEvent $event) {
    $order = $event->order;
    $trackingNumber = $event->trackingNumber;
    $trackingUrl = $event->trackingUrl;
});
```

### The `onOrderRefundCreated` Event
The event that is triggered by the `order.refund.created` webhook, when a refund is created for an order.

```php
use verbb\snipcart\events\OrderRefundEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_ORDER_REFUND_CREATED, function(OrderRefundEvent $event) {
    $refund = $event->refund;
});
```

### The `onOrderNotificationCreated` Event
The event that is triggered by the `order.notification.created` webhook, when a notification is added to an order.

```php
use verbb\snipcart\events\OrderNotificationEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_ORDER_NOTIFICATION_CREATED, function(OrderNotificationEvent $event) {
    $notification = $event->notification;
});
```

## Product Events

### The `productInventoryChange` Event
The event that is triggered after an order is completed, before Snipcart adjusts the inventory of a product in the order. It’s triggered once for each order item when **Reduce Item Quantities** is enabled.

The event receives the `element` that holds the Product Details field and the `quantity` to adjust its inventory by, which is the negative of the quantity ordered. Changes you make to `$event->quantity` are used for the adjustment. Set it to `0` to leave the inventory unchanged.

```php
use verbb\snipcart\events\InventoryEvent;
use verbb\snipcart\services\Products;
use yii\base\Event;

Event::on(Products::class, Products::EVENT_PRODUCT_INVENTORY_CHANGE, function(InventoryEvent $event) {
    $element = $event->element;
    $quantity = $event->quantity;
});
```

## Subscription Events

### The `onSubscriptionCreated` Event
The event that is triggered by the `subscription.created` webhook, when a subscription has been created.

```php
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_SUBSCRIPTION_CREATED, function(SubscriptionEvent $event) {
    $subscription = $event->subscription;
});
```

### The `onSubscriptionCancelled` Event
The event that is triggered by the `subscription.cancelled` webhook, when a subscription has been cancelled.

```php
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_SUBSCRIPTION_CANCELLED, function(SubscriptionEvent $event) {
    $subscription = $event->subscription;
});
```

### The `onSubscriptionPaused` Event
The event that is triggered by the `subscription.paused` webhook, when a subscription has been paused.

```php
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_SUBSCRIPTION_PAUSED, function(SubscriptionEvent $event) {
    $subscription = $event->subscription;
});
```

### The `onSubscriptionResumed` Event
The event that is triggered by the `subscription.resumed` webhook, when a subscription has been resumed.

```php
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_SUBSCRIPTION_RESUMED, function(SubscriptionEvent $event) {
    $subscription = $event->subscription;
});
```

### The `onSubscriptionInvoiceCreated` Event
The event that is triggered by the `subscription.invoice.created` webhook, when a subscription invoice has been created.

```php
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED, function(SubscriptionEvent $event) {
    $subscription = $event->subscription;
});
```

## Tax Events

### The `onTaxesCalculate` Event
The event that is triggered by the `taxes.calculate` webhook, when an order is ready for tax calculation. Snipcart only sends this webhook if you’ve enabled the _Webhook Taxes Provider_ option in the Snipcart dashboard.

Add taxes to `$event->taxes` and Snipcart returns them for the order. Without a listener, no taxes are returned.

```php
use verbb\snipcart\events\TaxesEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_TAXES_CALCULATE, function(TaxesEvent $event) {
    $order = $event->order;

    $event->taxes[] = [
        'name' => 'GST',
        'amount' => round($order->subtotal * 0.1, 2),
        'numberForInvoice' => 'GST',
    ];
});
```

## Customer Events

### The `onCustomerUpdate` Event
The event that is triggered by the `customauth:customer_updated` webhook, when a customer’s details have changed.

```php
use verbb\snipcart\events\CustomerEvent;
use verbb\snipcart\services\Webhooks;
use yii\base\Event;

Event::on(Webhooks::class, Webhooks::EVENT_ON_CUSTOMER_UPDATE, function(CustomerEvent $event) {
    $customer = $event->customer;
});
```

## ShipStation Events

### The `beforeSendOrder` Event
The event that is triggered before a completed order is sent to ShipStation, once the order has been prepared and validated.

The event receives the ShipStation `order`. Changes you make to `$event->order` are sent to ShipStation. In dev mode or test mode, the order isn’t sent.

```php
use verbb\snipcart\providers\shipstation\events\OrderEvent;
use verbb\snipcart\providers\shipstation\ShipStation;
use yii\base\Event;

Event::on(ShipStation::class, ShipStation::EVENT_BEFORE_SEND_ORDER, function(OrderEvent $event) {
    $order = $event->order;
});
```
