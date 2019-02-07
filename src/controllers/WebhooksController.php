<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\controllers;

use workingconcept\snipcart\events\CustomerEvent;
use workingconcept\snipcart\events\OrderStatusEvent;
use workingconcept\snipcart\events\OrderTrackingEvent;
use workingconcept\snipcart\events\SubscriptionEvent;
use workingconcept\snipcart\events\TaxesEvent;
use workingconcept\snipcart\models\Subscription;
use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\events\OrderEvent;
use workingconcept\snipcart\records\WebhookLog;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\models\Order;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\base\Exception;

class WebhooksController extends Controller
{
    // Constants
    // =========================================================================

    /**
     * @event OrderEvent Triggered before a completed event is handled by the plugin.
     */
    const EVENT_BEFORE_PROCESS_COMPLETED_ORDER = 'beforeProcessCompletedOrder';

    /**
     * @event OrderStatusEvent
     */
    const EVENT_ON_ORDER_STATUS_CHANGED = 'onOrderStatusChanged';

    /**
     * @event OrderStatusEvent
     */
    const EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED = 'onOrderPaymentStatusChanged';

    /**
     * @event OrderTrackingEvent
     */
    const EVENT_ON_ORDER_TRACKING_CHANGED = 'onOrderTrackingChanged';

    /**
     * @event SubscriptionEvent
     */
    const EVENT_ON_SUBSCRIPTION_CREATED = 'onSubscriptionCreated';

    /**
     * @event SubscriptionEvent
     */
    const EVENT_ON_SUBSCRIPTION_CANCELLED = 'onSubscriptionCancelled';

    /**
     * @event SubscriptionEvent
     */
    const EVENT_ON_SUBSCRIPTION_PAUSED = 'onSubscriptionPaused';

    /**
     * @event SubscriptionEvent
     */
    const EVENT_ON_SUBSCRIPTION_RESUMED = 'onSubscriptionResumed';

    /**
     * @event SubscriptionEvent
     */
    const EVENT_ON_SUBSCRIPTION_INVOICE_CREATED = 'onSubscriptionInvoiceCreated';

    /**
     * @event TaxesEvent
     */
    const EVENT_ON_TAXES_CALCULATE = 'onTaxesCalculate';

    /**
     * @event CustomerEvent
     */
    const EVENT_ON_CUSTOMER_UPDATE = 'onCustomerUpdate';

    /**
     * Snipcart's available webhook events
     */
    const WEBHOOK_ORDER_COMPLETED               = 'order.completed';
    const WEBHOOK_SHIPPINGRATES_FETCH           = 'shippingrates.fetch';
    const WEBHOOK_ORDER_STATUS_CHANGED          = 'order.status.changed';
    const WEBHOOK_ORDER_PAYMENT_STATUS_CHANGED  = 'order.paymentStatus.changed';
    const WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED = 'order.trackingNumber.changed';
    const WEBHOOK_SUBSCRIPTION_CREATED          = 'subscription.created';
    const WEBHOOK_SUBSCRIPTION_CANCELLED        = 'subscription.cancelled';
    const WEBHOOK_SUBSCRIPTION_PAUSED           = 'subscription.paused';
    const WEBHOOK_SUBSCRIPTION_RESUMED          = 'subscription.resumed';
    const WEBHOOK_SUBSCRIPTION_INVOICE_CREATED  = 'subscription.invoice.created';
    const WEBHOOK_TAXES_CALCULATE               = 'taxes.calculate';
    const WEBHOOK_CUSTOMER_UPDATED              = 'customauth:customer_updated';

    const WEBHOOK_EVENT_MAP = [
        self::WEBHOOK_ORDER_COMPLETED               => '_handleOrderCompleted',
        self::WEBHOOK_SHIPPINGRATES_FETCH           => '_handleShippingRatesFetch',
        self::WEBHOOK_ORDER_STATUS_CHANGED          => '_handleOrderStatusChange',
        self::WEBHOOK_ORDER_PAYMENT_STATUS_CHANGED  => '_handleOrderPaymentStatusChange',
        self::WEBHOOK_ORDER_TRACKING_NUMBER_CHANGED => '_handleOrderTrackingNumberChange',
        self::WEBHOOK_SUBSCRIPTION_CREATED          => '_handleSubscriptionCreated',
        self::WEBHOOK_SUBSCRIPTION_CANCELLED        => '_handleSubscriptionCancelled',
        self::WEBHOOK_SUBSCRIPTION_PAUSED           => '_handleSubscriptionPaused',
        self::WEBHOOK_SUBSCRIPTION_RESUMED          => '_handleSubscriptionResumed',
        self::WEBHOOK_SUBSCRIPTION_INVOICE_CREATED  => '_handleOrderSubscriptionInvoiceCreated',
        self::WEBHOOK_TAXES_CALCULATE               => '_handleTaxesCalculate',
        self::WEBHOOK_CUSTOMER_UPDATED              => '_handleCustomerUpdated',
    ];

