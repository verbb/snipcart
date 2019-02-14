# Logging

The Snipcart plugin logs warnings and errors like Craft and other plugins. It also includes options for logging incoming webhook requests and outgoing custom rate responses for troubleshooting.

Once enabled, you'll find these logs in `snipcart_webhook_log` and `snipcart_shipping_quotes` database tables.

Shipping quote logs will also be used to verify a customer's shipping rate selection from the Snipcart checkout when a completed order is sent to ShipStation. It's unlikely that these would ever differ, but Snipcart and ShipStation don't share any identifier for the rate quote so its method name and price are verified to ensure accuracy.