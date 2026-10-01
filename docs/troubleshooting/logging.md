# Logging
The Snipcart plugin logs warnings and errors like Craft and other plugins. Its application logs include webhook metadata, processing results and errors without copying the complete request body into the log file.

Valid webhook requests are stored in the `snipcart_webhook_log` database table so you can inspect the complete payload when troubleshooting. Entries older than 30 days are deleted when Craft runs garbage collection by default. You can change the retention period with the `webhookLogRetentionDays` setting, or set it to `0` to retain entries indefinitely. Custom rate logging is optional, and its responses are stored in the `snipcart_shipping_quotes` table when enabled.

Shipping quote logs will also be used to verify a customer's shipping rate selection from the Snipcart checkout when a completed order is sent to ShipStation. It's unlikely that these would ever differ, but Snipcart and ShipStation don't share any identifier for the rate quote so its method name and price are verified to ensure accuracy.
