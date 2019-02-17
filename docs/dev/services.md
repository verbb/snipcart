---
meta:
  - name: description
    content: Reference of Snipcart plugin's Craft Services.
---

# Services

The Snipcart plugin exposes just about everything it can do with Services. Each is accessible everywhere via `Snipcart::$plugin->handle`, where `handle` is the "camelCase" name of the class. If you wanted to interact with the Snipcart REST API, for example, you could use `$someData = Snipcart::$plugin->api->get('pretend-endpoint')` to grab data from a GET request.

## Api

Interacts directly with the Snipcart API via `get()` or `post()`. Just specify your desired endpoint and an optional array of parameters.

## Carts

Currently lists abandoned carts to be displayed in the control panel.

## Customers

Lists and searches customer data.

## Data

Returns store statistics.

## DigitalGoods

Doesn't do anything just yet. :/

## Discounts

Lists, modifies, and creates store discounts.

## Fields

Gets and saves Product Details field data. (Used by included Field Type.)

## Orders

Largest service, interacts with Order data.

## Products

Gets local Products information related to Snipcart Orders.

## Shipments

Facilitates optional interaction with shipping providers.

## Subscriptions

Lists subscriptions.

