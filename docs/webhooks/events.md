---
meta:
  - name: description
    content: Webhook examples using the Snipcart Craft CMS plugin.
---

# Webhook Event Examples

You're ready to latch onto Events if you've...

1. configured Snipcart to post to your Craft webhook
2. determined what you're listening for and what you need to do
3. established your own plugin or module for project-specific business logic

Let's say, for example, that we'd like to offer custom shipping rates depending on the specifics of an order. We've [configured our webhook](/webhooks/setup.md) and a [custom module](/examples/module.md).

## Customize Shipping Rates at Checkout

Snipcart sends a webhook request to see if we want to provide shipping rates. We'll listen for this and respond in the format Snipcart expects. The Event will come with the nearly-completed order and all its details, so all you need is to tell Craft that you'd like to respond to it and provide your own logic to send back rates.

Register an event listener for [`EVENT_BEFORE_RETURN_SHIPPING_RATES`](/dev/events.md#shipping-rate-response) in your module's `init()` method:

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

## Add a Rate for Specific Orders

We could just as easily add our own rate. Here, we'll add a special shipping option if someone has ordered only a trombone.

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

See the [Events reference](/dev/events.md) for a complete list of Events you can utilize.