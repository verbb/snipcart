<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use craft\web\Controller;
use yii\web\Response;
use craft\errors\MissingComponentException;
use yii\web\BadRequestHttpException;
use Craft;
use craft\helpers\DateTimeHelper;
use DateTime;
use fostercommerce\snipcart\Snipcart;

class OrdersController extends Controller
{
    public const START_DATE_PARAM = 'startDate';

    public const START_DATE_SESSION_KEY = 'snipcartStartDate';

    public const END_DATE_PARAM = 'endDate';

    public const END_DATE_SESSION_KEY = 'snipcartEndDate';

    /**
     * Displays paginated list of orders.
     *
     * @throws
     */
    public function actionIndex(): Response
    {
        $startDate = $this->getDateRangeExtent('start');
        $endDate = $this->getDateRangeExtent('end');

        $page = Craft::$app->getRequest()->getPageNum();
        $orders = Snipcart::$plugin->orders->listOrders($page, 20, [
            'from' => $startDate,
            'to' => $endDate,
        ]);

        $totalPages = ceil($orders->totalItems / $orders->limit);

        return $this->renderTemplate(
            'snipcart/cp/orders/index',
            [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'pageNumber' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $orders->totalItems,
                'orders' => $orders->items,
            ]
        );
    }

    /**
     * Displays order detail.
     *
     * @throws
     */
    public function actionOrderDetail(string $orderId): Response
    {
        $order = Snipcart::$plugin->orders->getOrder($orderId);
        $orderRefunds = Snipcart::$plugin->orders->getOrderRefunds($orderId);
        return $this->renderTemplate(
            'snipcart/cp/orders/detail',
            [
                'order' => $order,
                'orderRefunds' => $orderRefunds,
            ]
        );
    }

    /**
     * Refunds an order.
     *
     * @throws MissingComponentException
     * @throws BadRequestHttpException
     */
    public function actionRefund(): Response
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

    /**
     * @param string $extent `start` or `end`
     *
     * @return array|bool|\DateTime|false|int|string|null
     * @throws MissingComponentException
     */
    private function getDateRangeExtent(string $extent)
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        /**
         * Set the default range one month ago to present.
         */
        $defaultValue = $extent === 'start' ?
            (new DateTime())->modify('-1 month') :
            new DateTime();

        /**
         * Initialize the variable we’ll be sending back.
         */
        $date = $defaultValue;

        /**
         * Choose the parameter name to check for a date.
         */
        $paramName = $extent === 'start' ?
            self::START_DATE_PARAM :
            self::END_DATE_PARAM;

        /**
         * Choose the session key to check for a date.
         */
        $sessionKey = $extent === 'start' ?
            self::START_DATE_SESSION_KEY :
            self::END_DATE_SESSION_KEY;

        /**
         * Get any stored parameter.
         */
        $paramValue = $request->getParam($paramName);

        /**
         * Get any stored session value.
         */
        $sessionValue = $session->get($sessionKey);

        /**
         * First take any parameter, then any session value. Or take empty.
         */
        $storedValue = $paramValue ?? $sessionValue ?? '';

        if (! empty($storedValue)) {
            /**
             * If we have a stored value, make sure it’s a DateTime.
             */
            $date = DateTimeHelper::toDateTime($storedValue);
        }

        if ($session) {
            /**
             * If we have a session to work with, set our value there
             * before returning it.
             */
            $session->set($sessionKey, $date);
        }

        return $date;
    }
}
