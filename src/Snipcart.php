<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart;

use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\providers\shipstation\ShipStation;
use fostercommerce\snipcart\services\Api;
use fostercommerce\snipcart\services\Carts;
use fostercommerce\snipcart\services\Customers;
use fostercommerce\snipcart\services\Data;
use fostercommerce\snipcart\services\DigitalGoods;
use fostercommerce\snipcart\services\Discounts;
use fostercommerce\snipcart\services\Fields as SnipcartFields;
use fostercommerce\snipcart\services\Notifications;
use fostercommerce\snipcart\services\Orders;
use fostercommerce\snipcart\services\Products;
use fostercommerce\snipcart\services\Shipments;
use fostercommerce\snipcart\services\Subscriptions;
use fostercommerce\snipcart\services\Webhooks;
use fostercommerce\snipcart\variables\SnipcartVariable;
use fostercommerce\snipcart\widgets\Orders as OrdersWidget;
use fostercommerce\snipcart\models\Settings;
use fostercommerce\snipcart\fields\ProductDetails;
use fostercommerce\snipcart\assetbundles\PluginSettingsAsset;
use fostercommerce\snipcart\events\RegisterShippingProvidersEvent;
use fostercommerce\snipcart\helpers\RouteHelper;
use fostercommerce\snipcart\helpers\CraftQlHelper;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\utilities\ClearCaches;
use craft\services\Fields;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\console\Application as ConsoleApplication;
use craft\services\Dashboard;
use yii\base\Event;

/**
 * Class Snipcart
 *
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 *
 * @property  Api            $api
 * @property  Carts          $carts
 * @property  Customers      $customers
 * @property  Data           $data
 * @property  Discounts      $discounts
 * @property  SnipcartFields $fields
 * @property  Notifications  $notifications
 * @property  Orders         $orders
 * @property  Products       $products
 * @property  Shipments      $shipments
 * @property  Subscriptions  $subscriptions
 * @property  Webhooks       $webhooks
 */
class Snipcart extends Plugin
{
    /**
     * @var Snipcart
     */
    public static $plugin;

    /**
     * @event ShippingProviderEvent
     */
    const EVENT_REGISTER_SHIPPING_PROVIDERS = 'registerShippingProviders';

    /**
     * @var string
     */
    public $schemaVersion = '1.0.10';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
                
        $this->setComponents([
            'api'           => Api::class,
            'carts'         => Carts::class,
            'customers'     => Customers::class,
            'data'          => Data::class,
            'digitalGoods'  => DigitalGoods::class,
            'discounts'     => Discounts::class,
            'fields'        => SnipcartFields::class,
            'orders'        => Orders::class,
            'notifications' => Notifications::class,
            'products'      => Products::class,
            'shipments'     => Shipments::class,
            'subscriptions' => Subscriptions::class,
            'webhooks'      => Webhooks::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event) {
                $variable = $event->sender;
                $variable->set('snipcart', SnipcartVariable::class);
            }
        );

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = OrdersWidget::class;
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = ProductDetails::class;
            }
        );

        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            static function (RegisterCacheOptionsEvent $event) {
                $event->options = array_merge(
                    $event->options,
                    [
                        [
                            'key'    => Api::CACHE_TAG,
                            'action' => [Snipcart::$plugin->api, 'invalidateCache'],
                            'label'  => Craft::t('snipcart', 'Snipcart API cache'),
                        ],
                    ]
                );
            }
        );

        /**
         * Tell CraftQL how to grab Snipcart Product Details field data.
         */
        if (Craft::$app->getPlugins()->isPluginInstalled('craftql')) {
            Event::on(
                ProductDetails::class,
                'craftQlGetFieldSchema',
                static function ($event) {
                    $event->handled = true;

                    $outputSchema = CraftQlHelper::addFieldTypeToSchema(
                        $event->sender->handle,
                        $event->schema
                    );

                    $event->schema->addField($event->sender)
                        ->type($outputSchema);
                }
            );
        }

        /**
         * Register routes for a control panel request.
         */
        if (Craft::$app->getRequest()->isCpRequest) {
            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_CP_URL_RULES,
                static function (RegisterUrlRulesEvent $event) {
                    $event->rules = array_merge(
                        $event->rules,
                        RouteHelper::getCpRoutes()
                    );
                }
            );
        }

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'fostercommerce\snipcart\console\controllers';
        }

        $this->registerShippingProviders();
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        Craft::$app->getView()->registerAssetBundle(
            PluginSettingsAsset::class
        );

        return \Craft::$app->controller->renderTemplate(
            'snipcart/_settings',
            [
                'settings' => $this->getSettings(),
                'isCraft31' => VersionHelper::isCraft31(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'snipcart/_settings',
            [
                'settings' => $this->getSettings(),
                'isCraft31' => VersionHelper::isCraft31(),
            ]
        );
    }

    /**
     * Instantiate Shipping providers and make each available in an indexed array.
     */
    private function registerShippingProviders()
    {
        // just one for now!
        $shippingProviders = [ ShipStation::class ];

        if ($this->hasEventHandlers(self::EVENT_REGISTER_SHIPPING_PROVIDERS)) {
            $event = new RegisterShippingProvidersEvent([
                'shippingProviders' => $shippingProviders,
            ]);

            $this->trigger(self::EVENT_REGISTER_SHIPPING_PROVIDERS, $event);

            $shippingProviders = $event->shippingProviders;
        }

        $pluginSettings = $this->getSettings();

        foreach ($shippingProviders as $shippingProviderClass) {
            $instance = new $shippingProviderClass();

            $pluginSettings->addProvider($instance->refHandle(), $instance);
        }
    }
    
    

    /**
     * Logging method
     */
    static function log($message)
    {
        $file = Craft::getAlias('@storage/logs/craft-snipcart.log');
        $log = date('Y-m-d H:i:s').' '.$message."\n";

        \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }

}
