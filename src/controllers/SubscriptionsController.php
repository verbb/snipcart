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
    /**
     * Displays paginated list of subscriptions.
     *
     * @return \yii\web\Response
     * @throws
     */
    public function actionIndex(): \yii\web\Response
    {
        $page          = Craft::$app->getRequest()->getPageNum();
        $subscriptions = Snipcart::$plugin->subscriptions->listSubscriptions($page);
        $totalPages    = ceil($subscriptions->totalItems / $subscriptions->limit);

        return $this->renderTemplate(
            'snipcart/cp/subscriptions/index',
            [
                'pageNumber'    => $page,
                'totalPages'    => $totalPages,
                'totalItems'    => $subscriptions->totalItems,
                'subscriptions' => $subscriptions->items,
            ]
        );
    }

    /**
     * Displays subscription detail.
     *
     * @param string $subscriptionId
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDetail(string $subscriptionId): \yii\web\Response
    {
        $subscription = Snipcart::$plugin->subscriptions->getSubscription($subscriptionId);

        return $this->renderTemplate(
            'snipcart/cp/subscriptions/detail',
            [
                'subscription' => $subscription,
            ]
        );
    }

    /**
     * Cancels a subscription.
     *
     * @return \yii\web\Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionCancel(): \yii\web\Response
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