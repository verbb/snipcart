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
use yii\web\BadRequestHttpException;

class WebhooksController extends Controller
{
    // Constants
    // =========================================================================

    /**
     * @event WebhookEvent Triggered before a completed event is handled by the plugin.
     */
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

    const WEBHOOK_MODE_LIVE = 'Live';
    const WEBHOOK_MODE_TEST = 'Test';

    // Properties
    // =========================================================================

    public $enableCsrfValidation = false; // disable CSRF for this controller

    protected $allowAnonymous  = true; // allow all endpoints in this controller to be used publicly
    protected $validateWebhook = true;
    protected $settings;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        // return all output as JSON
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Validate and handle Snipcart's post according to the declared event type.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionHandle(): Response
    {
        // only take post requests
        $this->requirePostRequest();

        // keep a reference to the plugin settings
        $this->settings = Snipcart::$plugin->getSettings();

        if ($this->validateWebhook && ! $this->validateRequest())
        {
            // reject requests that can't be validated
            return $this->badRequestResponse([
                'reason' => "Couldn't validate the webhook request. Are you sure this is Snipcart calling?"
            ]);
        }

        $postData = json_decode(Craft::$app->request->getRawBody());

        if ($postData === null || !isset($postData->eventName))
        {
            // every Snipcart post should have an eventName, so we've got empty data or a bad format
            return $this->badRequestResponse([
                'reason' => 'NULL request body or missing eventName.'
            ]);
        }

        if (!isset($postData->content))
        {
            // every Snipcart post should have content
            return $this->badRequestResponse([
                'reason' => 'Request missing content.'
            ]);
        }

        if (! in_array($postData->eventName, self::WEBHOOK_EVENTS, true))
        {
            // only handle proper `eventName`s
            return $this->badRequestResponse([
                'reason' => 'Invalid event.'
            ]);
        }

        // TODO: respond thoughtfully to test vs. live mode
        // $mode = $postData->mode;

        // TODO: consider response for any timestamp that's not very recent
        // $createdOn = $postData->createdOn;

        if ($this->settings->logWebhookRequests)
        {
            $this->logWebhookTransaction($postData);
        }

        /**
         * Respond to different types of Snipcart events.
         */
        switch ($postData->eventName)
        {
            case self::WEBHOOK_ORDER_COMPLETED:
                $order = new SnipcartOrder($postData->content);
                return $this->handleOrderCompletedEvent($order);
            case self::WEBHOOK_SHIPPINGRATES_FETCH:
                $order = new SnipcartOrder($postData->content);
                return $this->handleShippingRateFetchEvent($order);
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
            default:
                // unsupported event
                return $this->notSupportedResponse();
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
        $rateInfo = Snipcart::$plugin->snipcart->getShippingRatesForOrder($order);

        $response = $this->asJson($rateInfo);

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
    private function badRequestResponse(array $errors): Response
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
        $responseData = [
            'success' => true,
            'errors' => [],
        ];

        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER))
        {
            $this->trigger(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER, new WebhookEvent([
                'order' => $order
            ]));
        }

        // is ShipStation an enabled provider?
        $sendToShipStation = in_array(
            Settings::PROVIDER_SHIPSTATION,
            $this->settings->enabledProviders,
            false
        );

        // send order to ShipStation if we need to
        if ($sendToShipStation)
        {
            $shipStationOrder = Snipcart::$plugin->shipStation->sendSnipcartOrder($order);
        }

        if ( ! $entryUpdateResult = Snipcart::$plugin->snipcart->updateElementsFromOrder($order))
        {
            $responseData['success']  = false;
            $responseData['errors'][] = [
                'elements' => $entryUpdateResult
            ];
        }

        if (isset($shipStationOrder))
        {
            // successful orders have a populated ->orderId, and with tests ->orderId = 99999999
            $responseData['shipstation_order_id'] = $shipStationOrder->orderId ?? '';

            if (count($shipStationOrder->getErrors()) > 0)
            {
                $responseData['shipstation_errors'] = $shipStationOrder->getErrors();

                $responseData['success']  = false;
                $responseData['errors'][] = [
                    'shipstation' => $entryUpdateResult
                ];
            }
        }

        if (count($responseData['errors']) === 0)
        {
            unset($responseData['errors']);
        }

        return $this->asJson($responseData);
    }

    /**
     * Store webhook details to the database for later scrutiny.
     *
     * @param $postBody
     */
    private function logWebhookTransaction($postBody)
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
     * @throws BadRequestHttpException  Thrown if the servery key is missing from the request.
     * @throws \Exception               Thrown if there's a problem with the actual API call.
     */
    protected function validateRequest(): bool
    {
        $key = 'HTTP_X_SNIPCART_REQUESTTOKEN';
        
        if (Craft::$app->getConfig()->general->devMode)
        {
            // don't validate if we're in dev mode
            return true;
        }

        if ( ! isset($_SERVER[$key]))
        {
            throw new BadRequestHttpException('Invalid request: no request token');
        }

        if ($token = $_SERVER[$key])
        {
            return Snipcart::$plugin->api->tokenIsValid($token);
        }

        return false;
    }

}
