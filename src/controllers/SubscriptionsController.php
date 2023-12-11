<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class SubscriptionsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $page = Craft::$app->getRequest()->getPageNum();
        $subscriptions = Snipcart::$plugin->getSubscriptions()->listSubscriptions($page);
        $totalPages = ceil($subscriptions->totalItems / $subscriptions->limit);

        return $this->renderTemplate('snipcart/cp/subscriptions/index', [
            'pageNumber' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $subscriptions->totalItems,
            'subscriptions' => $subscriptions->items,
        ]);
    }

    public function actionDetail(string $subscriptionId): Response
    {
        $subscription = Snipcart::$plugin->getSubscriptions()->getSubscription($subscriptionId);

        return $this->renderTemplate('snipcart/cp/subscriptions/detail', [
            'subscription' => $subscription,
        ]);
    }

    public function actionCancel(): Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        Snipcart::$plugin->getSubscriptions()->cancel($params['subscriptionId']);

        Craft::$app->getSession()->setNotice('Subscription cancelled.');

        return $this->redirectToPostedUrl();
    }
}
