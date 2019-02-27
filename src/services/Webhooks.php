<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\events\CustomerEvent;
use workingconcept\snipcart\events\OrderStatusEvent;
use workingconcept\snipcart\events\OrderTrackingEvent;
use workingconcept\snipcart\events\SubscriptionEvent;
use workingconcept\snipcart\events\TaxesEvent;
use workingconcept\snipcart\models\Subscription;
use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\Snipcart;
use workingconcept\snipcart\events\OrderEvent;
use workingconcept\snipcart\records\ShippingQuoteLog;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\ShippingRate;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\records\WebhookLog;
use Craft;

/**
 * This class handles valid data that's posted to Snipcart's webhook endpoint.
 *
 * @package workingconcept\snipcart\services
 */
class Webhooks extends \craft\base\Component
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
     * @var string Indicates that the webhook payload is live
     */
    const WEBHOOK_MODE_LIVE = 'Live';

    /**
     * @var string Indicates that the webhook payload is for testing
     */
    const WEBHOOK_MODE_TEST = 'Test';


    // Private Properties
    // =========================================================================

    /**
     * @var mixed local reference to decoded post data
     */
    private $_webhookData;

    /**
     * @var string Should be either WEBHOOK_MODE_LIVE or WEBHOOK_MODE_TEST
     */
    private $_currentMode;


    // Public Methods
    // =========================================================================

    /**
     * Sets the payload data and derived mode to be utilized within the service
     * and quietly logs the request before processing if logging is enabled.
     *
     * @param $payload
     */
    public function setData($payload)
    {
        /**
         * Track whether we're in live or test mode. We know ->mode exists
         * because the payload is validated before it gets here.
         */
        $this->setMode($payload->mode);

        /**
         * Establish our local reference to the payload.
         */
        $this->_webhookData = $payload;

        /**
         * Log for troubleshooting if that option is enabled.
         */
        if (Snipcart::$plugin->getSettings()->logWebhookRequests)
        {
            $this->_logWebhookTransaction();
        }
    }

    /**
     * Returns payload data in whatever format it was received.
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->_webhookData;
    }

    /**
     * Sets the mode of the current request, which is either `Live` or `Test`.
     *
     * @param string $mode 'Live' or 'Test'
     *
     * @return mixed
     */
    public function setMode($mode)
    {
        return $this->_currentMode = $mode;
    }

    /**
     * Gets the mode of the current request, which is either `Live` or `Test`.
     * @return string
     */
    public function getMode(): string
    {
        return $this->_currentMode;
    }

    /**
     * Handles a completed order.
     *
     * @return array Array with `success` (bool) and `errors` (string[])
     * @throws
     */
    public function handleOrderCompleted(): array
    {
        $order = $this->_getCleanOrder();

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

        if (Snipcart::$plugin->getSettings()->sendOrderNotificationEmail)
        {
            Snipcart::$plugin->orders->sendOrderEmailNotification(
                $order,
                [ 'providerOrders' => $providerOrders->orders ?? null ],
                Orders::NOTIFICATION_TYPE_ADMIN
            );
        }

        if (Snipcart::$plugin->getSettings()->sendCustomerOrderNotificationEmail)
        {
            Snipcart::$plugin->orders->sendOrderEmailNotification(
                $order,
                [ 'providerOrders' => $providerOrders->orders ?? null ],
                Orders::NOTIFICATION_TYPE_CUSTOMER
            );
        }

        if (count($responseData['errors']) === 0)
        {
            unset($responseData['errors']);
        }

        return $responseData;
    }

    /**
     * Processes Snipcart's shipping rate event, which gives us order details
     * and lets us send back shipping options.
     *
     * @return ShippingRate[]
     */
    public function handleShippingRatesFetch(): array
    {
        $order = $this->_getCleanOrder();
        $rates = Snipcart::$plugin->shipments->collectRatesForOrder($order);

        if (Snipcart::$plugin->getSettings()->logCustomRates)
        {
            $shippingQuoteLog         = new ShippingQuoteLog();
            $shippingQuoteLog->siteId = Craft::$app->sites->currentSite->id;
            $shippingQuoteLog->token  = $order->token;
            $shippingQuoteLog->body   = $rates;
            $shippingQuoteLog->save();
        }

        return $rates;
    }

    /**
     * Handles an order status change.
     *
     * @return array
     */
    public function handleOrderStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus   = $this->getData()->to;
        $order      = $this->_getCleanOrder();

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

        return $this->_nonResponse();
    }

    /**
     * Handles an order payment status change.
     *
     * @return array
     */
    public function handleOrderPaymentStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus   = $this->getData()->to;
        $order      = $this->_getCleanOrder();

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

        return $this->_nonResponse();
    }

    /**
     * Handles an order tracking number change.
     *
     * @return array
     */
    public function handleOrderTrackingNumberChange(): array
    {
        $trackingNumber = $this->getData()->trackingNumber;
        $trackingUrl    = $this->getData()->trackingUrl;
        $order          = $this->_getCleanOrder();

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

        return $this->_nonResponse();
    }

    /**
     * Handles a created subscription.
     *
     * @return array
     */
    public function handleSubscriptionCreated(): array
    {
        $subscription = $this->_getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CREATED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_nonResponse();
    }

    /**
     * Handles a cancelled subscription.
     *
     * @return array
     */
    public function handleSubscriptionCancelled(): array
    {
        $subscription = $this->_getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CANCELLED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CANCELLED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_nonResponse();
    }

    /**
     * Handles a paused subscription.
     *
     * @return array
     */
    public function handleSubscriptionPaused(): array
    {
        $subscription = $this->_getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_PAUSED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_PAUSED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_nonResponse();
    }

    /**
     * Handles a resumed subscription.
     *
     * @return array
     */
    public function handleSubscriptionResumed(): array
    {
        $subscription = $this->_getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_RESUMED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_RESUMED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_nonResponse();
    }

    /**
     * Handles a new subscription invoice.
     *
     * @return array
     */
    public function handleSubscriptionInvoiceCreated(): array
    {
        $subscription = $this->_getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED))
        {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->_nonResponse();
    }

    /**
     * Handles a tax calculation request.
     *
     * @return array `taxes` => Tax[]
     */
    public function handleTaxesCalculate(): array
    {
        $order = $this->_getCleanOrder();
        $taxes = [];

        if ($this->hasEventHandlers(self::EVENT_ON_TAXES_CALCULATE))
        {
            $event = new TaxesEvent([
                'order' => $order,
                'taxes' => [],
            ]);

            $this->trigger(self::EVENT_ON_TAXES_CALCULATE, $event);
            $taxes = array_merge($taxes, $event->taxes);
        }

        return [ 'taxes' => $taxes ];
    }

    /**
     * Handles updated customer details.
     *
     * @return array
     */
    public function handleCustomerUpdated(): array
    {
        $customer = $this->_getCleanCustomer();

        if ($this->hasEventHandlers(self::EVENT_ON_CUSTOMER_UPDATE))
        {
            $this->trigger(
                self::EVENT_ON_CUSTOMER_UPDATE,
                new CustomerEvent([
                    'customer' => $customer,
                ])
            );
        }

        return $this->_nonResponse();
    }


    // Private Methods
    // =========================================================================

    /**
     * Returns posted payload as an Order without letting unexpected root-level
     * attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don't want errors in production.
     *
     * @return Order
     */
    private function _getCleanOrder(): Order
    {
        $payload = ModelHelper::stripUnknownProperties(
            $this->getData()->content,
            Order::class
        );

        return new Order($payload);
    }

    /**
     * Returns posted payload as an Subscription without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don't want errors in production.
     *
     * @return Subscription
     */
    private function _getCleanSubscription(): Subscription
    {
        $payload = ModelHelper::stripUnknownProperties(
            $this->getData()->content,
            Subscription::class
        );

        return new Subscription($payload);
    }

    /**
     * Returns posted payload as an Customer without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don't want errors in production.
     *
     * @return Customer
     */
    private function _getCleanCustomer(): Customer
    {
        $payload = ModelHelper::stripUnknownProperties(
            $this->getData()->content,
            Customer::class
        );

        return new Customer($payload);
    }

    /**
     * Stores webhook details to the database for later scrutiny.
     */
    private function _logWebhookTransaction()
    {
        $webhookLog = new WebhookLog();

        $webhookLog->siteId = Craft::$app->sites->currentSite->id;
        $webhookLog->type   = $this->getData()->eventName;
        $webhookLog->body   = $this->getData();
        $webhookLog->mode   = strtolower($this->getMode());

        $webhookLog->save();
    }

    /**
     * Sends back a positive non-response to indicate the webhook was handled
     * even though we don't have any meaningful data to give back.
     *
     * @return array
     */
    private function _nonResponse(): array
    {
        return [ 'success' => true ];
    }

}