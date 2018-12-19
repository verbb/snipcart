<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
namespace workingconcept\snipcart\console\controllers;

use craft\helpers\DateTimeHelper;
use workingconcept\snipcart\Snipcart;
use Craft;
use yii\console\Controller;
use craft\mail\Message;
use yii\console\ExitCode;

class VerifyController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Verify that the most recently-added orders exist in both Snipcart and ShipStation
     * and that none quietly failed to make it to ShipStation.
     * 
     * If there was a failure, send email notifications.
     *
     * @return int
     * @throws
     */
    public function actionCheckOrders(): int
    {
        $startTime    = microtime(true);
        $limit        = 3;
        $failedOrders = [];
        
        $this->stdout('-------------------------------------' . PHP_EOL);
        $this->stdout("Checking last $limit orders..." . PHP_EOL);
        $this->stdout('-------------------------------------' . PHP_EOL);

        $orders = Snipcart::$plugin->orders->getOrders([
            'limit' => $limit, 
            'cache' => false 
        ]);

        foreach ($orders as $order)
        {
            $this->stdout("Snipcart $order->invoiceNumber ... ");
            $shipStationStatusString = '✓';

            if ( ! Snipcart::$plugin->shipments->
                        shipStation->
                        getOrderBySnipcartInvoice($order->invoiceNumber)
            )
            {
                $shipStationStatusString = '✗';
                $failedOrders[] = $order;
            }

            $this->stdout("ShipStation [$shipStationStatusString]" . PHP_EOL);
        }

        if (count($failedOrders) > 0)
        {
            $reFeedResults = $this->_reFeedToShipStation($failedOrders);
            $this->_sendAdminNotification($failedOrders, $reFeedResults);
        }

        $this->stdout('-------------------------------------' . PHP_EOL);

        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);

        $this->stdout("Finished in ${executionTime} seconds." . PHP_EOL . PHP_EOL);

        return ExitCode::OK;
    }


    // Private Methods
    // =========================================================================

    /**
     * Try re-feeding missing orders into ShipStation.
     *
     * @param \workingconcept\snipcart\models\Order[] $orders
     *
     * @return array If attempts were made to try re-sending the orders to ShipStation,
     *               they'll be in this array where the key is the invoice number
     *               and the value is true if successful.
     * @throws
     */
    private function _reFeedToShipStation($orders): array
    {
        $reFeedResult = [];

        foreach ($orders as $order)
        {
            // try again, but only briefly
            if (DateTimeHelper::isWithinLast($order->creationDate, '10 minutes'))
            {
                $this->stdout('-------------------------------------' . PHP_EOL);
                $this->stdout('Attempting to re-send order ' . $order->invoiceNumber . ' to ShipStation ... ');
                $result = Snipcart::$plugin->shipments->shipStation->createOrder($order);

                $succeeded = isset($result->orderId) && empty($result->getErrors());

                $statusString = $succeeded ? '✓' : '✗';
                $this->stdout($statusString . PHP_EOL);

                $reFeedResult[$order->invoiceNumber] = $succeeded;
            }
        }

        return $reFeedResult;
    }

    /**
     * Let somebody know that one or more orders didn't make it to ShipStation.
     *
     * @param \workingconcept\snipcart\models\Order[] $snipcartOrders
     * @param array $reFeedResults
     *
     * @return int
     * @throws
     */
    private function _sendAdminNotification($snipcartOrders, $reFeedResults): int
    {
        // TODO: consolidate with SnipcartService

        // temporarily change template modes so we can render the plugin's template
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        $message = new Message();

        foreach (Snipcart::$plugin->getSettings()->notificationEmails as $address)
        {
            $settings = Craft::$app->systemSettings->getSettings('email');

            $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
            $message->setTo($address);
            $message->setSubject('Recovered Snipcart Orders');
            $message->setHtmlBody($view->renderPageTemplate('snipcart/email/recovery', [
                'orders'    => $snipcartOrders,
                'reattempt' => $reFeedResults,
            ]));

            if ( ! Craft::$app->mailer->send($message))
            {
                $this->stderr("Notification failed to send to {$address}!". PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $view->setTemplateMode($oldTemplateMode);

        return ExitCode::OK;
    }

}