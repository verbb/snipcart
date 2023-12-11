<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;

use yii\base\Response;

use DateTime;
use DateTimeZone;

class ChartsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionGetOrdersData(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $type = $request->getRequiredParam('type');
        $range = $request->getRequiredParam('range');

        if ($range === 'weekly') {
            $startDate = (new DateTime('now'))->modify('-1 week');
        } else if ($range === 'monthly') {
            $startDate = (new DateTime('now'))->modify('-1 month');
        } else {
            $problem = 'Invalid date range requested.';
            Snipcart::error($problem);

            return $this->asJson($problem);
        }

        $endDate = (new DateTime('now'))->modify('-1 day');
        $formats = [];

        if ($type === 'totalSales') {
            $data = Snipcart::$plugin->getData()->getSales($startDate, $endDate);
            $chartData = $this->getTotalSales($data);

            $formats['currencySymbol'] = Snipcart::$plugin->getSettings()->getDefaultCurrencySymbol();
        } else if ($type === 'numberOfOrders') {
            $data = Snipcart::$plugin->getData()->getOrderCount($startDate, $endDate);
            $chartData = $this->getNumberOfOrders($data);
        } else {
            $problem = 'Invalid chart type requested.';
            Snipcart::error($problem);

            return $this->asJson($problem);
        }

        return $this->asJson([
            'series' => $chartData['series'],
            'columns' => $chartData['columns'],
            'formats' => $formats,
        ]);
    }

    public function actionGetCombinedData(): Response
    {
        $this->requirePostRequest();

        $formats = [];

        $salesData = Snipcart::$plugin->getData()->getSales($this->getStartDate(), $this->getEndDate());

        $salesChartData = $this->getTotalSales($salesData);
        $salesChartData['series'][0]['type'] = 'area';

        $formats['currencySymbol'] = Snipcart::$plugin->getSettings()->getDefaultCurrencySymbol();

        $orderData = Snipcart::$plugin->getData()->getOrderCount($this->getStartDate(), $this->getEndDate());
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


    // Private Methods
    // =========================================================================

    private function getTotalSales($data): array
    {
        return $this->formatForChart($data, 'Sales');
    }

    private function getNumberOfOrders($data): array
    {
        return $this->formatForChart($data, 'Orders');
    }

    private function formatForChart($data, string $label): array
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
                ],
            ],
            'columns' => $columns,
        ];
    }

    private function getStartDate(): DateTime
    {
        $startDateParam = Craft::$app->getRequest()->getParam('startDate');

        if (!$startDateParam) {
            return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
                ->modify('-1 month');
        }

        if (!is_string($startDateParam)) {
            return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
                ->modify('-1 month');
        }

        return DateTimeHelper::toDateTime([
            'date' => $startDateParam,
        ]);
    }

    private function getEndDate(): DateTime
    {
        $endDateParam = Craft::$app->getRequest()->getParam('endDate');
        
        if (!$endDateParam) {
            return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
        }

        if (!is_string($endDateParam)) {
            return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
        }

        return DateTimeHelper::toDateTime([
            'date' => $endDateParam,
        ]);
    }
}
