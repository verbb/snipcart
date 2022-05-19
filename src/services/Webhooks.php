<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\services;

use craft\base\Component;
use Craft;
use fostercommerce\snipcart\events\CustomerEvent;
use fostercommerce\snipcart\events\OrderEvent;
use fostercommerce\snipcart\events\OrderNotificationEvent;
use fostercommerce\snipcart\events\OrderRefundEvent;
use fostercommerce\snipcart\events\OrderStatusEvent;
use fostercommerce\snipcart\events\OrderTrackingEvent;
use fostercommerce\snipcart\events\SubscriptionEvent;
use fostercommerce\snipcart\events\TaxesEvent;
use fostercommerce\snipcart\helpers\ModelHelper;
use fostercommerce\snipcart\models\snipcart\Customer;
use fostercommerce\snipcart\models\snipcart\Notification;
use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\models\snipcart\Refund;
use fostercommerce\snipcart\models\snipcart\Subscription;
use fostercommerce\snipcart\records\ShippingQuoteLog;
use fostercommerce\snipcart\records\WebhookLog;
use fostercommerce\snipcart\Snipcart;

/**
 * This class handles valid data that's posted to Snipcart’s webhook endpoint.
 *
 * @package fostercommerce\snipcart\services
 */
class Webhooks extends Component
{
    /**
     * @event OrderEvent Triggered before a completed event is handled by the plugin.
     */
    public const EVENT_BEFORE_PROCESS_COMPLETED_ORDER = 'beforeProcessCompletedOrder';

    /**
     * @event OrderStatusEvent
     */
    public const EVENT_ON_ORDER_STATUS_CHANGED = 'onOrderStatusChanged';

    /**
     * @event OrderStatusEvent
     */
    public const EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED = 'onOrderPaymentStatusChanged';

    /**
     * @event OrderTrackingEvent
     */
    public const EVENT_ON_ORDER_TRACKING_CHANGED = 'onOrderTrackingChanged';

    /**
     * @event SubscriptionEvent
     */
    public const EVENT_ON_SUBSCRIPTION_CREATED = 'onSubscriptionCreated';

    /**
     * @event SubscriptionEvent
     */
    public const EVENT_ON_SUBSCRIPTION_CANCELLED = 'onSubscriptionCancelled';

    /**
     * @event SubscriptionEvent
     */
    public const EVENT_ON_SUBSCRIPTION_PAUSED = 'onSubscriptionPaused';

    /**
     * @event SubscriptionEvent
     */
    public const EVENT_ON_SUBSCRIPTION_RESUMED = 'onSubscriptionResumed';

    /**
     * @event SubscriptionEvent
     */
    public const EVENT_ON_SUBSCRIPTION_INVOICE_CREATED = 'onSubscriptionInvoiceCreated';

    /**
     * @event TaxesEvent
     */
    public const EVENT_ON_TAXES_CALCULATE = 'onTaxesCalculate';

    /**
     * @event CustomerEvent
     */
    public const EVENT_ON_CUSTOMER_UPDATE = 'onCustomerUpdate';

    /**
     * @event OrderRefundEvent
     */
    public const EVENT_ON_ORDER_REFUND_CREATED = 'onOrderRefundCreated';

    /**
     * @event OrderNotificationEvent
     */
    public const EVENT_ON_ORDER_NOTIFICATION_CREATED = 'onOrderNotificationCreated';

    /**
     * @var string Indicates that the webhook payload is live
     */
    public const WEBHOOK_MODE_LIVE = 'Live';

    /**
     * @var string Indicates that the webhook payload is for testing
     */
    public const WEBHOOK_MODE_TEST = 'Test';

    /**
     * @var mixed local reference to decoded post data
     */
    private $webhookData;

    /**
     * @var string Should be either WEBHOOK_MODE_LIVE or WEBHOOK_MODE_TEST
     */
    private string $currentMode = '';

    /**
     * Sets the payload data and derived mode to be utilized within the service
     * and quietly logs the request before processing if logging is enabled.
     *
     * @param $payload
     */
    public function setData($payload): void
    {
        /**
         * Track whether we’re in live or test mode. We know ->mode exists
         * because the payload is validated before it gets here.
         */
        $this->setMode($payload->mode);

        /**
         * Establish our local reference to the payload.
         */
        $this->webhookData = $payload;

        /**
         * Log for troubleshooting if that option is enabled.
         */
        if (Snipcart::$plugin->getSettings()->logWebhookRequests) {
            $this->logWebhookTransaction();
        }
    }

