<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;
use verbb\snipcart\services\Api;

use Craft;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SubscriptionsController extends BaseCpController
{
    // Properties
    // =========================================================================

    protected array $actionPermissions = [
        'cancel' => Snipcart::PERMISSION_MANAGE_SUBSCRIPTIONS,
    ];


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

        $subscriptionId = (string)Craft::$app->getRequest()->getRequiredBodyParam('subscriptionId');
        $subscription = Snipcart::$plugin->getSubscriptions()->getSubscription($subscriptionId, false);

        if (!$subscription || !$subscription->id) {
            throw new NotFoundHttpException('Subscription not found.');
        }

        Snipcart::$plugin->getSubscriptions()->cancel($subscription->id);
        Api::invalidateCache();

        Craft::$app->getSession()->setNotice('Subscription cancelled.');

        return $this->redirectToPostedUrl();
    }
}
