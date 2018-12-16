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
        $startTime = microtime(true);

        $limit    = 3;
        $failures = [];

        $this->stdout('-------------------------------------' . PHP_EOL);
        $this->stdout("Checking last $limit orders..." . PHP_EOL);
        $this->stdout('-------------------------------------' . PHP_EOL);

        $snipcartOrders    = $this->getSnipcartOrders($limit);
        $shipStationOrders = $this->getShipStationOrders($limit * 2);

        foreach ($snipcartOrders as $snipcartOrder) 
        {
            $success = false;

            foreach ($shipStationOrders as $shipStationOrder)
            {
                if ($shipStationOrder->orderNumber === $snipcartOrder->invoiceNumber)
                {
                    $success = true;
                    continue;
                }
            }

            $shipStationStatusString = $success ? '✓' : '✗';

            $this->stdout("Snipcart $snipcartOrder->invoiceNumber → ShipStation [$shipStationStatusString]" . PHP_EOL);
            
            if ( ! $success)
            {
                $failures[] = $snipcartOrder;
            }
        }

        if (count($failures) > 0)
        {
            $this->reFeedToShipStation($failures);
            // TODO: reconfirm updated orders
            $this->sendAdminNotification($failures);
        }

        $this->stdout('-------------------------------------' . PHP_EOL);

        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);

        $this->stdout("Executed in ${executionTime} seconds." . PHP_EOL);

        return ExitCode::OK;
    }

    /**
     * Try re-feeding missing orders into ShipStation.
     *
     * @param \workingconcept\snipcart\models\SnipcartOrder[] $snipcartOrders
     *
     * @return int
     * @throws
     */
    private function reFeedToShipStation($snipcartOrders): int
    {
        foreach ($snipcartOrders as $snipcartOrder)
        {
            // try again, but not forever
            if (DateTimeHelper::isWithinLast($snipcartOrder->creationDate, '30 minutes'))
            {
                $this->stdout("Attempting to re-send order to ShipStation." . PHP_EOL);
                Snipcart::$plugin->shipStation->sendSnipcartOrder($snipcartOrder);
            }
        }

        return ExitCode::OK;
    }

    /**
     * Let somebody know that one or more orders didn't make it to ShipStation.
     *
     * @param \workingconcept\snipcart\models\SnipcartOrder[] $snipcartOrders
     * @return int
     * @throws
     */
    private function sendAdminNotification($snipcartOrders): int
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
                'orders' => $snipcartOrders,
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


    // Private Methods
    // =========================================================================

    /**
     * Fetch recent Snipcart orders.
     * TODO: refactor to rely on service for actual SnipcartOrder models
     *
     * @param integer $limit
     *
     * @return array
     */
    private function getSnipcartOrders($limit = 5): array
    {
        $snipcart       = new \workingconcept\snipcart\services\SnipcartService;
        $snipcartClient = $snipcart->getClient();
        $response       = $snipcartClient->get('orders?limit=' . $limit);

        return json_decode($response->getBody())->items;
    }

    /**
     * Fetch recent ShipStation orders.
     *
     * @param integer $limit
     *
     * @return array
     */
    private function getShipStationOrders($limit = 5): array
    {
        return Snipcart::$plugin->shipStation->listOrders($limit);
    }
}