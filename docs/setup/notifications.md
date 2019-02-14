---
meta:
  - name: description
    content: How to customize and send Snipcart order email notifications from Craft CMS.
---

# Custom Email Notifications

The Snipcart plugin can be configured to send two types of email notifications out of the box: order confirmations for store admins, and order confirmations for customers.

Both are disabled by default, and each comes with a preconfigured Twig template that you can override with your own option from a control panel setting.

If you'd like to create your own templates, check out each of the included ones for reference:

- [Store Admin Notification](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/templates/email/order.twig)
- [Customer Notification](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/templates/email/customer-order.twig)
