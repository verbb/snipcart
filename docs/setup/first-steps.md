---
meta:
    - name: description
      content: Getting started with Snipcart and Craft CMS.
---

# Install & Configure the Plugin

This guide assumes you've established a Craft site and you're setting up a new store for the first time.

## Create a Snipcart Account

[Create an account](https://app.snipcart.com/register):

![Sign up!](../../resources/sign-up.png)

Grab your API keys from _Account_ → _API Keys_ to the right of the admin interface:

![Public and Private API Keys](../../resources/api-keys.png)

## Install the Plugin

**From the plugin store,** find Snipcart and choose _Install_. Done.

**From your local project,** add with:

```
composer require workingconcept/craft-snipcart
```

You can then install from the Craft CMS control panel: _Settings_ → _Plugins_, choose the gear dropdown to the right of _Snipcart_, and select _Install_.

Or install from the command line:

```
./craft install/plugin snipcart
```

## Configure the Plugin

Visit the plugin's Settings page and provide at least your public+secret API keys. If you're just getting started, you'll probably want to configure your products, orders, and webhooks.

### Snipcart Account

At minimum, you'll need to add the _Snipcart Public API Key_ and _Snipcart Secret API Key_.

:::tip
These fields support environment variables so you can keep your secrets secret!
:::

### Orders

Everything in this section is quiet and off by default, but you can turn on any features you'd like to use.

### Webhooks

Nothing to set here, but you'll want to grab this URL to [link Snipcart and Craft with webhooks](/webhooks/setup.md)!

### Logging

Caching is enabled and somewhat conservative to balance timeliness and speed, but here you can adjust that and optionally turn on webhook logging that could help with troubleshooting.

### Shipping

If you've set up a ShipStation account and want the Snipcart plugin to get live rates or forward orders, this is where you'll need to add your credentials.

:::tip
The _API Key_ and _API Secret_ fields support environment variables as well.
:::
