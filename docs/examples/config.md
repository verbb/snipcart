---
meta:
    - name: description
      content: Snipcart plugin static config example.
---

# Static Config

This is a complete static config file, which you can edit as needed and place in `config/snipcart.php`.

```php
<?php

/**
 * An example static config for the Snipcart plugin.
 *
 * Copy this to config/snipcart.php (exactly that location+name) and edit as needed.
 */

return [
    // Snipcart API key
    'publicApiKey' => '',

    // Snipcart secret API key
    'secretApiKey' => '',
    
    // default currency
    'defaultCurrency' => 'USD',    

    // send order notifications to designated store admins
    'sendOrderNotificationEmail' => false,

    // optional email addresses for admins who want order notifications from Craft
    'notificationEmails' => [],

    // optional path to a custom template to be used for admin order notification emails
    'notificationEmailTemplate' => '',

    // `true` if you'd like Craft to send completed order notifications to customers
    'sendCustomerOrderNotificationEmail' => false,

    // optional custom template path for customer order notification emails
    'customerNotificationEmailTemplate' => '',

    // `true` if you'd like to decrement product quantities when orders are processed
    'reduceQuantitiesOnOrder' => false,

    // the name (not handle) of a custom field for Gift Notes, as sent to Snipcart
    'orderGiftNoteFieldName' => '',

    // the name (not handle) of a custom field for order comments, as sent to Snipcart
    'orderCommentsFieldName' => '',

    // cache Snipcart API responses
    'cacheResponses' => true,

    // Snipcart API response cache duration
    'cacheDurationLimit' => 300, // 5 minutes

    // whether or not to log responses to `shippingrates.fetch` webhook events for troubleshooting
    'logCustomRates' => true,

    // whether or not to log all valid incoming webhook posts from Snipcart
    'logWebhookRequests' => false,

    // settings for shipping providers (aka ShipStation)
    'providerSettings' => [
        'shipStation' => [
            'apiKey' => '',
            'apiSecret' => '',
            'defaultCarrierCode' => '', // can be empty
            'defaultPackageCode' => '', // can be empty
            'defaultCountry' => 'US', // must be set
            'defaultWarehouseId' => 0, // must be set
            'defaultOrderConfirmation' => 'delivery', // must be set
            'enableShippingRates' => false,
            'sendCompletedOrders' => false,
        ],
    ],

    // address to be used for rate quotes and orders with shipping providers (when enabled)
    'shipFromAddress' => [
        'name' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
        'phone' => '',
        'email' => '',
    ],
];
```
