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

class OrdersController extends \craft\web\Controller
{
    public function actionRefund()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        Snipcart::$plugin->orders->refundOrder(
            $params['orderId'],
            $params['amount'],
            $params['comment'],
            $params['notifyCustomer']
        );

        Craft::$app->getSession()->setNotice('Order refunded.');

        return $this->redirectToPostedUrl();
    }
}