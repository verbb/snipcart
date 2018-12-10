<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\events\WebhookEvent;
use workingconcept\snipcart\records\WebhookLog;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\models\SnipcartOrder;
use workingconcept\snipcart\models\Settings;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class WebhooksController extends Controller
{
    // Constants
    // =========================================================================

    const EVENT_BEFORE_PROCESS_COMPLETED_ORDER = 'beforeProcessCompletedOrder';
    
    /**
     * Snipcart's available webhook events
     */
    const WEBHOOK_ORDER_COMPLETED               = 'order.completed';
    const WEBHOOK_SHIPPINGRATES_FETCH           = 'shippingrates.fetch';
    const WEBHOOK_ORDER_STATUS_CHANGED          = 'order.status.changed';
    const WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED = 'order.trackingNumber.changed';
    const WEBHOOK_SUBSCRIPTION_CREATED          = 'subscription.created';
    const WEBHOOK_SUBSCRIPTION_CANCELLED        = 'subscription.cancelled';
    const WEBHOOK_SUBSCRIPTION_PAUSED           = 'subscription.paused';
    const WEBHOOK_SUBSCRIPTION_RESUMED          = 'subscription.resumed';
    const WEBHOOK_SUBSCRIPTION_INVOICE_CREATED  = 'subscription.invoice.created';
    const WEBHOOK_TAXES_CALCULATE               = 'taxes.calculate';
    const WEBHOOK_CUSTOMER_UPDATED              = 'customauth:customer_updated';

    const WEBHOOK_EVENTS = [
        self::WEBHOOK_ORDER_COMPLETED,
        self::WEBHOOK_SHIPPINGRATES_FETCH,
        self::WEBHOOK_ORDER_STATUS_CHANGED,
        self::WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED,
        self::WEBHOOK_SUBSCRIPTION_CREATED,
        self::WEBHOOK_SUBSCRIPTION_CANCELLED,
        self::WEBHOOK_SUBSCRIPTION_PAUSED,
        self::WEBHOOK_SUBSCRIPTION_RESUMED,
        self::WEBHOOK_SUBSCRIPTION_INVOICE_CREATED,
        self::WEBHOOK_TAXES_CALCULATE,
        self::WEBHOOK_CUSTOMER_UPDATED,
    ];


    // Properties
    // =========================================================================

    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected $allowAnonymous  = true; // allow all endpoints in this controller to be used publicly
    protected $validateWebhook = true;
    protected $settings;


    // Public Methods
    // =========================================================================

    /**
     * Validate and handle Snipcart's post according to the declared event type.
     *
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionHandle(): ?Response
    {
        // only take post requests
        $this->requirePostRequest();

        // keep a reference to the plugin settings
        $this->settings = Snipcart::$plugin->getSettings();

        if ($this->validateWebhook && ! $this->validateRequest())
        {
            // reject requests that can't be validated
            return $this->badResponse([
                'reason' => "Couldn't validate the webhook request. Are you sure this is Snipcart calling?"
            ]);
        }

        $postData = json_decode(Craft::$app->request->getRawBody());

        if (is_null($postData) or !isset($postData->eventName))
        {
            // every Snipcart post should have an eventName, so we've got empty data or a bad format
            return $this->badResponse([
                'reason' => 'NULL response body or missing eventName.'
            ]);
        }

        // TODO: respond thoughtfully to test vs. live mode
        $mode = $postData->mode;

        // TODO: consider response for any timestamp that's not very recent
        $createdOn = $postData->createdOn;

        if ($this->settings->logWebhookRequests)
        {
            $this->logWebhookTransaction($postData);
        }

        /**
         * Respond to different types of Snipcart events.
         */

        // TODO: gracefully handle failure to populate models

        switch ($postData->eventName)
        {
            case self::WEBHOOK_ORDER_COMPLETED:
                $order = new SnipcartOrder($postData->content);
                return $this->handleOrderCompletedEvent($order);
                break;
            case self::WEBHOOK_SHIPPINGRATES_FETCH:
                $order = new SnipcartOrder($postData->content);
                return $this->handleShippingRateFetchEvent($order);
                break;
            case self::WEBHOOK_ORDER_STATUS_CHANGED:
            case self::WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED:
            case self::WEBHOOK_SUBSCRIPTION_CREATED:
            case self::WEBHOOK_SUBSCRIPTION_CANCELLED:
            case self::WEBHOOK_SUBSCRIPTION_PAUSED:
            case self::WEBHOOK_SUBSCRIPTION_RESUMED:
            case self::WEBHOOK_SUBSCRIPTION_INVOICE_CREATED:
            case self::WEBHOOK_TAXES_CALCULATE:
            case self::WEBHOOK_CUSTOMER_UPDATED:
                return $this->asJson([ 'success' => true ]);
                break;
            default:
                // unsupported event
                return $this->notSupportedResponse();
                break;
        }
    }


    // Private Methods
    // =========================================================================

    /**
     * Process Snipcart's shipping rate event, which gives us order details and lets us send back shipping options.
     *
     * @param  SnipcartOrder $order
     *
     * @return Response
     */
    private function handleShippingRateFetchEvent(SnipcartOrder $order): Response
    {
        $options = Snipcart::$plugin->snipcart->processShippingRates($order);

        $response = $this->asJson([
            'rates' => $options
        ]);

        if ($this->settings->logCustomRates)
        {
            $shippingQuoteLog         = new ShippingQuoteLog();
            $shippingQuoteLog->siteId = Craft::$app->sites->currentSite->id;
            $shippingQuoteLog->token  = $order->token;
            $shippingQuoteLog->body   = $response->data;
            $shippingQuoteLog->save();    
        }

        return $response;
    }


    /**
     * Output a 400 response with an optional JSON error array.
     *
     * @param  array  $errors Array of errors that explain the 400 response
     *
     * @return Response
     */
    private function badResponse(array $errors): Response
    {
        $response = Craft::$app->getResponse();

        $response->format  = Response::FORMAT_JSON;
        $response->content = json_encode([
            'success' => false,
            'errors' => $errors
        ]);

        $response->setStatusCode(400, 'Bad Request');

        return $response;
    }


    /**
     * Output a 200 response so Snipcart knows we're okay, but not handling the event.
     *
     * @return Response
     */
    private function notSupportedResponse(): Response
    {
        $response = Craft::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }


    /**
     * Handle the completed order.
     *
     * @param  SnipcartOrder $order
     *
     * @return Response
     */
    private function handleOrderCompletedEvent(SnipcartOrder $order): Response
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER))
        {
            $this->trigger(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER, new WebhookEvent([
                'order' => $order
            ]));
        }

        $sendToShipStation = in_array(
            Settings::PROVIDER_SHIPSTATION,
            $this->settings->enabledProviders,
            true
        );

        // send order to ShipStation if we need to
        if ($sendToShipStation)
        {
            $shipStationOrder = Snipcart::$plugin->shipStation->sendSnipcartOrder($order);
        }

        if ( ! $entryUpdateResult = Snipcart::$plugin->snipcart->updateElementsFromOrder($order))
        {
            return $this->asJson(
                [
                    'success' => false,
                    'errors'  => $entryUpdateResult
                ]
            );
        }

        return $this->asJson(
            [
                'success'  => true,
                'order_id' => $shipStationOrder->id ?? '',
            ]
        );
    }


    /**
     * Store webhook details to the database for later scrutiny.
     *
     * @param $postBody
     */
    private function logWebhookTransaction($postBody): void
    {
        $webhookLog = new WebhookLog();

        $webhookLog->siteId = Craft::$app->sites->currentSite->id;
        $webhookLog->type   = $postBody->eventName;
        $webhookLog->body   = $postBody;

        $webhookLog->save();
    }


    /**
     * Ask Snipcart whether the request's token is genuine.
     *
     * @return boolean
     */
    protected function validateRequest(): bool
    {
        $key = 'HTTP_X_SNIPCART_REQUESTTOKEN';
        
        if (Craft::$app->getConfig()->general->devMode)
        {
            // don't validate if we're in dev mode
            return true;
        }

        if (!isset($_SERVER[$key]))
        {
            throw new Exception('Invalid request: no request token');
        }

        if ($token = $_SERVER[$key])
        {
            $response = Snipcart::$plugin->snipcart->validateToken($token);

            if (isset($response->token) && $response->token === $token)
            {
                return true;
            }
        }

        return false;
    }

}
