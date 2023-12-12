# Setup
This guide assumes you've established a Craft site and you're setting up a new store for the first time.

## Create a Snipcart Account
Start by [creating an account](https://app.snipcart.com/register) via Snipcart. Copy your API keys from **Account** → **API Keys** in Snipcart to **Settings** → **Snipcart** on your Craft site.

### Orders
Everything here is off by default, but you can configure a number of options that don't require any extra code unless you'd prefer your own markup, field types, and integrations.

Order Comments and gift notes are explained more in [their own section](docs:feature-tour/order-fields), and while email notifications are ready to go you can learn more about using your own templates for them in [Custom Email Notifications](docs:feature-tour/notifications).

### Webhooks
There's nothing to set here, but this part is important: use this URL to [link Snipcart and Craft with webhooks](docs:webhooks/setup).

### Logging
Caching is enabled and somewhat conservative to balance timeliness and speed, but here you can adjust that and optionally turn on webhook logging [that could help with troubleshooting](docs:troubleshooting/logging).

### Shipping
If you've set up a ShipStation account and want the Snipcart plugin to get live rates or forward orders, this is where you'll need to add your credentials along with a _Ship From_ address.

See the [Shipments page](docs:shipments/overview) for more about how ShipStation integration works.