    const WEBHOOK_MODE_LIVE = 'Live';
    const WEBHOOK_MODE_TEST = 'Test';


    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     * @var bool Disable CSRF for this controller
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     * @var bool allow all endpoints in this controller to be used publicly
     */
    protected $allowAnonymous  = true;

    /**
     * @var bool
     */
    private static $validateWebhook = true;

    /**
     * @var mixed local reference to decoded post data
     */
    private $postData;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /**
         * Return all output as JSON.
         */
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Validate and handle Snipcart's post.
     * @return Response
     * @throws BadRequestHttpException if method isn't post or if something's
     *                                 wrong with the post itself.
     * @throws Exception if the mapped handler method doesn't exist.
     * @todo appropriately handle $postData->mode (test vs. live)
     * @todo appropriately handle weird/old timestamps in $postData->createdOn
     */
    public function actionHandle(): Response
    {
        /**
         * Only take post requests.
         */
        $this->requirePostRequest();

        $this->postData = json_decode(Craft::$app->getRequest()->getRawBody());

        if ($reason = $this->_hasInvalidRequestData())
        {
            return $this->_badRequestResponse([ 'reason' => $reason ]);
        }

        if (Snipcart::$plugin->getSettings()->logWebhookRequests)
        {
            $this->_logWebhookTransaction();
        }

        return $this->_handleWebhookData($this->postData->eventName);
    }


    // Private Methods
    // =========================================================================

    /**
     * Send the webhook's post content to the appropriate handler method.
     * @param string $eventName    Event name from `WEBHOOK_EVENT_MAP`.
     * @return Response
     * @throws Exception if the mapped handler method doesn't exist.
     */
    private function _handleWebhookData($eventName): Response
    {
        if (method_exists($this, self::WEBHOOK_EVENT_MAP[$eventName]))
        {
            /**
             * Call the method defined in `WEBHOOK_EVENT_MAP`,
             * with $postContent as its argument.
             */
            return $this->{self::WEBHOOK_EVENT_MAP[$eventName]}();
        }

        throw new Exception(sprintf(
            'Invalid Snipcart webhook handler specified for `%s`.',
                $eventName)
        );
    }

