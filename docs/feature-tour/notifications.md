# Notifications
The Snipcart plugin can be configured to send two types of email notifications out of the box: order confirmations for store admins, and order confirmations for customers.

Both are disabled by default, and each comes with a preconfigured Twig template that you can override with your own option from a control panel setting.

If you'd like to create your own templates, check out each of the included ones for reference:

- [Store Admin Notification](https://github.com/verbb/snipcart/blob/craft-4/src/templates/email/order.html)
- [Customer Notification](https://github.com/verbb/snipcart/blob/craft-4/src/templates/email/customer-order.html)

Each one will have two Twig variables available to it:

1. `order`: a complete Order model populated with order information. If a ShipStation order was created, it will be available in `providerOrders.shipStation`.
2. `settings`: the Snipcart plugin settings.

::: tip
The Snipcart plugin uses [`pelago/emogrifier`](https://packagist.org/packages/pelago/emogrifier) to inline HTML email styles and improve compatibility across email clients. No need to inline styles directly within your template!
:::
