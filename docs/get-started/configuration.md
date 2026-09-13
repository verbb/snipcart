# Configuration

You can customise Snipcart’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `snipcart.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will set the default currency to US dollars:

```php
<?php

return [
    'defaultCurrency' => 'usd',
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `publicApiKey`

**Type:** `string|null` · **Default:** `null`

Snipcart API key.
:::

- `secretApiKey - Snipcart secret API key.

::: reference
### `defaultCurrency`

**Type:** `string|null` · **Default:** `null`

The default currency.
:::


::: reference
### `sendOrderNotificationEmail`

**Type:** `bool` · **Default:** `false`

Whether to send order notifications to designated store admins.
:::


::: reference
### `notificationEmails`

**Type:** `array` · **Default:** `[]`

A collection of email addresses for admins who want order notifications.
:::


::: reference
### `notificationEmailTemplate`

**Type:** `string|null` · **Default:** `null`

Custom template to be used for admin order notification emails.
:::


::: reference
### `sendCustomerOrderNotificationEmail`

**Type:** `bool` · **Default:** `false`

Whether to send completed order notifications to customers.
:::


::: reference
### `customerNotificationEmailTemplate`

**Type:** `string|null` · **Default:** `null`

Custom template path for customer order notification emails.
:::


::: reference
### `reduceQuantitiesOnOrder`

**Type:** `bool` · **Default:** `false`

Whether to decrement product quantities when orders are processed.
:::


::: reference
### `orderGiftNoteFieldName`

**Type:** `string|null` · **Default:** `null`

The name (not handle) of a custom field for Gift Notes, as sent to Snipcart.
:::


::: reference
### `orderCommentsFieldName`

**Type:** `string|null` · **Default:** `null`

The name (not handle) of a custom field for order comments, as sent to Snipcart.
:::


::: reference
### `cacheResponses`

**Type:** `bool` · **Default:** `true`

Cache Snipcart API responses.
:::


::: reference
### `cacheDurationLimit`

**Type:** `int` · **Default:** `300`

Snipcart API response cache duration.
:::


::: reference
### `logCustomRates`

**Type:** `bool` · **Default:** `false`

Whether to log responses to `shippingrates.fetch` webhook events for troubleshooting.
:::


::: reference
### `logWebhookRequests`

**Type:** `bool` · **Default:** `false`

Whether to log all valid incoming webhook posts from Snipcart.
:::


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


::: reference
### `publicTestApiKey`

**Type:** `string|null` · **Default:** `null`

The public API key for the test account, selected when test mode is enabled.
:::

::: reference
### `secretApiKey`

**Type:** `string|null` · **Default:** `null`

The secret API key for live server-side requests. Keep it out of public templates.
:::

::: reference
### `secretTestApiKey`

**Type:** `string|null` · **Default:** `null`

The secret API key for test server-side requests.
:::

::: reference
### `enabledCurrencies`

**Type:** `array` · **Default:** `['usd']`

The currency codes enabled for the store. The default enables US dollars.
:::

::: reference
### `providers`

**Type:** `array` · **Default:** `[]`

Provider configuration for shipping integrations. Keep provider-specific options with the relevant shipping setup.
:::

::: reference
### `reFeedAttemptWindow`

**Type:** `int` · **Default:** `15`

How many minutes an order remains eligible for re-feeding when the verification command finds it missing from ShipStation.
:::

::: reference
### `testMode`

**Type:** `bool|null` · **Default:** `false`

Whether to select the test API keys instead of the live keys.
:::

::: reference
### `sendTestModeEmail`

**Type:** `bool|null` · **Default:** `false`

Whether to send notification emails while testing.
:::

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Snipcart.
