# Configuration
Create a `snipcart.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Snipcart, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'publicApiKey' => null,
        'publicTestApiKey' => null,
        'secretApiKey' => null,
        'secretTestApiKey' => null,
        'sendOrderNotificationEmail' => false,
        'notificationEmails' => [],
        'notificationEmailTemplate' => null,
        'sendCustomerOrderNotificationEmail' => false,
        'customerNotificationEmailTemplate' => null,
        'defaultCurrency' => null,
        'enabledCurrencies' => ['USD'],
        'orderGiftNoteFieldName' => null,
        'orderCommentsFieldName' => null,
        'reduceQuantitiesOnOrder' => false,
        'cacheResponses' => true,
        'cacheDurationLimit' => 300, // 5 minutes
        'logCustomRates' => false,
        'logWebhookRequests' => false,
        'shipFromAddress' => [],
        'providers' => [],
        'providerSettings' => [],
        'reFeedAttemptWindow' => 15,
        'testMode' => false,
        'sendTestModeEmail' => false,
    ],
];
```

## Configuration options
- `publicApiKey` - Snipcart API key.
- `secretApiKey - Snipcart secret API key.
- `defaultCurrency` - The default currency.
- `sendOrderNotificationEmail` - Whether to send order notifications to designated store admins.
- `notificationEmails` - A collection of email addresses for admins who want order notifications.
- `notificationEmailTemplate` - Custom template to be used for admin order notification emails.
- `sendCustomerOrderNotificationEmail` - Whether to send completed order notifications to customers.
- `customerNotificationEmailTemplate` - Custom template path for customer order notification emails.
- `reduceQuantitiesOnOrder` - Whether to decrement product quantities when orders are processed.
- `orderGiftNoteFieldName` - The name (not handle) of a custom field for Gift Notes, as sent to Snipcart.
- `orderCommentsFieldName` - The name (not handle) of a custom field for order comments, as sent to Snipcart.
- `cacheResponses` - Cache Snipcart API responses.
- `cacheDurationLimit` - Snipcart API response cache duration.
- `logCustomRates` - Whether to log responses to `shippingrates.fetch` webhook events for troubleshooting.
- `logWebhookRequests` - Whether to log all valid incoming webhook posts from Snipcart.

### Provider Settings
Return an array with the config settings for any shipping providers.

```php
<?php

return [
    '*' => [
        // ...
        'providerSettings' => [
            'shipStation' => [
                'apiKey' => '',
                'apiSecret' => '',
                'defaultCarrierCode' => '',
                'defaultPackageCode' => '',
                'defaultCountry' => 'US', // Required
                'defaultWarehouseId' => 0, // Required
                'defaultOrderConfirmation' => 'delivery', // Required
                'enableShippingRates' => false,
                'sendCompletedOrders' => false,
            ],
        ],
    ],
];
```

### Shipping Address
Return an array for the address to be used for rate quotes and orders with shipping providers.

```php
<?php

return [
    '*' => [
        // ...
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
    ],
];
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Snipcart.