    /**
     * Returns payload data in whatever format it was received.
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->webhookData;
    }

    /**
     * Sets the mode of the current request, which is either `Live` or `Test`.
     *
     * @param string $mode 'Live' or 'Test'
     *
     * @return mixed
     */
    public function setMode(string $mode): string
    {
        return $this->currentMode = $mode;
    }

    /**
     * Gets the mode of the current request, which is either `Live` or `Test`.
     */
    public function getMode(): string
    {
        return $this->currentMode;
    }

    /**
     * Handles a completed order.
     *
     * @return array Array with `success` (bool) and `errors` (string[])
     * @throws
     */
    public function handleOrderCompleted(): array
    {
        $order = $this->getCleanOrder();

        $responseData = [
            'success' => true,
            'errors' => [],
        ];

        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER)) {
            $this->trigger(
                self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER,
                new OrderEvent([
                    'order' => $order,
                ])
            );
        }

        $providerOrders = Snipcart::$plugin->shipments->handleCompletedOrder($order);

        if (! empty($providerOrders->errors)) {
            $responseData['success'] = false;
            $responseData['errors'][] = $providerOrders->errors;
        }

        if (! Snipcart::$plugin->orders->updateProductsFromOrder($order)) {
            $responseData['success'] = false;
            $responseData['errors'][] = [
                'elements' => 'Failed to update product Elements.',
            ];
        }

        if (isset($providerOrders->orders['shipStation'])) {
            /**
             * Successful orders have a populated ->orderId, but with tests
             * we set ->orderId to 99999999.
             */
            $responseData['shipstation_order_id'] = $providerOrders->orders['shipStation']->orderId ?? '';
        }

        if (Snipcart::$plugin->getSettings()->sendOrderNotificationEmail) {
            Snipcart::$plugin->orders->sendOrderEmailNotification(
                $order,
                [
                    'providerOrders' => $providerOrders->orders ?? null,
                ],
                Orders::NOTIFICATION_TYPE_ADMIN
            );
        }

        if (Snipcart::$plugin->getSettings()->sendCustomerOrderNotificationEmail) {
            Snipcart::$plugin->orders->sendOrderEmailNotification(
                $order,
                [
                    'providerOrders' => $providerOrders->orders ?? null,
                ],
                Orders::NOTIFICATION_TYPE_CUSTOMER
            );
        }

        if ($responseData['errors'] === []) {
            unset($responseData['errors']);
        }

        return $responseData;
    }

    /**
     * Processes Snipcart’s shipping rate event, which gives us order details
     * and lets us send back shipping options.
     *
     * @return array [ 'rates' => ShippingRate[], 'package' => Package ]
     */
    public function handleShippingRatesFetch(): array
    {
        $order = $this->getCleanOrder();
        $rates = Snipcart::$plugin->shipments->collectRatesForOrder($order);

        if (Snipcart::$plugin->getSettings()->logCustomRates) {
            $shippingQuoteLog = new ShippingQuoteLog();
            $shippingQuoteLog->siteId = Craft::$app->sites->currentSite->id;
            $shippingQuoteLog->token = $order->token;
            $shippingQuoteLog->body = $rates;
            $shippingQuoteLog->save();
        }

        return $rates;
    }

    /**
     * Handles an order status change.
     */
    public function handleOrderStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus = $this->getData()->to;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_STATUS_CHANGED)) {
            $this->trigger(
                self::EVENT_ON_ORDER_STATUS_CHANGED,
                new OrderStatusEvent([
                    'order' => $order,
                    'fromStatus' => $fromStatus,
                    'toStatus' => $toStatus,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles an order payment status change.
     */
    public function handleOrderPaymentStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus = $this->getData()->to;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED)) {
            $this->trigger(
                self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED,
                new OrderStatusEvent([
                    'order' => $order,
                    'fromStatus' => $fromStatus,
                    'toStatus' => $toStatus,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles an order tracking number change.
     */
    public function handleOrderTrackingNumberChange(): array
    {
        $trackingNumber = $this->getData()->trackingNumber;
        $trackingUrl = $this->getData()->trackingUrl;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_TRACKING_CHANGED)) {
            $this->trigger(
                self::EVENT_ON_ORDER_TRACKING_CHANGED,
                new OrderTrackingEvent([
                    'order' => $order,
                    'trackingNumber' => $trackingNumber,
                    'trackingUrl' => $trackingUrl,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a created subscription.
     */
    public function handleSubscriptionCreated(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CREATED)) {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a cancelled subscription.
     */
    public function handleSubscriptionCancelled(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CANCELLED)) {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_CANCELLED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a paused subscription.
     */
    public function handleSubscriptionPaused(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_PAUSED)) {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_PAUSED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a resumed subscription.
     */
    public function handleSubscriptionResumed(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_RESUMED)) {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_RESUMED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a new subscription invoice.
     */
    public function handleSubscriptionInvoiceCreated(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED)) {
            $this->trigger(
                self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED,
                new SubscriptionEvent([
                    'subscription' => $subscription,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles a tax calculation request.
     *
     * @return array `taxes` => Tax[]
     */
    public function handleTaxesCalculate(): array
    {
        $order = $this->getCleanOrder();
        $taxes = [];

        if ($this->hasEventHandlers(self::EVENT_ON_TAXES_CALCULATE)) {
            $taxesEvent = new TaxesEvent([
                'order' => $order,
                'taxes' => [],
            ]);

            $this->trigger(self::EVENT_ON_TAXES_CALCULATE, $taxesEvent);
            $taxes = array_merge($taxes, $taxesEvent->taxes);
        }

        return [
            'taxes' => $taxes,
        ];
    }

    /**
     * Handles updated customer details.
     */
    public function handleCustomerUpdated(): array
    {
        $customer = $this->getCleanCustomer();

        if ($this->hasEventHandlers(self::EVENT_ON_CUSTOMER_UPDATE)) {
            $this->trigger(
                self::EVENT_ON_CUSTOMER_UPDATE,
                new CustomerEvent([
                    'customer' => $customer,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles new refund.
     */
    public function handleRefundCreated(): array
    {
        $refund = $this->getCleanRefund();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_REFUND_CREATED)) {
            $this->trigger(
                self::EVENT_ON_ORDER_REFUND_CREATED,
                new OrderRefundEvent([
                    'refund' => $refund,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Handles new order notification.
     */
    public function handleNotificationCreated(): array
    {
        $notification = $this->getCleanNotification();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_NOTIFICATION_CREATED)) {
            $this->trigger(
                self::EVENT_ON_ORDER_NOTIFICATION_CREATED,
                new OrderNotificationEvent([
                    'notification' => $notification,
                ])
            );
        }

        return $this->nonResponse();
    }

    /**
     * Returns posted payload as an Order without letting unexpected root-level
     * attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don’t want errors in production.
     */
    private function getCleanOrder(): Order
    {
        return ModelHelper::safePopulateModel(
            $this->getData()->content,
            Order::class
        );
    }

    /**
     * Returns posted payload as a Subscription without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don’t want errors in production.
     */
    private function getCleanSubscription(): Subscription
    {
        return ModelHelper::safePopulateModel(
            $this->getData()->content,
            Subscription::class
        );
    }

    /**
     * Returns posted payload as a Customer without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don’t want errors in production.
     */
    private function getCleanCustomer(): Customer
    {
        return ModelHelper::safePopulateModel(
            $this->getData()->content,
            Customer::class
        );
    }

    /**
     * Returns posted payload as a Refund without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don’t want errors in production.
     */
    private function getCleanRefund(): Refund
    {
        return ModelHelper::safePopulateModel(
            $this->getData()->content,
            Refund::class
        );
    }

    /**
     * Returns posted payload as a Notification without letting unexpected
     * root-level attributes throw an exception.
     *
     * This is important because Snipcart payloads sometimes include new
     * attributes without warning and we don’t want errors in production.
     */
    private function getCleanNotification(): Notification
    {
        return ModelHelper::safePopulateModel(
            $this->getData()->content,
            Notification::class
        );
    }

    /**
     * Stores webhook details to the database for later scrutiny.
     */
    private function logWebhookTransaction(): void
    {
        $webhookLog = new WebhookLog();

        $webhookLog->siteId = Craft::$app->sites->currentSite->id;
        $webhookLog->type = $this->getData()->eventName;
        $webhookLog->body = $this->getData();
        $webhookLog->mode = strtolower($this->getMode());

        $webhookLog->save();
    }

    /**
     * Sends back a positive non-response to indicate the webhook was handled
     * even though we don't have any meaningful data to give back.
     */
    private function nonResponse(): array
    {
        return [
            'success' => true,
        ];
    }
}
