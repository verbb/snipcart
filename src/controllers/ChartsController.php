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

        $endDate = new \DateTime('now');

        if ($type === 'totalSales')
        {
            $data = Snipcart::$plugin->data->getSales($startDate, $endDate);
            $chartData = $this->_getTotalSales($data);
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

        $rows    = $chartData['rows'];
        $columns = $chartData['columns'];

        $defaultFormats = [
            'numberFormat' => ',.0f',
            'percentFormat' => ',.2%',
            'currencyFormat' => '$,.2f',
            'shortDateFormats' => [
                'day'   => '%-m/%-d',
                'month' => '%-m/%y',
                'year'  => '%Y'
            ]
        ];

        return $this->asJson([
            'dataTable'        => [
                'columns' => $columns,
                'rows'    => $rows,
            ],
            'total'            => count($chartData['rows']),
            'totalHtml'        => count($chartData['rows']),
            'formats'          => $defaultFormats,
            'orientation'      => Craft::$app->locale->getOrientation(),
            'scale'            => 'day',
            'localeDefinition' => [],
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

        foreach ($data->data as $row)
        {
            $rows[] = [
                $row->name,
                $row->value
            ];
        }

        return [
            'rows' => $rows,
            'columns' => [
                [
                    'type'  => 'date',
                    'label' => 'Day'
                ],
                [
                    'type'  => 'currency',
                    'label' => 'Sales'
                ]
            ],
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

        foreach ($data->data as $row)
        {
            $rows[] = [
                $row->name,
                $row->value
            ];
        }

        return [
            'rows' => $rows,
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
        ];
    }
}