    /**
     * Handle a completed order.
     * @return Response
     * @throws
     */
    private function _handleOrderCompleted(): Response
    {
        $order = new Order($this->postData->content);

        $responseData = [
            'success' => true,
            'errors' => [],
        ];

        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER))
        {
            $this->trigger(
                self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER,
                new OrderEvent([
                    'order' => $order
                ])
            );
        }

        $providerOrders = Snipcart::$plugin->shipments->handleCompletedOrder($order);

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
            Snipcart::$plugin->orders->sendOrderEmailNotification(
                $order,
                [ 'providerOrders' => $providerOrders->orders ?? null ]
            );
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
     * @return Response
     */
    private function _handleShippingRatesFetch(): Response
    {
        $order    = new Order($this->postData->content);
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
     * @return Response
     */
    private function _handleOrderStatusChange(): Response
    {
        $fromStatus = $this->postData->from;
        $toStatus   = $this->postData->to;
        $order      = new Order($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_STATUS_CHANGED))
        {
            $this->trigger(
                self::EVENT_ON_ORDER_STATUS_CHANGED,
                new OrderStatusEvent([
                    'order'      => $order,
                    'fromStatus' => $fromStatus,
                    'toStatus'   => $toStatus,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleOrderPaymentStatusChange(): Response
    {
        $fromStatus = $this->postData->from;
        $toStatus   = $this->postData->to;
        $order      = new Order($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED))
        {
            $this->trigger(
                self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED,
                new OrderStatusEvent([
                    'order'      => $order,
                    'fromStatus' => $fromStatus,
                    'toStatus'   => $toStatus,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleOrderTrackingNumberChange(): Response
    {
        $trackingNumber = $this->postData->trackingNumber;
        $trackingUrl    = $this->postData->trackingUrl;
        $order          = new Order($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_TRACKING_CHANGED))
        {
            $this->trigger(
                self::EVENT_ON_ORDER_TRACKING_CHANGED,
                new OrderTrackingEvent([
                    'order'          => $order,
                    'trackingNumber' => $trackingNumber,
                    'trackingUrl'    => $trackingUrl,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleSubscriptionCreated(): Response
    {
        $subscription = new Subscription($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CREATED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleSubscriptionCancelled(): Response
    {
        $subscription = new Subscription($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CANCELLED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CANCELLED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleSubscriptionPaused(): Response
    {
        $subscription = new Subscription($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_PAUSED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_PAUSED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleSubscriptionResumed(): Response
    {
        $subscription = new Subscription($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_RESUMED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_RESUMED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleSubscriptionInvoiceCreated(): Response
    {
        $subscription = new Subscription($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * @return Response
     */
    private function _handleTaxesCalculate(): Response
    {
        $order = new Order($this->postData->content);
        $taxes = [];

        if ($this->hasEventHandlers(self::EVENT_ON_TAXES_CALCULATE))
        {
            $this->trigger(
                self::EVENT_ON_TAXES_CALCULATE,
                new TaxesEvent([
                    'order' => $order,
                    'taxes' => [],
                ])
            );

            $taxes = array_merge($taxes, $event->taxes);
        }

        return $this->asJson([
            'taxes' => $taxes
        ]);
    }

    /**
     * @return Response
     */
    private function _handleCustomerUpdated(): Response
    {
        $customer = new Customer($this->postData->content);

        if ($this->hasEventHandlers(self::EVENT_ON_CUSTOMER_UPDATE))
        {
            $this->trigger(
                self::EVENT_ON_CUSTOMER_UPDATE,
                new CustomerEvent([
                    'customer' => $customer,
                ])
            );
        }

        return $this->_notSupportedResponse();
    }

    /**
     * Output a 400 response with an optional JSON error array.
     *
     * @param  array  $errors Array of errors that explain the 400 response
     *
     * @return Response
     */
    private function _badRequestResponse(array $errors): Response
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
     * Output a 200 response so Snipcart knows we're okay but not
     * handling the event.
     * @return Response
     */
    private function _notSupportedResponse(): Response
    {
        return $this->asJson([ 'success' => true ]);
    }

    /**
     * Store webhook details to the database for later scrutiny.
     */
    private function _logWebhookTransaction()
    {
        $webhookLog = new WebhookLog();

        $webhookLog->siteId = Craft::$app->sites->currentSite->id;
        $webhookLog->type   = $this->postData->eventName;
        $webhookLog->body   = $this->postData;

        $webhookLog->save();
    }

    /**
     * Make sure we don't have invalid data, or return a reason if we do.
     * @return bool|string
     * @throws BadRequestHttpException if there's a problem with the token.
     */
    private function _hasInvalidRequestData()
    {
        /**
         * Reject requests that can't be validated.
         */
        if (self::$validateWebhook && ! $this->_requestIsValid())
        {
            return 'Could not validate webhook request. Are you Snipcart?';
        }

        /**
         * Every Snipcart post should have an eventName, so we've got
         * missing data or a bad format.
         */
        if ($this->postData === null || !isset($this->postData->eventName))
        {
            return 'NULL request body or missing eventName.';
        }

        /**
         * Every Snipcart post should have a content property.
         */
        if (!isset($this->postData->content))
        {
            return 'Request missing content.';
        }

        /**
         * We've received an invalid `eventName`.
         */
        if (! array_key_exists(
            $this->postData->eventName,
            self::WEBHOOK_EVENT_MAP
        ))
        {
            return 'Invalid event.';
        }

        // We *don't* have invalid request data!
        return false;
    }

    /**
     * Use the supplied token to be sure the request is genuine.
     *
     * @return boolean
     * @throws BadRequestHttpException  Thrown if the servery key is missing from the request.
     * @throws \Exception               Thrown if there's a problem with the actual API call.
     */
    private function _requestIsValid(): bool
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
