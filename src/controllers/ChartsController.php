<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\helpers\FormatHelper;
use workingconcept\snipcart\Snipcart;
use Craft;
use yii\base\Response;

class ChartsController extends \craft\web\Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Fetch order data JSON for the Dashboard widget's chart.
     *
     * @todo Use another chart renderer so we can support displaying currencies
     *       that don't use a dollar sign.
     *
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionGetOrdersData(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $type    = $request->getRequiredParam('type');
        $range   = $request->getRequiredParam('range');

        if ($range === 'weekly')
        {
            $startDate = (new \DateTime('now'))->modify('-1 week');
        }
        elseif ($range === 'monthly')
        {
            $startDate = (new \DateTime('now'))->modify('-1 month');
        }
        else
        {
            $problem = 'Invalid date range requested.';
            Craft::error($problem, 'snipcart');
            return $this->asJson($problem);
        }

        $endDate = (new \DateTime('now'))->modify('-1 day');
        $formats = [];

        if ($type === 'totalSales')
        {
            $data = Snipcart::$plugin->data->getSales($startDate, $endDate);
            $chartData = $this->_getTotalSales($data);
            $formats['currencySymbol'] = Snipcart::$plugin->getSettings()
                ->getDefaultCurrencySymbol();
        }
        elseif ($type === 'numberOfOrders')
        {
            $data = Snipcart::$plugin->data->getOrderCount($startDate, $endDate);
            $chartData = $this->_getNumberOfOrders($data);
        }
        else
        {
            $problem = 'Invalid chart type requested.';
            Craft::error($problem, 'snipcart');
            return $this->asJson($problem);
        }

        return $this->asJson([
            'series'  => $chartData['series'],
            'columns' => $chartData['columns'],
            'formats' => $formats,
        ]);
    }

    public function actionGetCombinedData(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $type    = $request->getRequiredParam('type');

        $startDate = (new \DateTime('now'))->modify('-1 month');
        $endDate = (new \DateTime('now'))->modify('-1 day');
        $formats = [];

        $salesData = Snipcart::$plugin->data->getSales($startDate, $endDate);
        $salesChartData = $this->_getTotalSales($salesData);
        //$salesChartData['series'][0]['type'] = 'line';
        $formats['currencySymbol'] = Snipcart::$plugin->getSettings()
            ->getDefaultCurrencySymbol();

        $orderData = Snipcart::$plugin->data->getOrderCount($startDate, $endDate);
        $orderChartData = $this->_getNumberOfOrders($orderData);
        //$orderChartData['series'][0]['type'] = 'line';

        return $this->asJson([
            'series' => [
                $orderChartData['series'][0],
                $salesChartData['series'][0],
            ],
            'columns' => $salesChartData['columns'],
            'formats' => $formats,
        ]);

    }

    // Private Methods
    // =========================================================================

    /**
     * Reformat Snipcart's returned data into a chart-friendly format.
     *
     * @param $data
     * @return array
     */
    private function _getTotalSales($data): array
    {
        $rows = [];
        $columns = [];

        foreach ($data->data as $row)
        {
            $rows[] = $row->value;
            $columns[] = $row->name;
        }

        return [
            'series' => [
                [
                    'name' => 'Sales',
                    'data' => $rows,
                ]
            ],
            'columns' => $columns
        ];
    }

    /**
     * Reformat Snipcart's returned data into a chart-friendly format.
     *
     * @param $data
     * @return array
     */
    private function _getNumberOfOrders($data): array
    {
        $rows = [];
        $columns = [];

        foreach ($data->data as $row)
        {
            $rows[] = $row->value;
            $columns[] = $row->name;
        }

        return [
            'series' => [
                [
                    'name' => 'Orders',
                    'data' => $rows,
                ]
            ],
            'columns' => $columns
        ];
    }
}