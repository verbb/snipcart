![Snipcart](resources/hero.svg)

<h1 align="center">Snipcart Craft CMS 3 Plugin</h1>

<h4 align="center">Do more with <a href="https://snipcart.com/">Snipcart</a> directly from Craft CMS.</h4>

<p align="center"><a href="https://travis-ci.org/workingconcept/snipcart-craft-plugin"><img src="https://travis-ci.org/workingconcept/snipcart-craft-plugin.svg?branch=master" alt="CI status"></a> <a href="https://scrutinizer-ci.com/g/workingconcept/snipcart-craft-plugin/"><img src="https://scrutinizer-ci.com/g/workingconcept/snipcart-craft-plugin/badges/quality-score.png?b=master" alt="Scrutinizer status"></a></p>

---

<p align="center">🚧 <b>This plugin is under development and will require a paid license once it's ready for the plugin store.</b> 🚧</p>

---

## Features

The Snipcart plugin brings tight Snipcart integration to Craft CMS for easier setup and greater flexibility with minimal custom development.

- Fast store setup (even if you're new to Snipcart) with an optional Field Type and Twig tags.
- Powerful store customization with developer-friendly Events, Models, and [test project](https://github.com/workingconcept/snipcart-test) with examples and automated tests.
- Control panel section for sales stats and browsing orders, customers, subscriptions, and abandoned carts without leaving Craft CMS.
- Create discounts and issue refunds from the Craft CMS control panel.
- Live shipping rates and tax calculation at checkout with Snipcart's various shipping and tax providers.
- Included ShipStation integration for fetching shipping rates and forwarding completed Snipcart orders for processing.

If Commerce Pro is too complex for your project and Commerce Lite is bit too limiting, Snipcart will be a great fit.

|                           | Snipcart Plugin  | Commerce Lite | Commerce Pro |
| ------------------------- | ---------------- | ------------- | ------------ |
| Price                     | $179*            | $199          | $999         |
| Products                  | ✓                | ✓             | ✓            |
| Subscriptions             | ✓                | ✓             | ✓            |
| Custom Payment Gateways   | ✓**              | ✓             | ✓            |
| Custom Checkout Process   | ✓***             | ✓             | ✓            |
| Taxes & Shipping          | ✓                | ×             | ✓            |
| Shopping Cart             | ✓                | ×             | ✓            |
| Multi-Step Checkout Flow  | ✓                | ×             | ✓            |
| Sales & Discounts         | ✓                | ×             | ✓            |
| Digital Products          | ✓                | ×             | ✓            |
| Live Tax & Shipping Rates | ✓                | ×             | ✓            |
| [ShipStation](https://www.shipstation.com/) Integration   | ✓                | ×             | [+ plugin](https://plugins.craftcms.com/shipstationconnect)     |

*: Snipcart is a hosted service with [its own service fee](https://snipcart.com/pricing), and all three options require payment gateway fees.  
**: Commerce [has first-party support for seven payment gateways](https://docs.craftcms.com/commerce/v2/payment-gateways.html) and [Snipcart supports nine](https://snipcart.com/list-ecommerce-payment-gateways). **But** Commerce supports more gateways via plugins.  
***: Snipcart offers a highly-customizable cart template that you can customize however much you'd like.

## Installation

**From the command line:**

```shell
composer require workingconcept/craft-snipcart
./craft install snipcart
```

**From the plugin store:**

Find and install the [Snipcart plugin](https://plugins.craftcms.com/snipcart).

## Documentation

Setup guide, documentation and developer reference: https://snipcart.docs.workingconcept.com/

## Support

This plugin is actively supported and maintained. Please email [support@workingconcept.com](mailto:support@workingconcept.com), [file a GitHub issue](https://github.com/workingconcept/snipcart-craft-plugin/issues) or [submit a pull request](https://github.com/workingconcept/snipcart-craft-plugin/pulls) and you'll receive a response within 24 hours—probably sooner. 

---

This plugin is brought to you by [Working Concept](https://workingconcept.com), which has no affiliation with [Snipcart](https://snipcart.com/).
