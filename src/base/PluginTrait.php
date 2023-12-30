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

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static Snipcart $plugin;


    // Traits
    // =========================================================================

    use LogTrait;


    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('snipcart');

        return [
            'components' => [
                'api' => Api::class,
                'carts' => Carts::class,
                'customers' => Customers::class,
                'data' => Data::class,
                'digitalGoods' => DigitalGoods::class,
                'discounts' => Discounts::class,
                'fields' => Fields::class,
                'notifications' => Notifications::class,
                'orders' => Orders::class,
                'products' => Products::class,
                'shipments' => Shipments::class,
                'subscriptions' => Subscriptions::class,
                'webhooks' => Webhooks::class,
            ],
        ];
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
}