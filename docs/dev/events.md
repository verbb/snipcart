# Events

Once you've configured Snipcart to post to your webhook URL, each of the following will be triggered in conjunction with the relevant customer action.

## Available Events

### `Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES`

Triggered in response to a `shippingrates.fetch` webhook event, but before shipping rates are requested from a shipping provider. This is a good time to modify the order before a shipping rate request if necessary.

### `Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES`

Triggered before any custom shipping rates are returned to Snipcart so that they may be filtered or modified.

### `WebhooksController::EVENT_BEFORE_PROCESS_COMPLETED_ORDER`

Triggered immediately after an order is completed and sent via the `order.completed` webhook event. This is before the order is sent to any providers or inventory adjustments are attempted.

### `Products::EVENT_PRODUCT_INVENTORY_CHANGE`

Triggered after an order has been completed, and contains references to each relevant Entry and a numeric value that can be used to adjust its quantity.

### `WebhooksController::EVENT_ON_ORDER_STATUS_CHANGED`

### `WebhooksController::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED`

### `WebhooksController::EVENT_ON_ORDER_TRACKING_CHANGED`

### `WebhooksController::EVENT_ON_SUBSCRIPTION_CREATED`

### `WebhooksController::EVENT_ON_SUBSCRIPTION_CANCELLED`

### `WebhooksController::EVENT_ON_SUBSCRIPTION_PAUSED`

### `WebhooksController::EVENT_ON_SUBSCRIPTION_RESUMED`

### `WebhooksController::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED`

### `WebhooksController::EVENT_ON_TAXES_CALCULATE`

### `WebhooksController::EVENT_ON_CUSTOMER_UPDATE`

## Example Usage

Hooking into events is pretty easy if you've...

1. configured Snipcart to post to your Craft webhook
2. determined what you're listening for and what you need to do
3. established your own plugin or module for project-specific business logic

Let's say, for example, that we'd like to offer custom shipping rates depending on the specifics of an order. We've set up our web hook and a [custom module](/examples/module.md).

### Listen for Shipping Rate Request

Snipcart will send an event to the webhook to fetch shipping rates. We'll listen for this and respond with whatever rates we want in the format Snipcart expects. The event will come with the nearly-completed order and all its details, so all you need is to tell Craft that you'd like to respond to it and provide your own logic to send back rates.

Register an event listener for `EVENT_BEFORE_RETURN_SHIPPING_RATES` in your module's `init()` method:

```php
public function init()
{
    parent::init();

    Event::on(
        Shipments::class,
        Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES,
        function(WebhookEvent $event) {
            $event->rates = $this->modifyShippingRates(
                $event->rates,
                $event->order,
                $event->packaging
            );
        }
    );
}
```

### Filter Out Rates

This calls our own custom function called `modifyShippingRates()`, which looks at order details and gives back an array of rates. (This logic could all live inside of `function(WebhookEvent $event) {}`, but it'll be tidier to separate things a bit.)

That function, also in our base Module class—even though it could live anywhere—might look like this:

```php
/**
 * Remove specific items from available shipping rate options.
 *
 * @param ShippingRate[] $rates
 * @param Order          $order
 * @param Package        $package
 *
 * @return array
 */
private function modifyShippingRates($rates, $order, $package): array
{
    // don't allow any rates that include these strings
    $disallow = [
        'Flat Rate Envelope',
        'Regional Box A',
    ];

    $filteredRates = []; // we'll populate and return this

    foreach ($rates as $rate)
    {
        foreach ($disallow as $notAllowed)
        {
            /**
             * allow the rate if the disallowed string is *not* 
             * in the rate description
             */
            if (stripos($rate->description, $notAllowed) === false)
            {
                $filteredRates[] = $rate;
            }
        }
    }

    return $filteredRates;
}
```

The function above will filter out any existing rates whose description include `Flat Rate Envelope` or `Regional Box A`.

### Add a Rate for Specific Orders

We could just as well *add* our own rate. Here, we'll add a special shipping option if someone has ordered _only_ a trombone.

```php
/**
 * Add special option for trombone orders.
 *
 * @param ShippingRate[] $rates
 * @param Order          $order
 * @param Package        $package
 *
 * @return array
 */
private function modifyShippingRates($rates, $order, $package): array
{
    $numberOfOrderItems = count($order->items);

    if ($order->items === 1)
    {
        foreach ($order->items as $item)
        {
            if ($item->name === 'Trombone') 
            {
                $rates[] = new ShippingRate([
                    'cost' => 12.99,
                    'description' => 'Special Trombone Shipping',
                ])
            }
        }
    }

    return $rates;
}
```

The order details include a lot of information, so you could tailor rates based on the customer, the shipping destination, the order items, or a whole bunch of other things you'll see in the [Snipcart Order model](/dev/models.md#order).