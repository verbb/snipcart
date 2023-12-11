<?php
namespace verbb\snipcart\console\controllers;

use verbb\snipcart\Snipcart;
use verbb\snipcart\providers\shipstation\ShipStation;

use craft\helpers\DateTimeHelper;

use yii\console\Controller;
use yii\console\ExitCode;

class VerifyController extends Controller
{
    // Constants
    // =========================================================================

    public const SUCCESS_CHAR = '✓';
    public const FAIL_CHAR = '✗';


    // Public Methods
    // =========================================================================

    public function actionCheckOrders(bool $forceFeed = false, int $limit = 3): int
    {
        $startTime = microtime(true);
        $failedOrders = [];

        $this->stdout('-------------------------------------' . PHP_EOL);
        $this->stdout("Checking last $limit orders..." . PHP_EOL);
        $this->stdout('-------------------------------------' . PHP_EOL);

        $orders = Snipcart::$plugin->getOrders()->getOrders([
            'limit' => $limit,
            'cache' => false,
        ]);

        foreach ($orders as $order) {
            $this->stdout("Snipcart $order->invoiceNumber … ");

            if ($order->hasShippableItems()) {
                $success = true;

                $shipStationOrder = Snipcart::$plugin->getShipments()
                    ->shipStation
                    ->getOrderBySnipcartInvoice($order->invoiceNumber);

                if (!$shipStationOrder instanceof \verbb\snipcart\models\shipstation\Order) {
                    $success = false;
                    $failedOrders[] = $order;
                }

                $this->stdout(sprintf('ShipStation [%s]' . PHP_EOL, $success ? self::SUCCESS_CHAR : self::FAIL_CHAR));
            } else {
                // no shippable items, may not need to be in ShipStation
                $this->stdout(' [skipped]' . PHP_EOL);
            }
        }

        if ($failedOrders !== []) {
            $reFeedResults = $this->reFeedToShipStation($failedOrders, $forceFeed);

            $this->sendAdminNotification($failedOrders, $reFeedResults);
        }

        $this->stdout('-------------------------------------' . PHP_EOL);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime);

        $this->stdout("Finished in $executionTime seconds." . PHP_EOL . PHP_EOL);

        return ExitCode::OK;
    }


    // Private Methods
    // =========================================================================

    private function reFeedToShipStation(array $orders, bool $force): array
    {
        $reFeedResult = [];
        $minuteLimit = Snipcart::$plugin->getSettings()->reFeedAttemptWindow;

        foreach ($orders as $order) {
            // try again, but only briefly or if forced
            if ($force || DateTimeHelper::isWithinLast($order->creationDate, $minuteLimit . ' minutes')) {
                $this->stdout('-------------------------------------' . PHP_EOL);
                $this->stdout("Re-sending order $order->invoiceNumber to ShipStation … ");

                $result = Snipcart::$plugin->getShipments()->shipStation->createOrder($order);
                $succeeded = isset($result->orderId) && empty($result->getErrors());
                $wasTest = $succeeded && $result->orderId === ShipStation::TEST_ORDER_ID;

                // log failure for troubleshooting
                Snipcart::error('ShipStation re-feed failed: {errors}', [
                    'errors' => implode(', ', $result->getErrors()),
                ]);

                $statusString = $succeeded ? self::SUCCESS_CHAR : self::FAIL_CHAR;

                if ($wasTest) {
                    $statusString .= ' (test)';
                }

                $this->stdout($statusString . PHP_EOL);

                $reFeedResult[$order->invoiceNumber] = $succeeded;
            }
        }

        // TODO: re-verify and report result

        return $reFeedResult;
    }

    private function sendAdminNotification(array $snipcartOrders, array $reFeedResults): int
    {
        Snipcart::$plugin->getNotifications()->setEmailTemplate('snipcart/email/recovery');

        $containsFailures = false;

        foreach ($reFeedResults as $invoiceNumber => $success) {
            if ($success === false) {
                $containsFailures = true;
                break;
            }
        }

        Snipcart::$plugin->getNotifications()->setNotificationVars([
            'orders' => $snipcartOrders,
            'reattempt' => $reFeedResults,
            'containsFailures' => $containsFailures,
        ]);

        $toEmails = Snipcart::$plugin->getSettings()->notificationEmails;
        $subject = 'Recovered Snipcart Orders';

        if (!Snipcart::$plugin->getNotifications()->sendEmail($toEmails, $subject)) {
            $this->stderr('Notifications failed.' . PHP_EOL);
        }

        return ExitCode::OK;
    }
}
