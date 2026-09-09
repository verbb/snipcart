<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;
use verbb\snipcart\models\Settings;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class PluginController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        /* @var Settings $settings */
        $settings = Snipcart::$plugin->getSettings();

        return $this->renderTemplate('snipcart/settings', [
            'settings' => $settings,
            'selectedTab' => Craft::$app->getRequest()->getSegment(3) ?: 'general',
        ]);
    }
}
