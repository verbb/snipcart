<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
namespace workingconcept\snipcart\console\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\models\snipcart\Order;
use workingconcept\snipcart\providers\shipstation\ShipStation;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Console utility for making sure Snipcart orders made it to ShipStation,
 * and attempting to re-feed any order that got lost in the tubes.
 *
 * @package workingconcept\snipcart\console\controllers
 */
class VerifyController extends Controller
{
    const SUCCESS_CHAR = '✓';
    const FAIL_CHAR = '✗';

    /**
     * Checks that most recent orders exist in Snipcart and ShipStation.
     * Sends notifications and tries again if any are missing.
     *
     * @param bool $forceFeed  forcefully re-send missing orders
     * @param int  $limit      number of recent orders to check
     *
     * @return int
     * @throws
     */
    public function actionCheckOrders($forceFeed = false, $limit = 3): int
    {
        $startTime    = microtime(true);
        $failedOrders = [];
        
        $this->stdout('-------------------------------------' . PHP_EOL);
        $this->stdout("Checking last $limit orders..." . PHP_EOL);
        $this->stdout('-------------------------------------' . PHP_EOL);

        $orders = Snipcart::$plugin->orders->getOrders([
            'limit' => $limit,
            'cache' => false
        ]);

        foreach ($orders as $order) {
            $this->stdout("Snipcart $order->invoiceNumber … ");

            if ($order->hasShippableItems()) {
                $success = true;
                $shipStationOrder = Snipcart::$plugin->shipments
                    ->shipStation
                    ->getOrderBySnipcartInvoice($order->invoiceNumber);

                if (! $shipStationOrder) {
                    $success = false;
                    $failedOrders[] = $order;
                }

                $this->stdout(
                    sprintf(
                        'ShipStation [%s]' . PHP_EOL,
                        $success ? self::SUCCESS_CHAR : self::FAIL_CHAR
                    )
                );
            } else {
                // no shippable items, may not need to be in ShipStation
                $this->stdout(' [skipped]' . PHP_EOL);
            }
        }

        if (count($failedOrders) > 0) {
            $reFeedResults = $this->reFeedToShipStation(
                $failedOrders,
                $forceFeed
            );

            $this->sendAdminNotification($failedOrders, $reFeedResults);
        }

        $this->stdout('-------------------------------------' . PHP_EOL);

        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);

        $this->stdout("Finished in ${executionTime} seconds." . PHP_EOL . PHP_EOL);

        return ExitCode::OK;
    }

    /**
     * Tries re-feeding missing orders into ShipStation.
     *
     * @param Order[] $orders
     * @param bool    $force
     *
     * @return array  If attempts were made to re-send the orders to
     *                ShipStation, they'll be in this array where the key is the
     *                invoice number and the value is true if successful.
     * @throws
     */
    private function reFeedToShipStation($orders, $force): array
    {
        $reFeedResult = [];
        $minuteLimit = Snipcart::$plugin->getSettings()->reFeedAttemptWindow;

        foreach ($orders as $order) {
            // try again, but only briefly or if forced
            if ($force || DateTimeHelper::isWithinLast(
                $order->creationDate,
                $minuteLimit . ' minutes'
            )) {
                $this->stdout('-------------------------------------' . PHP_EOL);
                $this->stdout(sprintf(
                    'Re-sending order %s to ShipStation … ',
                    $order->invoiceNumber
                ));

                $result = Snipcart::$plugin->shipments->shipStation->createOrder($order);
                $succeeded = isset($result->orderId) && empty($result->getErrors());
                $wasTest = $succeeded && $result->orderId === ShipStation::TEST_ORDER_ID;

                // log failure for troubleshooting
                Craft::error(sprintf(
                    'ShipStation re-feed failed: %s',
                    implode(', ', $result->getErrors())
                ), 'snipcart');

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

    /**
     * Lets somebody know that one or more orders didn’t make it to ShipStation.
     *
     * @param Order[] $snipcartOrders
     * @param array   $reFeedResults
     *
     * @return int
     * @throws
     */
    private function sendAdminNotification($snipcartOrders, $reFeedResults): int
    {
        Snipcart::$plugin->notifications->setEmailTemplate(
            'snipcart/email/recovery'
        );

        $containsFailures = false;

        foreach ($reFeedResults as $invoiceNumber => $success) {
            if ($success === false) {
                $containsFailures = true;
                break;
            }
        }

        Snipcart::$plugin->notifications->setNotificationVars([
            'orders' => $snipcartOrders,
            'reattempt' => $reFeedResults,
            'containsFailures' => $containsFailures
        ]);

        $toEmails = Snipcart::$plugin->getSettings()->notificationEmails;
        $subject = 'Recovered Snipcart Orders';

        if (! Snipcart::$plugin->notifications->sendEmail($toEmails, $subject)) {
            $this->stderr('Notifications failed.'. PHP_EOL);
        }

        return ExitCode::OK;
    }

}