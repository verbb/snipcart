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
use workingconcept\snipcart\models\Order;

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

    /**
     * @inheritdoc
     * @var bool Disable CSRF for this controller
     */
    public $enableCsrfValidation = false; //

    /**
     * @inheritdoc
     * @var bool allow all endpoints in this controller to be used publicly
     */
    protected $allowAnonymous  = true;

    /**
     * @var bool
     */
    private static $validateWebhook = true;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        /**
         * Return all output as JSON.
         */
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
        /**
         * Only take post requests.
         */
        $this->requirePostRequest();

        $postData = json_decode(Craft::$app->request->getRawBody());

        if ($reason = $this->hasInvalidRequestData($postData))
        {
            return $this->badRequestResponse([ 'reason' => $reason ]);
        }

        // TODO: respond thoughtfully to test vs. live mode
        // $mode = $postData->mode;

        // TODO: consider response for any timestamp that's not very recent
        // $createdOn = $postData->createdOn;

        if (Snipcart::$plugin->getSettings()->logWebhookRequests)
        {
            $this->logWebhookTransaction($postData);
        }

        /**
         * Respond to different types of Snipcart events.
         */
        switch ($postData->eventName)
        {
            case self::WEBHOOK_ORDER_COMPLETED:
                $order = new Order($postData->content);
                return $this->handleOrderCompleted($order);
            case self::WEBHOOK_SHIPPINGRATES_FETCH:
                $order = new Order($postData->content);
                return $this->handleShippingRatesFetch($order);
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
     * Handle the completed order.
     *
     * @param  Order $order
     *
     * @return Response
     * @throws
     */
    private function handleOrderCompleted(Order $order): Response
    {
        $responseData = [
            'success' => true,
            'errors' => [],
        ];

        $extraEmailVars = [];

        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER)) {
            $this->trigger(
                self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER,
                new WebhookEvent([
                    'order' => $order
                ])
            );
        }

        $providerOrders = Snipcart::$plugin->shipments->handleCompletedOrder($order);

        if (! empty($providerOrders->orders))
        {
            $extraEmailVars['providerOrders'] = $providerOrders->orders;
        }

        if (! empty($providerOrders->errors))
        {
            $responseData['success']  = false;
            $responseData['errors'][] = $providerOrders->errors;
        }

        if ( ! $entryUpdateResult = Snipcart::$plugin->orders->updateElementsFromOrder($order))
        {
            $responseData['success']  = false;
            $responseData['errors'][] = [
                'elements' => $entryUpdateResult
            ];
        }

        if (isset($providerOrders->orders['shipStation']))
        {
            /**
             * Successful orders have a populated ->orderId, but with tests
             * we set ->orderId to 99999999.
             */
            $responseData['shipstation_order_id'] = $providerOrders->orders['shipStation']->orderId ?? '';
        }

        if (isset(Snipcart::$plugin->getSettings()->notificationEmails))
        {
            Snipcart::$plugin->orders->sendOrderEmailNotification($order, $extraEmailVars);
        }

        if (count($responseData['errors']) === 0)
        {
            unset($responseData['errors']);
        }

        return $this->asJson($responseData);
    }

    /**
     * Process Snipcart's shipping rate event, which gives us order details
     * and lets us send back shipping options.
     *
     * @param Order $order
     * @return Response
     * @todo be careful to skip if the order isn't shippable
     */
    private function handleShippingRatesFetch(Order $order): Response
    {
        $rates    = Snipcart::$plugin->shipments->collectRatesForOrder($order);
        $response = $this->asJson($rates);

        if (Snipcart::$plugin->getSettings()->logCustomRates)
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
     * Output a 200 response so Snipcart knows we're okay, but not
     * handling the event.
     *
     * @return Response
     */
    private function notSupportedResponse(): Response
    {
        return Craft::$app->getResponse()->setStatusCode(200);
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
     * Make sure we don't have invalid data, or return a reason if we do.
     *
     * @param $postData
     * @return bool|string
     * @throws BadRequestHttpException
     */
    private function hasInvalidRequestData($postData)
    {
        /**
         * Reject requests that can't be validated.
         */
        if (self::$validateWebhook && ! $this->validateRequest())
        {
            return 'Could not validate webhook request. Are you Snipcart?';
        }

        /**
         * Every Snipcart post should have an eventName, so we've got
         * missing data or a bad format.
         */
        if ($postData === null || !isset($postData->eventName))
        {
            return 'NULL request body or missing eventName.';
        }

        /**
         * Every Snipcart post should have a content property.
         */
        if (!isset($postData->content))
        {
            return 'Request missing content.';
        }

        /**
         * We've received an invalid `eventName`.
         */
        if (! in_array($postData->eventName, self::WEBHOOK_EVENTS, true))
        {
            return 'Invalid event.';
        }

        // We *don't* have invalid request data!
        return false;
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
        $key     = 'x-snipcart-requesttoken';
        $headers = Craft::$app->getRequest()->getHeaders();

        if ( ! $headers->has($key) && Craft::$app->getConfig()->general->devMode)
        {
            // don't validate if we're in dev mode
            return true;
        }

        if ( ! $headers->has($key))
        {
            throw new BadRequestHttpException('Invalid request: no request token');
        }

        $token = $headers->get($key);

        if ( ! is_string($token))
        {
            throw new BadRequestHttpException('Invalid request: token can only be a string');
        }

        return Snipcart::$plugin->api->tokenIsValid($token);
    }

}
