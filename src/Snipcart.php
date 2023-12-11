<?php
namespace verbb\snipcart;

use verbb\snipcart\assetbundles\PluginSettingsAsset;
use verbb\snipcart\events\RegisterShippingProvidersEvent;
use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\helpers\CraftQlHelper;
use verbb\snipcart\helpers\RouteHelper;
use verbb\snipcart\helpers\VersionHelper;
use verbb\snipcart\models\Settings;
use verbb\snipcart\providers\shipstation\ShipStation;
use verbb\snipcart\services\Api;
use verbb\snipcart\services\Carts;
use verbb\snipcart\services\Customers;
use verbb\snipcart\services\Data;
use verbb\snipcart\services\DigitalGoods;
use verbb\snipcart\services\Discounts;
use verbb\snipcart\services\Fields as SnipcartFields;
use verbb\snipcart\services\Notifications;
use verbb\snipcart\services\Orders;
use verbb\snipcart\services\Products;
use verbb\snipcart\services\Shipments;
use verbb\snipcart\services\Subscriptions;
use verbb\snipcart\services\Webhooks;
use verbb\snipcart\variables\SnipcartVariable;
use verbb\snipcart\widgets\Orders as OrdersWidget;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class Snipcart extends Plugin
{
    const EVENT_REGISTER_SHIPPING_PROVIDERS = 'registerShippingProviders';

    public static $plugin;
    public $schemaVersion = '1.1.10';
    public $hasCpSection = true;
    public $hasCpSettings = true;

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
            $this->controllerNamespace = 'verbb\snipcart\console\controllers';
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

}
