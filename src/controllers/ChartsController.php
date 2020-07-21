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
    /**
     * Fetches order data JSON for the Dashboard widget's chart.
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

        if ($range === 'weekly') {
            $startDate = (new \DateTime('now'))->modify('-1 week');
        } elseif ($range === 'monthly') {
            $startDate = (new \DateTime('now'))->modify('-1 month');
        } else {
            $problem = 'Invalid date range requested.';
            Craft::error($problem, 'snipcart');
            return $this->asJson($problem);
        }

        $endDate = (new \DateTime('now'))->modify('-1 day');
        $formats = [];

        if ($type === 'totalSales') {
            $data = Snipcart::$plugin->data->getSales($startDate, $endDate);
            $chartData = $this->getTotalSales($data);
            $formats['currencySymbol'] = Snipcart::$plugin->getSettings()
                ->getDefaultCurrencySymbol();
        } elseif ($type === 'numberOfOrders') {
            $data = Snipcart::$plugin->data->getOrderCount($startDate, $endDate);
            $chartData = $this->getNumberOfOrders($data);
        } else {
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
     * Fetches order and sales stats in one response for the CP overview chart.
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
            $this->getStartDate(),
            $this->getEndDate()
        );

        $salesChartData = $this->getTotalSales($salesData);
        $salesChartData['series'][0]['type'] = 'area';

        $formats['currencySymbol'] = Snipcart::$plugin->getSettings()
            ->getDefaultCurrencySymbol();

        $orderData = Snipcart::$plugin->data->getOrderCount(
            $this->getStartDate(),
            $this->getEndDate()
        );
        $orderChartData = $this->getNumberOfOrders($orderData);

        return $this->asJson([
            'series' => [
                $orderChartData['series'][0],
                $salesChartData['series'][0],
            ],
            'columns' => $salesChartData['columns'],
            'formats' => $formats,
        ]);
    }

    /**
     * Gets chart series for Snipcart sales data.
     *
     * @param $data
     * @return array
     */
    private function getTotalSales($data): array
    {
        return $this->formatForChart($data, 'Sales');
    }

    /**
     * Gets chart series for Snipcart orders data.
     *
     * @param $data
     * @return array
     */
    private function getNumberOfOrders($data): array
    {
        return $this->formatForChart($data, 'Orders');
    }

    /**
     * Translates Snipcart’s returned data into a chart-friendly series.
     *
     * @param $data
     * @param string $label
     *
     * @return array
     */
    private function formatForChart($data, $label): array
    {
        $rows = [];
        $columns = [];

        foreach ($data->data as $row) {
            $rows[] = $row->value;
            $columns[] = $row->name;
        }

        return [
            'series' => [
                [
                    'name' => $label,
                    'data' => $rows,
                ]
            ],
            'columns' => $columns
        ];
    }

    /**
     * Gets the beginning of the range used for visualizing stats.
     *
     * @return DateTime
     * @throws
     */
    private function getStartDate(): DateTime
    {
        $startDateParam = Craft::$app->getRequest()->getParam('startDate');

        if ($startDateParam && is_string($startDateParam)) {
            return DateTimeHelper::toDateTime([ 'date' => $startDateParam ]);
        }
        
        return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
            ->modify('-1 month');
    }

    /**
     * Gets the end of the range used for visualizing stats.
     *
     * @return DateTime
     * @throws
     */
    private function getEndDate(): DateTime
    {
        $endDateParam = Craft::$app->getRequest()->getParam('endDate');

        if ($endDateParam && is_string($endDateParam)) {
            return DateTimeHelper::toDateTime([ 'date' => $endDateParam ]);
        }

        return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
    }

}