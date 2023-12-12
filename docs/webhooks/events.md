# Events
You're ready to latch onto Events if you've...

1. Configured Snipcart to post to your Craft webhook
2. Determined what you're listening for and what you need to do
3. Established your own plugin or module for project-specific business logic

Let's say, for example, that we'd like to offer custom shipping rates depending on the specifics of an order. We've [configured our webhook](docs:webhooks/setup) and a custom module.

## Customize Shipping Rates at Checkout
Snipcart sends a webhook request to see if we want to provide shipping rates. We'll listen for this and respond in the format Snipcart expects. The Event will come with the nearly-completed order and all its details, so all you need is to tell Craft that you'd like to respond to it and provide your own logic to send back rates.

Register an event listener for [`EVENT_BEFORE_RETURN_SHIPPING_RATES`](docs:developers/events#shipping-rate-response) in your module's `init()` method:

```php
public function init(): void
{
    parent::init();

    Event::on(Shipments::class, Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES, function(ShippingRateEvent $event) {
        $event->rates = $this->modifyShippingRates(
            $event->rates,
            $event->order,
            $event->package
        );
    });
}
```

This calls our own custom function called `modifyShippingRates()`, which looks at order details and gives back an array of rates. (This logic could all live inside of `function(WebhookEvent $event) {}`, but it'll be tidier to separate things a bit.)

That function, also in our base Module class—even though it could live anywhere—might look like this:

```php
private function modifyShippingRates($rates, $order, $package): array
{
    // don't allow any rates that include these strings
    $disallow = [
        'Flat Rate Envelope',
        'Regional Box A',
    ];

    $filteredRates = []; // we'll populate and return this

    foreach ($rates as $rate) {
        foreach ($disallow as $notAllowed) {
            // allow the rate if the disallowed string is *not* in the rate description
            if (stripos($rate->description, $notAllowed) === false) {
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
private function modifyShippingRates($rates, $order, $package): array
{
    $numberOfOrderItems = count($order->items);

    if ($order->items === 1) {
        foreach ($order->items as $item) {
            if ($item->name === 'Trombone')  {
                $rates[] = new ShippingRate([
                    'cost' => 12.99,
                    'description' => 'Special Trombone Shipping',
                ]);
            }
        }
    }

    return $rates;
}
```

The order details include a lot of information, so you could tailor rates based on the customer, the shipping destination, the order items, or a whole bunch of other things you'll see in the Snipcart Order model.

See the [Events reference](docs:developers/events) for a complete list of Events you can utilize.
