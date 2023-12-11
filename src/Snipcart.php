<?php
namespace verbb\snipcart;

use verbb\snipcart\base\PluginTrait;
use verbb\snipcart\events\RegisterShippingProvidersEvent;
use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\models\Settings;
use verbb\snipcart\providers\shipstation\ShipStation;
use verbb\snipcart\services\Api;
use verbb\snipcart\variables\SnipcartVariable;
use verbb\snipcart\widgets\Orders as OrdersWidget;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\utilities\ClearCaches;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;

use yii\base\Event;

class Snipcart extends Plugin
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_SHIPPING_PROVIDERS = 'registerShippingProviders';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Properties
    // =========================================================================

    public string $schemaVersion = '1.1.0';
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerFieldTypes();
        $this->_registerVariable();
        $this->_registerCacheTypes();
        $this->_registerShippingProviders();

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
            $this->_registerWidgets();
        }
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('snipcart/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['snipcart'] = 'snipcart/overview/index';
            $event->rules['snipcart/orders'] = 'snipcart/orders/index';
            $event->rules['snipcart/order/<orderId>'] = 'snipcart/orders/order-detail';
            $event->rules['snipcart/customers'] = 'snipcart/customers/index';
            $event->rules['snipcart/customer/<customerId>'] = 'snipcart/customers/customer-detail';
            $event->rules['snipcart/discounts'] = 'snipcart/discounts/index';
            $event->rules['snipcart/discounts/new'] = 'snipcart/discounts/new';
            $event->rules['snipcart/discount/<discountId>'] = 'snipcart/discounts/discount-detail';
            $event->rules['snipcart/abandoned'] = 'snipcart/carts/index';
            $event->rules['snipcart/abandoned/<cartId>'] = 'snipcart/carts/detail';
            $event->rules['snipcart/subscriptions'] = 'snipcart/subscriptions/index';
            $event->rules['snipcart/subscription/<subscriptionId>'] = 'snipcart/subscriptions/detail';
            $event->rules['snipcart/settings'] = 'snipcart/plugin/settings';
        });
    }

    private function _registerVariable(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('snipcart', SnipcartVariable::class);
        });
    }

    private function _registerFieldTypes(): void
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = ProductDetails::class;
        });
    }

    private function _registerWidgets(): void
    {
        Event::on(Dashboard::class, Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = OrdersWidget::class;
        });
    }

    private function _registerCacheTypes(): void
    {
        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS, function(RegisterCacheOptionsEvent $event) {
            $event->options[] = [
                'key' => Api::CACHE_TAG,
                'label' => Craft::t('snipcart', 'Snipcart API cache'),
                'action' => [Snipcart::$plugin->getApi(), 'invalidateCache'],
            ];
        });
    }

    private function _registerShippingProviders(): void
    {
        $shippingProviders = [ShipStation::class];

        if ($this->hasEventHandlers(self::EVENT_REGISTER_SHIPPING_PROVIDERS)) {
            $registerShippingProvidersEvent = new RegisterShippingProvidersEvent([
                'shippingProviders' => $shippingProviders,
            ]);

            $this->trigger(self::EVENT_REGISTER_SHIPPING_PROVIDERS, $registerShippingProvidersEvent);

            $shippingProviders = $registerShippingProvidersEvent->shippingProviders;
        }

        $pluginSettings = $this->getSettings();

        foreach ($shippingProviders as $shippingProvider) {
            $instance = new $shippingProvider();

            $pluginSettings->addProvider($instance->refHandle(), $instance);
        }
    }
}
