![Snipcart](resources/hero.svg)

<h1 align="center">Snipcart Craft CMS 3 Plugin</h1>

<h4 align="center">Do more with <a href="https://snipcart.com/">Snipcart</a> directly from Craft CMS.</h4>

<p align="center"><img src="https://travis-ci.org/workingconcept/snipcart-craft-plugin.svg?branch=master" alt="CI Status"></p>

---

<p align="center">🚧 <b>This plugin is under development and will require a paid license once it's ready for the plugin store.</b> 🚧</p>

---

## Features

Snipcart makes it easy to add a full-featured cart system to any site in very little time, and this plugin makes it both easier to get started with Snipcart and lets Craft CMS take more advantage of its features right out of the box. If Craft Commerce is too complex for your project, odds are Snipcart will be a great fit.

- Browse Snipcart orders, customers, and subscriptions from the Craft CMS control panel.
- View up-to-date sales statistics from the Craft CMS dashboard.
- Issue refunds and set up discounts from the Craft CMS control panel.
- Use an optional Field Type to store product SKU, dimensions, and weight.
- Extend easily to manage your own shipping rates, inventory, and email notifications.

## Installation & Setup

You can get started with Snipcart very easily by including jQuery and their JS snippet and setting up _Buy_ and _Add to Cart_ buttons. This plugin has a Field Type and some template variables that can make that even faster, but you don't have to use them at all.

- Add Snipcart to your site.
- Install this plugin with `composer require workingconcept/craft-snipcart` and `./craft install/plugin snipcart`.
- You don't *have* to use Entries to define products, but the plugin assumes you'll want to and it'll need to know what field you've used to define a product SKU at minimum. Use the included field type or a single-line Plain Text field to designate a SKU for one or more entry types.
- On the plugin's settings page, add your Snipcart public and private API keys along with fields you use to define products.
- View your orders from the control panel, add sales stats to your dashboard, and easily hook in to develop your own custom shipping rates or webhook actions.
- (eventually) Configure a provider to automatically generate shipment labels or send the order for fulfillment using another third-party service.

## Support

Please submit [an issue](https://github.com/workingconcept/snipcart-craft-plugin/issues) or [a pull request](https://github.com/workingconcept/snipcart-craft-plugin/pulls) if anything needs attention. We'll do our best to respond promptly.

---

## Development

### Events

You can use your own plugin or module to listen for specific Snipcart Events and modify data.

#### Example Usage

If Snipcart sends a `shippingrates.fetch` webhook to request custom shipping rates to be shown to the customer at checkout, you can add, remove, or modify rates programmatically by listening for the Snipcart plugin's `EVENT_BEFORE_RETURN_SHIPPING_RATES` and calling your own function.

The following should appear in a plugin or module's `init()` method. When the Event is triggered in the example below, the rates ($event->rates) will be modified by a custom function called `modifyShippingRates()`:

```
Event::on(
    SnipcartService::class,
    SnipcartService::EVENT_BEFORE_RETURN_SHIPPING_RATES,
    function(WebhookEvent $event) {
        $event->rates = $this->modifyShippingRates(
            $event->rates,
            $event->order,
            $event->packaging
        );
    }
);
```

The event passes `rates`, `order`, and `packaging` details. You can modify any of them, though in this case only the rates will be sent to Snipcart.

#### Available Events

##### `EVENT_BEFORE_REQUEST_SHIPPING_RATES`

Triggered in response to a `shippingrates.fetch` webhook event, but before shipping rates are requested from a shipping provider. This is a good time to modify the order before a shipping rate request if necessary.

##### `EVENT_BEFORE_RETURN_SHIPPING_RATES`

Triggered before any custom shipping rates are returned to Snipcart so that they may be filtered or modified.

##### `EVENT_BEFORE_PROCESS_COMPLETED_ORDER`

Triggered immediately after an order is completed and sent via the `order.completed` webhook event. This is before the order is sent to any providers or inventory adjustments are attempted.

##### `EVENT_PRODUCT_INVENTORY_CHANGE`

Triggered after an order has been completed, and contains references to each relevant Entry and a numeric value that can be used to adjust its quantity.


---

## TODO

- [ ] publish setup guide
- [ ] publish feature overview
- [ ] provide event hooks for all Snipcart webhook events
    - [x] `order.completed`
    - [x] `shippingrates.fetch`
    - [ ] `order.status.changed`
    - [ ] `order.trackingNumber.changed`
    - [ ] `subscription.created`
    - [ ] `subscription.cancelled`
    - [ ] `subscription.paused`
    - [ ] `subscription.resumed`
    - [ ] `subscription.invoice.created`
    - [ ] `taxes.calculate`
    - [ ] `customauth:customer_updated`
- [ ] add translations
- [ ] support multiple sites
- [ ] add currency configuration
- [ ] browse subscriptions from the control panel
- [ ] issue refunds from the control panel
- [ ] manage discounts from the control panel
- [ ] sales statistics dashboard widget
- [ ] Snipcart Product fieldtype (dimensions, weight, and SKU)
- [ ] support inventory management
- [ ] template variables for quick setup
    - [ ] Add to Cart button
    - [ ] Buy Now button
    - [ ] Snipcart snippet tag
    - [ ] View Cart button
- [ ] ShipStation integration
- [ ] Shippo integration


---

This plugin is brought to you by [Working Concept](https://workingconcept.com), which has no affiliation with [Snipcart](https://snipcart.com/).