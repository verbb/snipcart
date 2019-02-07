![Snipcart](resources/hero.svg)

<h1 align="center">Snipcart Craft CMS 3 Plugin</h1>

<h4 align="center">Do more with <a href="https://snipcart.com/">Snipcart</a> directly from Craft CMS.</h4>

<p align="center"><a href="https://travis-ci.org/workingconcept/snipcart-craft-plugin"><img src="https://travis-ci.org/workingconcept/snipcart-craft-plugin.svg?branch=master" alt="CI status"></a> <a href="https://scrutinizer-ci.com/g/workingconcept/snipcart-craft-plugin/"><img src="https://scrutinizer-ci.com/g/workingconcept/snipcart-craft-plugin/badges/quality-score.png?b=master" alt="Scrutinizer status"></a></p>

---

<p align="center">🚧 <b>This plugin is under development and will require a paid license once it's ready for the plugin store.</b> 🚧</p>

---

## Features

The Snipcart plugin brings tighter Snipcart integration to Craft CMS for easier setup and greater flexibility with minimal custom development.

- Use an optional Field Type and handy Twig tags to make store setup a breeze.
- Browse Snipcart orders, customers, and subscriptions from the Craft CMS control panel.
- Create discounts and issue refunds from the Craft CMS control panel.
- View sales statistics from the Craft CMS dashboard.
- Set up webhooks to have full control over order emails, shipping rates, inventory, and just about anything you can think up.
- Get live shipping rates at checkout with Snipcart's FedEx, USPS, UPS, Purolator, Canada Post and Australia Post integrations.
- Calculate exact taxes at checkout with Snipcart's TaxCloud and TaxJar integrations.

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
| ShipStation Integration   | ✓                | ×             | [+ plugin](https://plugins.craftcms.com/shipstationconnect)     |

*: Snipcart is a hosted service with [its own service fee](https://snipcart.com/pricing), and all three options require payment gateway fees.  
**: Commerce [has first-party support for seven payment gateways](https://docs.craftcms.com/commerce/v2/payment-gateways.html) and [Snipcart supports nine](https://snipcart.com/list-ecommerce-payment-gateways). An important difference is that Commerce can support more gateways and functionality via plugins.  
***: Snipcart offers a highly-customizable cart template that you can customize however much you'd like.

## Installation

**From the command line:**

```shell
composer require workingconcept/craft-snipcart
./craft install snipcart
```

**From the plugin store:**

Find and install the [Snipcart plugin](https://plugins.craftcms.com/snipcart) by Working Concept.

## Documentation

Yes! Full set of documentation and examples here: https://snipcart.docs.workingconcept.com/

## Support

Please submit [an issue](https://github.com/workingconcept/snipcart-craft-plugin/issues) or [a pull request](https://github.com/workingconcept/snipcart-craft-plugin/pulls) if anything needs attention. We'll do our best to respond promptly. 

You can also look for @matt in the Craft CMS Slack group and @mattrambles on Twitter.

---

This plugin is brought to you by [Working Concept](https://workingconcept.com), which has no affiliation with [Snipcart](https://snipcart.com/).
