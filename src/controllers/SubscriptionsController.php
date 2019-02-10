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

class SubscriptionsController extends \craft\web\Controller
{
    public function actionCancel()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        Snipcart::$plugin->subscriptions->cancel(
            $params['subscriptionId']
        );

        Craft::$app->getSession()->setNotice('Subscription cancelled.');

        return $this->redirectToPostedUrl();
    }
}