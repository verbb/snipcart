<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */
namespace workingconcept\snipcart\console\controllers;

use Craft;
use yii\console\Controller;
use craft\mail\Message;

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
     * @return void
     */
    public function actionCheckOrders()
    {
        $startTime    = microtime(true);
        $updatedCount = 0;

        $limit    = 3;
        $failures = [];

        echo "-------------------------------------\n";
        echo "Checking last $limit orders...\n";
        echo "-------------------------------------\n";

        $snipcartOrders    = $this->getSnipcartOrders($limit);
        $shipStationOrders = $this->getShipStationOrders($limit * 2);

        foreach ($snipcartOrders as $snipcartOrder) 
        {
            $success = false;

            foreach ($shipStationOrders as $shipStationOrder)
            {
                if ($shipStationOrder->orderNumber == $snipcartOrder->invoiceNumber)
                {
                    $success = true;
                    continue;
                }
            }

            $shipStationStatusString = $success ? '✓' : '✗';

            echo "Snipcart $snipcartOrder->invoiceNumber → ShipStation [$shipStationStatusString]\n";
            
            if ( ! $success)
            {
                $failures[] = $snipcartOrder;
            }
        }

        if (count($failures) > 0)
        {
            $this->reFeedToShipStation($failures);
            // TODO: reconfirm updated orders
            // TODO: send admin email notification
            $this->sendAdminNotification($failures);
        }

        echo "-------------------------------------\n";

        $endTime       = microtime(true);
        $executionTime = ($endTime - $startTime);

        echo "Executed in ${executionTime} seconds.\n";

        return true;
    }


    /**
     * Try re-feeding missing orders into ShipStation.
     *
     * @param [type] $snipcartOrders
     * @return void
     */
    private function reFeedToShipStation($snipcartOrders)
    {
        // TODO: feed each order to ShipStation
    }


    /**
     * Let somebody know that one or more orders didn't make it to ShipStation.
     *
     * @param [type] $snipcartOrders
     * @return void
     */
    private function sendAdminNotification($snipcartOrders)
    {
        // TODO: consolidate with SnipcartService

        if (is_array(Snipcart::$plugin->settings->notificationEmails))
        {
            $emailAddresses = Snipcart::$plugin->settings->notificationEmails;
        }
        elseif (is_string(Snipcart::$plugin->settings->notificationEmails))
        {
            $emailAddresses = explode(',', Snipcart::$plugin->settings->notificationEmails);
        }
        else
        {
            throw new \Exception('Email notification setting must be string or array.');
        }

        // temporarily change template modes so we can render the plugin's template
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        $siteName = Craft::$app->getConfig()->siteName;
        
        $message = new Message();

        foreach ($emailAddresses as $address)
        {
            $settings = Craft::$app->systemSettings->getSettings('email');

            $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
            $message->setTo($address);
            $message->setSubject('Recovered ' . $siteName . ' Orders');
            $message->setHtmlBody($view->renderPageTemplate('snipcart/email/recovery', [
                'orders' => $snipcartOrders,
            ]));

            try
            {
                Craft::$app->mailer->send($message);
            }
            catch (ErrorException $e)
            {
                $errors[] = $e;
            }
        }

        $view->setTemplateMode($oldTemplateMode);
    }


    /**
     * Fetch recent Snipcart orders.
     *
     * @param integer $limit
     * @return void
     */
    private function getSnipcartOrders($limit = 5)
    {
        $snipcart       = new \workingconcept\snipcart\services\SnipcartService;
        $snipcartClient = $snipcart->getClient($limit);
        $response       = $snipcartClient->get('orders?limit=' . $limit);

        $responseData = json_decode($response->getBody(true));

        return $responseData->items;
    }

    
    /**
     * Fetch recent ShipStation orders.
     *
     * @param integer $limit
     * @return void
     */
    private function getShipStationOrders($limit = 5)
    {
        $shipStation       = new \workingconcept\snipcart\services\ShipStationService;
        $shipStationOrders = $shipStation->listOrders($limit);

        return $shipStationOrders;
    }
}