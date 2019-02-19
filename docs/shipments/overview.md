---
meta:
    - name: description
      content: Using ShipStation with Snipcart and Craft CMS.
---

# ShipStation

The Snipcart plugin offers basic integration with [ShipStation](https://www.shipstation.com/), a third-party service that can be used to get rates, print postage, manage inventory, or even manage multiple warehouses and a whole fulfillment process. This integration can be used to get live shipping rates directly from ShipStation, and send Snipcart orders to ShipStation once they're completed.

::: warning
Nothing will happen with ShipStation until you've [configured the Snipcart webhook](/webhooks/setup.md) and switched on _Enable Shipping Rates?_ and/or _Send Completed Orders?_.
:::

## Fetching Rates

If you switch on _Enable Shipping Rates?_, the Snipcart plugin will automatically send order information to ShipStation to fetch live shipping rates. This happens at checkout, when Snipcart sends the `shippingrates.fetch` webhook event to ask for rate options. Options in the response will be added to any rates Snipcart is already providing (if any).

If for some reason no rates are available, the cart will display a generic message (not an error) to the user. This can happen for various reasons, like shipping to an unsupported country or requesting rates for a shipment whose dimensions or weight aren't serviceable by the configured shippers.

## Sending Completed Orders

If you've configured the Snipcart plugin to send completed orders to ShipStation, webhook responses and admin confirmation emails will both include the resulting ShipStation order ID that was created.

### Development Mode

Orders won't be sent to ShipStation if Craft is running with `devMode` set to `true`. Everything will work the same, but a "successful" result with have a ShipStation order ID of `99999999`.
