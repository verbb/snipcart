<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use craft\helpers\ChartHelper;
use Craft;
use yii\base\Response;

class ChartsController extends \craft\web\Controller
{

    public function actionGetOrdersData(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $type    = $request->getRequiredParam('type');
        $range   = $request->getRequiredParam('range');

        // TODO: account for chart types
            // itemsSold
            // totalSales
            // numberOfOrders

        $data = Snipcart::$plugin->orders->listOrdersByDay(1, 500);

        $rows = [];

        foreach ($data as $date => $orderCount)
        {
            $rows[] = [ $date, $orderCount ];
        }

        $dataTable = [
            'columns' => [
                [
                    'type'  => 'date',
                    'label' => 'Day'
                ],
                [
                    'type'  => 'number',
                    'label' => 'Orders'
                ]
            ],
            'rows' => $rows,
        ];

        return $this->asJson([
            'dataTable'        => $dataTable,
            'total'            => count($data),
            'totalHtml'        => count($data),
            'formats'          => ChartHelper::formats(),
            'orientation'      => Craft::$app->locale->getOrientation(),
            'scale'            => 'day',
            'localeDefinition' => [],
        ]);
    }
}