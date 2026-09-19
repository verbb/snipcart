<?php
namespace modules\snipcartscreenshots;

use craft\web\Application;
use verbb\snipcart\Snipcart;
use yii\base\Event;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init(): void
    {
        parent::init();

        Event::on(
            Application::class,
            Application::EVENT_INIT,
            static function(): void {
                if (Snipcart::$plugin) {
                    Snipcart::$plugin->set('orders', ScreenshotOrders::class);
                    Snipcart::$plugin->set('customers', ScreenshotCustomers::class);
                    Snipcart::$plugin->set('data', ScreenshotData::class);
                }
            },
        );
    }
}
