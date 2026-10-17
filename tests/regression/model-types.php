<?php

$vendor = getenv('VERBB_TEST_VENDOR') ?: dirname(__DIR__, 2) . '/vendor';
require $vendor . '/autoload.php';
require_once $vendor . '/yiisoft/yii2/Yii.php';
require_once $vendor . '/craftcms/cms/src/Craft.php';

use verbb\snipcart\models\ShippingQuoteLog;
use verbb\snipcart\models\shipstation\OrderItem;
use verbb\snipcart\models\snipcart\CustomerStatistics;
use verbb\snipcart\models\snipcart\Product;
use verbb\snipcart\models\snipcart\Subscription;

$checks = 0;
$check = static function(bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$created = new DateTime('2026-01-01T10:00:00+00:00');
$updated = new DateTime('2026-01-02T10:00:00+00:00');
$log = new ShippingQuoteLog(['dateCreated' => $created, 'dateUpdated' => $updated]);
$check($log->dateCreated === $created && $log->dateUpdated === $updated, 'Shipping logs must accept native dates.');
$log->dateUpdated = null;
$check($log->dateUpdated === null, 'An unsaved shipping log may have no update date.');

foreach ([Product::class, Subscription::class] as $class) {
    $metadata = (object)['campaign' => 'summer', 'active' => false];
    $model = new $class(['metadata' => $metadata]);
    $check($model->metadata === $metadata, 'Decoded API metadata objects must be accepted.');
    $model->metadata = ['campaign' => 'summer'];
    $check($model->metadata['campaign'] === 'summer', 'Array metadata must remain supported.');
    $model->metadata = null;
    $check($model->metadata === null, 'Absent metadata must remain supported.');
}

$item = new OrderItem();
$check($item->name === null, 'An unpopulated ShipStation item must be constructible.');
$item->name = 'Example item';
$check($item->name === 'Example item', 'ShipStation item names must accept strings.');
$statistics = new CustomerStatistics(['ordersCount' => 2, 'ordersAmount' => 19.5]);
$check($statistics->ordersCount === 2 && $statistics->ordersAmount === 19.5, 'Customer totals must hydrate from API data.');
echo "Passed $checks model regression checks on PHP " . PHP_VERSION . ".\n";
