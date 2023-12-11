<?php
namespace verbb\snipcart\base;

use verbb\snipcart\Snipcart;
use verbb\snipcart\services\Api;
use verbb\snipcart\services\Carts;
use verbb\snipcart\services\Customers;
use verbb\snipcart\services\Data;
use verbb\snipcart\services\DigitalGoods;
use verbb\snipcart\services\Discounts;
use verbb\snipcart\services\Fields;
use verbb\snipcart\services\Notifications;
use verbb\snipcart\services\Orders;
use verbb\snipcart\services\Products;
use verbb\snipcart\services\Shipments;
use verbb\snipcart\services\Subscriptions;
use verbb\snipcart\services\Webhooks;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static Snipcart $plugin;


    // Public Methods
    // =========================================================================

    public static function log(string $message, array $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('snipcart', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'snipcart');
    }

    public static function error(string $message, array $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('snipcart', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'snipcart');
    }


    // Public Methods
    // =========================================================================

    public function getApi(): Api
    {
        return $this->get('api');
    }

    public function getCarts(): Carts
    {
        return $this->get('carts');
    }

    public function getCustomers(): Customers
    {
        return $this->get('customers');
    }

    public function getData(): Data
    {
        return $this->get('data');
    }

    public function getDigitalGoods(): DigitalGoods
    {
        return $this->get('digitalGoods');
    }

    public function getDiscounts(): Discounts
    {
        return $this->get('discounts');
    }

    public function getFields(): Fields
    {
        return $this->get('fields');
    }

    public function getOrders(): Orders
    {
        return $this->get('orders');
    }

    public function getNotifications(): Notifications
    {
        return $this->get('notifications');
    }

    public function getProducts(): Products
    {
        return $this->get('products');
    }

    public function getShipments(): Shipments
    {
        return $this->get('shipments');
    }

    public function getSubscriptions(): Subscriptions
    {
        return $this->get('subscriptions');
    }

    public function getWebhooks(): Webhooks
    {
        return $this->get('webhooks');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents(): void
    {
        $this->setComponents([
            'api' => Api::class,
            'carts' => Carts::class,
            'customers' => Customers::class,
            'data' => Data::class,
            'digitalGoods' => DigitalGoods::class,
            'discounts' => Discounts::class,
            'fields' => Fields::class,
            'orders' => Orders::class,
            'notifications' => Notifications::class,
            'products' => Products::class,
            'shipments' => Shipments::class,
            'subscriptions' => Subscriptions::class,
            'webhooks' => Webhooks::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _setLogging(): void
    {
        BaseHelper::setFileLogging('snipcart');
    }

}