<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart;

use craft\base\Field;
use craft\fields\Number;
use craft\fields\PlainText;
use workingconcept\snipcart\controllers\WebhooksController;
use workingconcept\snipcart\controllers\SnipcartController;
use workingconcept\snipcart\controllers\TestController;
use workingconcept\snipcart\controllers\VerifyController;
use workingconcept\snipcart\services\SnipcartService;
use workingconcept\snipcart\services\ShipStationService;
use workingconcept\snipcart\variables\SnipcartVariable;
use workingconcept\snipcart\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\console\Application as ConsoleApplication;

use yii\base\Event;

class Snipcart extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Reporter
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
            'snipcart'    => SnipcartService::class,
            'shipStation' => ShipStationService::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('snipcart', SnipcartVariable::class);
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
    }

    public function getSettingsResponse()
    {
        $view = Craft::$app->getView();
        $namespace = $view->getNamespace();
        $view->setNamespace('settings');
        
        $settingsHtml = $this->settingsHtml();
        $view->setNamespace($namespace);

        $controller = Craft::$app->controller;

        // TODO: update ShipStation webhook subscription if we've changed settings

        return $controller->renderTemplate('settings/plugins/_settings', [
            'plugin' => $this,
            'settingsHtml' => $settingsHtml
        ]);
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
    	$allFields = Craft::$app->fields->getAllFields();

    	// TODO: be a bit more civilized about this

    	$productIdentifierOptions = [
        	null => 'Select Field...',
			'id' => 'Element ID',
		];

    	$supportedProductIdentifierTypes = [
        	PlainText::class,
			Number::class,
		];

		$productInventoryFieldOptions = [
			null => 'Select Field...',
		];

		$orderNoteFieldOptions = [
			null => 'Select Field...',
		];

		$giftNoteFieldOptions = [
			null => 'Select Field...',
		];

		$supportedProductInventoryFieldTypes = [
			Number::class,
		];

		foreach ($allFields as $field)
        {
        	if (in_array(get_class($field), $supportedProductIdentifierTypes, true))
			{
				// disallow multiline text as an option
				if (isset($field->multiline) && $field->multiline)
				{
					continue;
				}

				$productIdentifierOptions[$field->handle] = $field->name;
			}

			if (in_array(get_class($field), $supportedProductInventoryFieldTypes, true))
			{
				$productInventoryFieldOptions[$field->handle] = $field->name;
				$orderNoteFieldOptions[$field->handle] = $field->name;
				$giftNoteFieldOptions[$field->handle] = $field->name;
			}
		}


		return Craft::$app->view->renderTemplate(
            'snipcart/_settings',
            [
                'productIdentifierOptions' => $productIdentifierOptions,
                'productInventoryFieldOptions' => $productInventoryFieldOptions,
                'orderNoteFieldOptions' => $orderNoteFieldOptions,
                'giftNoteFieldOptions' => $giftNoteFieldOptions,
                'settings' => $this->getSettings()
            ]
        );
    }

}
