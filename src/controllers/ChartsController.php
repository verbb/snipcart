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
use yii\base\Response;
use DateTimeZone;
use DateTime;
use craft\helpers\DateTimeHelper;

class ChartsController extends \craft\web\Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Fetch order data JSON for the Dashboard widget's chart.
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

    /**
     * Fetch order and sales stats in one response for the CP overview chart.
     *
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     * @throws
     */
    public function actionGetCombinedData(): Response
    {
        $this->requirePostRequest();

        $formats = [];

        $salesData = Snipcart::$plugin->data->getSales(
            $this->_getStartDate(),
            $this->_getEndDate()
        );

        $salesChartData = $this->_getTotalSales($salesData);
        $salesChartData['series'][0]['type'] = 'area';

        $formats['currencySymbol'] = Snipcart::$plugin->getSettings()
            ->getDefaultCurrencySymbol();

        $orderData = Snipcart::$plugin->data->getOrderCount(
            $this->_getStartDate(),
            $this->_getEndDate()
        );
        $orderChartData = $this->_getNumberOfOrders($orderData);

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

    /**
     * Get the beginning of the range used for visualizing stats.
     * @return DateTime
     * @throws
     */
    private function _getStartDate(): DateTime
    {
        $startDateParam = Craft::$app->getRequest()->getParam('startDate');

        if ($startDateParam && is_string($startDateParam))
        {
            return DateTimeHelper::toDateTime([ 'date' => $startDateParam ]);
        }
        
        return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
            ->modify('-1 month');
    }

    /**
     * Get the end of the range used for visualizing stats.
     * @return DateTime
     * @throws
     */
    private function _getEndDate(): DateTime
    {
        $endDateParam = Craft::$app->getRequest()->getParam('endDate');

        if ($endDateParam && is_string($endDateParam))
        {
            return DateTimeHelper::toDateTime([ 'date' => $endDateParam ]);
        }

        return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
    }

}