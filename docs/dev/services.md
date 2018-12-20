# Services

The Snipcart plugin exposes just about everything it can do with Services, just like Craft. Each one is accessible everywhere via `Snipcart::$plugin->handle`, where `handle` is the "camelCase" name of the class. If you wanted to interact with the Snipcart REST API, for example, you could use `$someData = Snipcart::$plugin->api->get('pretend-endpoint')` to grab data from a GET request.

## Api

## Carts

## Customers

## Discounts

## Orders

## Products

## Shipments

## Subscriptions