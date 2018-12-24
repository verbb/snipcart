<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart;

use workingconcept\snipcart\services\Api;
use workingconcept\snipcart\services\Carts;
use workingconcept\snipcart\services\Customers;
use workingconcept\snipcart\services\Discounts;
use workingconcept\snipcart\services\Orders;
use workingconcept\snipcart\services\Products;
use workingconcept\snipcart\services\Shipments;
use workingconcept\snipcart\services\Subscriptions;
use workingconcept\snipcart\variables\SnipcartVariable;
use workingconcept\snipcart\widgets\Orders as OrdersWidget;
use workingconcept\snipcart\models\Settings;
use workingconcept\snipcart\fields\ProductDetails;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\fields\Number;
use craft\fields\PlainText;
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
 * @property  Api           $api
 * @property  Carts         $carts
 * @property  Customers     $customers
 * @property  Discounts     $discounts
 * @property  Orders        $orders
 * @property  Products      $products
 * @property  Shipments     $shipments
 * @property  Subscriptions $subscriptions
 */
class Snipcart extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Snipcart
     */
    public static $plugin;


    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.2';

    // Public Methods
    // =========================================================================

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
            'discounts'     => Discounts::class,
            'orders'        => Orders::class,
            'products'      => Products::class,
            'shipments'     => Shipments::class,
            'subscriptions' => Subscriptions::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('snipcart', SnipcartVariable::class);
            }
        );

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = OrdersWidget::class;
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event)
            {
                $event->types[] = ProductDetails::class;
            }
        );

        if (Craft::$app->getRequest()->isCpRequest)
        {
            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_CP_URL_RULES,
                function(RegisterUrlRulesEvent $event) {
                    $rules = [
                        'snipcart/order/<orderId>' => ['template' => 'snipcart/order'],
                        'snipcart/orders/<pageNumber>' => ['template' => 'snipcart/index'],
                        'snipcart/customers/<pageNumber>' => ['template' => 'snipcart/customers'],
                        'snipcart/customer/<customerId>' => ['template' => 'snipcart/customer'],
                        'snipcart/discounts' => ['template' => 'snipcart/discounts'],
                        'snipcart/abandoned' => ['template' => 'snipcart/abandoned'],
                    ];

                    $event->rules = array_merge($event->rules, $rules);
                }
            );
        }

        if (Craft::$app instanceof ConsoleApplication)
        {
            $this->controllerNamespace = 'workingconcept\snipcart\console\controllers';
        }

        $fileTarget = new \craft\log\FileTarget([
            'logFile' => Craft::getAlias('@storage/logs/snipcart.log'),
            'categories' => ['snipcart']
        ]);

        // include the new target file target to the dispatcher
        Craft::getLogger()->dispatcher->targets[] = $fileTarget;
    }

    // Protected Methods
    // =========================================================================

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
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'snipcart/_settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

}
