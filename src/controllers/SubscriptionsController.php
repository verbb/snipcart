<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use Craft;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use fostercommerce\snipcart\Snipcart;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SubscriptionsController extends Controller
{
    /**
     * Displays paginated list of subscriptions.
     *
     * @throws
     */
    public function actionIndex(): Response
    {
        $page = Craft::$app->getRequest()->getPageNum();
        $subscriptions = Snipcart::$plugin->subscriptions->listSubscriptions($page);
        $totalPages = ceil($subscriptions->totalItems / $subscriptions->limit);

        return $this->renderTemplate(
            'snipcart/cp/subscriptions/index',
            [
                'pageNumber' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $subscriptions->totalItems,
                'subscriptions' => $subscriptions->items,
            ]
        );
    }

    /**
     * Displays subscription detail.
     *
     * @throws \Exception
     */
    public function actionDetail(string $subscriptionId): Response
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
     * @throws MissingComponentException
     * @throws BadRequestHttpException
     */
    public function actionCancel(): Response
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
