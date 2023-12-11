<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;

use yii\web\Response;

use DateTime;

class OrdersController extends Controller
{
    // Constants
    // =========================================================================

    public const START_DATE_PARAM = 'startDate';
    public const START_DATE_SESSION_KEY = 'snipcartStartDate';
    public const END_DATE_PARAM = 'endDate';
    public const END_DATE_SESSION_KEY = 'snipcartEndDate';


    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $startDate = $this->getDateRangeExtent('start');
        $endDate = $this->getDateRangeExtent('end');

        $page = Craft::$app->getRequest()->getPageNum();
        $orders = Snipcart::$plugin->getOrders()->listOrders($page, 20, [
            'from' => $startDate,
            'to' => $endDate,
        ]);

        $totalPages = ceil($orders->totalItems / $orders->limit);

        return $this->renderTemplate('snipcart/cp/orders/index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageNumber' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $orders->totalItems,
            'orders' => $orders->items,
        ]);
    }

    public function actionOrderDetail(string $orderId): Response
    {
        $order = Snipcart::$plugin->getOrders()->getOrder($orderId);
        $orderRefunds = Snipcart::$plugin->getOrders()->getOrderRefunds($orderId);
        
        return $this->renderTemplate('snipcart/cp/orders/detail', [
            'order' => $order,
            'orderRefunds' => $orderRefunds,
        ]);
    }

    public function actionRefund(): Response
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->post();

        Snipcart::$plugin->getOrders()->refundOrder($params['orderId'], $params['amount'], $params['comment'], $params['notifyCustomer']);

        Craft::$app->getSession()->setNotice('Order refunded.');

        return $this->redirectToPostedUrl();
    }


    // Private Methods
    // =========================================================================

    private function getDateRangeExtent(string $extent): mixed
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $defaultValue = $extent === 'start' ? (new DateTime())->modify('-1 month') : new DateTime();
        $date = $defaultValue;

        $paramName = $extent === 'start' ? self::START_DATE_PARAM : self::END_DATE_PARAM;
        $sessionKey = $extent === 'start' ? self::START_DATE_SESSION_KEY : self::END_DATE_SESSION_KEY;

        $paramValue = $request->getParam($paramName);
        $sessionValue = $session->get($sessionKey);
        $storedValue = $paramValue ?? $sessionValue ?? '';

        if (!empty($storedValue)) {
            $date = DateTimeHelper::toDateTime($storedValue);
        }

        if ($session) {
            $session->set($sessionKey, $date);
        }

        return $date;
    }
}
