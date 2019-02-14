<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use DateTime;

class OverviewController extends \craft\web\Controller
{
    /**
     * Display store overview.
     * @return \yii\web\Response
     * @throws
     */
    public function actionIndex(): \yii\web\Response
    {
        if ( ! Snipcart::$plugin->getSettings()->isConfigured())
        {
            return $this->renderTemplate('snipcart/cp/welcome');
        }

        $startDate = (new DateTime())->modify('-1 month');
        $endDate   = new DateTime();
        $stats     = Snipcart::$plugin->data->getPerformance($startDate, $endDate);
        $orders    = Snipcart::$plugin->orders->listOrders(1, 10);
        $customers = Snipcart::$plugin->customers->listCustomers(1, 10, [
            'orderBy' => 'ordersValue'
        ]);

        return $this->renderTemplate('snipcart/cp/index',
            [
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'stats'     => $stats,
                'orders'    => $orders,
                'customers' => $customers,
            ]
        );
    }
}