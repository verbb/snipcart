<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;

class SnipcartController extends Controller
{
    
    public function actionSaveAccount()
    {
        $this->requirePostRequest();

        if (Craft::$app->snipcart->saveAccount()) {
            Craft::$app->userSession->setNotice(Craft::t('Account settings saved.'));
            $this->redirectToPostedUrl();
        }
    }

}
