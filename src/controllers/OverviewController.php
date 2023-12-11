<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\helpers\FormatHelper;
use verbb\snipcart\Snipcart;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;

use yii\web\Response;

use DateTime;
use DateTimeZone;

class OverviewController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        if (!Snipcart::$plugin->getSettings()->isConfigured()) {
            return $this->renderTemplate('snipcart/cp/welcome');
        }

        return $this->renderTemplate('snipcart/cp/index', $this->getOrderAndCustomerSummary());
    }

    public function actionGetStats(): Response
    {
        return $this->asJson($this->getOverviewStats(true));
    }

    public function actionGetOrdersCustomers(): Response
    {
        return $this->asJson($this->getOrderAndCustomerSummary(true));
    }


    // Private Methods
    // =========================================================================

    private function getOverviewStats(bool $preFormat = false): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $stats = Snipcart::$plugin->getData()->getPerformance($startDate, $endDate);

        if ($preFormat) {
            $defaultCurrency = Snipcart::$plugin->getSettings()->defaultCurrency;

            $stats->ordersCount = number_format($stats->ordersCount);
            $stats->ordersSales = FormatHelper::formatCurrency($stats->ordersSales, $defaultCurrency);
            $stats->averageOrdersValue = FormatHelper::formatCurrency($stats->averageOrdersValue, $defaultCurrency);
            $stats->averageCustomerValue = FormatHelper::formatCurrency($stats->averageCustomerValue, $defaultCurrency);
        }

        return [
            'stats' => $stats,
        ];
    }

    private function getOrderAndCustomerSummary(bool $preFormat = false): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $orders = Snipcart::$plugin->getOrders()->listOrders(1, 10);

        $customers = Snipcart::$plugin->getCustomers()->listCustomers(1, 10, [
            'orderBy' => 'ordersValue',
        ]);

        if ($preFormat) {
            foreach ($orders->items as &$item) {
                // TODO: see if there's a better way to attach dynamic fields
                $item = $item->toArray(['id', 'invoiceNumber', 'creationDate', 'finalGrandTotal'], ['cpUrl', 'billingAddressName']);

                $item['creationDate'] = DateTimeHelper::toDateTime($item['creationDate'])->format('n/j');
                $item['finalGrandTotal'] = FormatHelper::formatCurrency($item['finalGrandTotal']);
            }

            foreach ($customers->items as &$item) {
                // TODO: see if there's a better way to attach dynamic fields
                $item = $item->toArray(['id', 'billingAddressName', 'statistics'], ['cpUrl']);

                $item['statistics']['ordersCount'] = number_format($item['statistics']['ordersCount']);
                $item['statistics']['ordersAmount'] = FormatHelper::formatCurrency($item['statistics']['ordersAmount']);
            }
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'orders' => $orders,
            'customers' => $customers,
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
