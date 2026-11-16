<?php

$vendor = getenv('VERBB_TEST_VENDOR') ?: dirname(__DIR__, 2) . '/vendor';
require $vendor . '/autoload.php';
require_once $vendor . '/yiisoft/yii2/Yii.php';
require_once $vendor . '/craftcms/cms/src/Craft.php';

// Load this checkout's source ahead of any installed copy of the plugin.
spl_autoload_register(static function(string $class): void {
    $prefix = 'verbb\\snipcart\\';

    if (str_starts_with($class, $prefix)) {
        $file = dirname(__DIR__, 2) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}, true, true);

use verbb\snipcart\Snipcart;
use verbb\snipcart\models\Settings;
use verbb\snipcart\models\snipcart\Order as SnipcartOrder;
use verbb\snipcart\models\snipcart\Package;
use verbb\snipcart\models\snipcart\ShippingRate;
use verbb\snipcart\providers\shipstation\ShipStation;

use craft\base\Plugin;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

$checks = 0;
$check = static function(bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

// PHP 8.0 needs private properties made accessible before reflection writes them.
$setProperty = static function(string $class, string $property, object $object, mixed $value): void {
    $reflection = new ReflectionProperty($class, $property);
    $reflection->setAccessible(true);
    $reflection->setValue($object, $value);
};

$plugin = (new ReflectionClass(Snipcart::class))->newInstanceWithoutConstructor();
$setProperty(Plugin::class, '_settings', $plugin, new Settings([
    'shipFromAddress' => ['postalCode' => '90210'],
    'providerSettings' => [
        'shipStation' => [
            'apiKey' => 'key',
            'apiSecret' => 'secret',
            'defaultCarrierCode' => 'stamps_com',
            'defaultPackageCode' => 'package',
            'defaultCountry' => 'US',
            'defaultWarehouseId' => 123456,
            'defaultOrderConfirmation' => 'delivery',
            'enableShippingRates' => true,
            'sendCompletedOrders' => true,
        ],
    ],
]));
Snipcart::$plugin = $plugin;

$shipStation = new ShipStation();
$settings = $shipStation->getSettings();
$check($settings->defaultCarrierCode === 'stamps_com', 'The carrier code must populate from provider settings.');
$check($settings->defaultPackageCode === 'package', 'The package code must populate from provider settings.');
$check($settings->enableShippingRates === true, 'Shipping rates must be enabled from provider settings.');
$check($settings->sendCompletedOrders === true, 'Sending completed orders must be enabled from provider settings.');

try {
    (new ShipStation())->getClient();
} catch (Error $error) {
    $check(!str_contains($error->getMessage(), 'before initialization'), 'Creating the client must not read an uninitialized property.');
}

$history = [];
$handler = HandlerStack::create(new MockHandler([
    new Response(200, [], json_encode([
        ['serviceName' => 'USPS Priority Mail', 'serviceCode' => 'usps_priority_mail', 'shipmentCost' => 9.5, 'otherCost' => 1],
        ['serviceName' => 'USPS Ground Advantage', 'serviceCode' => 'usps_ground_advantage', 'shipmentCost' => 5, 'otherCost' => 0, 'carrierNickname' => null],
    ])),
]));
$handler->push(GuzzleHttp\Middleware::history($history));
$setProperty(ShipStation::class, 'client', $shipStation, new Client(['handler' => $handler]));

// Snipcart's rate webhook supplies the flat shipping address fields.
$order = new SnipcartOrder([
    'invoiceNumber' => 'SNIP-1001',
    'totalWeight' => '500',
    'shippingAddressCity' => 'Austin',
    'shippingAddressProvince' => 'TX',
    'shippingAddressPostalCode' => '78701',
    'shippingAddressCountry' => 'US',
]);

$rates = $shipStation->getRatesForOrder($order, new Package(['length' => 10, 'width' => 8, 'height' => 4, 'weight' => 50]));
$request = json_decode((string)$history[0]['request']->getBody(), true);

$check($request['carrierCode'] === 'stamps_com' && $request['packageCode'] === 'package', 'Rate requests must send the configured carrier and package codes.');
$check($request['toPostalCode'] === '78701' && $request['toCountry'] === 'US', 'Rate requests must send the destination address.');
$check(count($rates) === 2 && $rates[0] instanceof ShippingRate, 'ShipStation rates must convert to Snipcart rates.');
$check($rates[0]->cost === 10.5 && $rates[0]->description === 'USPS Priority Mail' && $rates[0]->code === 'usps_priority_mail', 'Rate cost, name and code must carry across.');
$check($rates[1]->cost === 5.0, 'Rates with unknown response properties must still convert.');

echo "Passed $checks ShipStation rate regression checks on PHP " . PHP_VERSION . ".\n";
