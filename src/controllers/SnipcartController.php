<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use Craft;
use craft\web\Controller;

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
