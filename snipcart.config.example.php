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

    // add the keys of *configured* options from the `providers` section
    'enabledProviders' => [],

    // optional email addresses for admins who want order notifications from Craft
    'notificationEmails' => [],

    // handle for a custom Craft field that's used as a Snipcart product ID
    'productIdentifier' => '',

    // handle of a custom Craft entry Integer field that defines product quantity
    'productInventoryField' => '',

    // `true` if you'd like to decrement product quantities when orders are processed
    'reduceQuantitiesOnOrder' => false,

    // the name (not handle) of a custom field for Gift Notes, as sent to Snipcart
    'orderGiftNoteFieldName' => '',

    // the name (not handle) of a custom field for order comments, as sent to Snipcart
    'orderCommentsFieldName' => '',

    // cache Snipcart API responses
    'cacheResponses' => true,

    // Snipcart API response cache duration
    'cacheDuration' => 300, // 5 minutes

    // whether or not to log responses to `shippingrates.fetch` webhook events for troubleshooting
    'logCustomRates' => true,

    // whether or not to log all valid incoming webhook posts from Snipcart
    'logWebhookRequests' => false,

    // define any package types you normally use, where weight is for a box/envelope and packing materials
    // only applies to custom rules when shipping providers are enabled
    'customPackaging' => [
        'name' => [
            'length' => 0,
            'width' => 0,
            'height' => 0,
            'weight' => 100, // grams
        ]
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
    // configure providers
    'providers' => [
        'shipStation' => [
            'apiKey' => '',
            'apiSecret' => '',
            'defaultCarrierCode' => '', // can be empty
            'defaultPackageCode' => '', // can be empty
            'defaultCountry' => 'US', // must be set
            'defaultWarehouseId' => 0, // must be set
            'defaultOrderConfirmation' => 'delivery', // must be set
        ],
        'shippo' => [
            'apiToken' => '',
        ],
    ]
];
