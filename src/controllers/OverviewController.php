<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;
use DateTime;
use DateTimeZone;
use fostercommerce\snipcart\helpers\FormatHelper;
use fostercommerce\snipcart\Snipcart;
use yii\base\InvalidConfigException;
use yii\web\Response;

class OverviewController extends Controller
{
    /**
     * Displays store overview.
     *
     * @throws
     */
    public function actionIndex(): Response
    {
        if (! Snipcart::$plugin->getSettings()->isConfigured()) {
            return $this->renderTemplate('snipcart/cp/welcome');
        }

        return $this->renderTemplate(
            'snipcart/cp/index',
            $this->getOrderAndCustomerSummary()
        );
    }

    /**
     * Gets the stats for the top panels.
     *
     * @throws
     */
    public function actionGetStats(): Response
    {
        return $this->asJson(
            $this->getOverviewStats(true)
        );
    }

    /**
     * Gets the data for the recent order and top customer summary tables.
     *
     * @throws InvalidConfigException
     */
    public function actionGetOrdersCustomers(): Response
    {
        return $this->asJson(
            $this->getOrderAndCustomerSummary(true)
        );
    }

    /**
     * Gets store statistics for the Snipcart landing/overview.
     *
     * @throws InvalidConfigException
     */
    private function getOverviewStats(bool $preFormat = false): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $stats = Snipcart::$plugin->data->getPerformance($startDate, $endDate);

        if ($preFormat) {
            $defaultCurrency = Snipcart::$plugin->getSettings()->defaultCurrency;

            $stats->ordersCount = number_format($stats->ordersCount);
            $stats->ordersSales = FormatHelper::formatCurrency(
                $stats->ordersSales,
                $defaultCurrency
            );
            $stats->averageOrdersValue = FormatHelper::formatCurrency(
                $stats->averageOrdersValue,
                $defaultCurrency
            );
            $stats->averageCustomerValue = FormatHelper::formatCurrency(
                $stats->averageCustomerValue,
                $defaultCurrency
            );
        }

        return [
            'stats' => $stats,
        ];
    }

    /**
     * Gets recent order and top customer statistics.
     *
     * @throws InvalidConfigException
     */
    private function getOrderAndCustomerSummary(bool $preFormat = false): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $orders = Snipcart::$plugin->orders->listOrders(1, 10);

        $customers = Snipcart::$plugin->customers->listCustomers(1, 10, [
            'orderBy' => 'ordersValue',
        ]);

        if ($preFormat) {
            foreach ($orders->items as &$item) {
                // TODO: see if there's a better way to attach dynamic fields
                $item = $item->toArray(
                    ['id', 'invoiceNumber', 'creationDate', 'finalGrandTotal'],
                    ['cpUrl', 'billingAddressName']
                );

                $item['creationDate'] = DateTimeHelper::toDateTime($item['creationDate'])->format('n/j');
                $item['finalGrandTotal'] = FormatHelper::formatCurrency($item['finalGrandTotal']);
            }

            foreach ($customers->items as &$item) {
                // TODO: see if there's a better way to attach dynamic fields
                $item = $item->toArray(
                    ['id', 'billingAddressName', 'statistics'],
                    ['cpUrl']
                );

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

    /**
     * Gets the beginning of the range used for visualizing stats.
     *
     * @throws
     */
    private function getStartDate(): DateTime
    {
        $startDateParam = Craft::$app->getRequest()->getParam('startDate');
        if (! $startDateParam) {
            return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
                ->modify('-1 month');
        }

        if (! is_string($startDateParam)) {
            return (new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())))
                ->modify('-1 month');
        }

        return DateTimeHelper::toDateTime([
            'date' => $startDateParam,
        ]);
    }

    /**
     * Gets the end of the range used for visualizing stats.
     *
     * @throws
     */
    private function getEndDate(): DateTime
    {
        $endDateParam = Craft::$app->getRequest()->getParam('endDate');
        if (! $endDateParam) {
            return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
        }

        if (! is_string($endDateParam)) {
            return new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone()));
        }

        return DateTimeHelper::toDateTime([
            'date' => $endDateParam,
        ]);
    }
}
