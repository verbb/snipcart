<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;
use verbb\snipcart\models\Settings;

use Craft;

use yii\web\Response;

use verbb\base\controllers\SettingsController as BaseSettingsController;

class SettingsController extends BaseSettingsController
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        /* @var Settings $settings */
        $settings = Snipcart::$plugin->getSettings();

        return $this->renderTemplate('snipcart/settings', [
            'settings' => $settings,
            'selectedTab' => Craft::$app->getRequest()->getSegment(3) ?: 'general',
        ]);
    }
}
