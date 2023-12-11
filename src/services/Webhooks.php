<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\events\CustomerEvent;
use verbb\snipcart\events\OrderEvent;
use verbb\snipcart\events\OrderNotificationEvent;
use verbb\snipcart\events\OrderRefundEvent;
use verbb\snipcart\events\OrderStatusEvent;
use verbb\snipcart\events\OrderTrackingEvent;
use verbb\snipcart\events\SubscriptionEvent;
use verbb\snipcart\events\TaxesEvent;
use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\Customer;
use verbb\snipcart\models\snipcart\Notification;
use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\models\snipcart\Refund;
use verbb\snipcart\models\snipcart\Subscription;
use verbb\snipcart\records\ShippingQuoteLog;
use verbb\snipcart\records\WebhookLog;

use Craft;
use craft\base\Component;

class Webhooks extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_PROCESS_COMPLETED_ORDER = 'beforeProcessCompletedOrder';
    public const EVENT_ON_ORDER_STATUS_CHANGED = 'onOrderStatusChanged';
    public const EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED = 'onOrderPaymentStatusChanged';
    public const EVENT_ON_ORDER_TRACKING_CHANGED = 'onOrderTrackingChanged';
    public const EVENT_ON_SUBSCRIPTION_CREATED = 'onSubscriptionCreated';
    public const EVENT_ON_SUBSCRIPTION_CANCELLED = 'onSubscriptionCancelled';
    public const EVENT_ON_SUBSCRIPTION_PAUSED = 'onSubscriptionPaused';
    public const EVENT_ON_SUBSCRIPTION_RESUMED = 'onSubscriptionResumed';
    public const EVENT_ON_SUBSCRIPTION_INVOICE_CREATED = 'onSubscriptionInvoiceCreated';
    public const EVENT_ON_TAXES_CALCULATE = 'onTaxesCalculate';
    public const EVENT_ON_CUSTOMER_UPDATE = 'onCustomerUpdate';
    public const EVENT_ON_ORDER_REFUND_CREATED = 'onOrderRefundCreated';
    public const EVENT_ON_ORDER_NOTIFICATION_CREATED = 'onOrderNotificationCreated';
    public const WEBHOOK_MODE_LIVE = 'Live';
    public const WEBHOOK_MODE_TEST = 'Test';


    // Properties
    // =========================================================================

    private mixed $webhookData;
    private string $currentMode = '';


    // Public Methods
    // =========================================================================

    public function setData($payload): void
    {
        $this->setMode($payload->mode);
        $this->webhookData = $payload;

        if (Snipcart::$plugin->getSettings()->logWebhookRequests) {
            $this->logWebhookTransaction();
        }
    }

    public function getData(): mixed
    {
        return $this->webhookData;
    }

    public function setMode(string $mode): string
    {
        return $this->currentMode = $mode;
    }

    public function getMode(): string
    {
        return $this->currentMode;
    }

    public function handleOrderCompleted(): array
    {
        $order = $this->getCleanOrder();

        $responseData = [
            'success' => true,
            'errors' => [],
        ];

        if ($this->hasEventHandlers(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER)) {
            $this->trigger(self::EVENT_BEFORE_PROCESS_COMPLETED_ORDER, new OrderEvent([
                'order' => $order,
            ]));
        }

        $providerOrders = Snipcart::$plugin->getShipments()->handleCompletedOrder($order);

        if (!empty($providerOrders->errors)) {
            $responseData['success'] = false;
            $responseData['errors'][] = $providerOrders->errors;
        }

        if (!Snipcart::$plugin->getOrders()->updateProductsFromOrder($order)) {
            $responseData['success'] = false;
            $responseData['errors'][] = [
                'elements' => 'Failed to update product Elements.',
            ];
        }

        if (isset($providerOrders->orders['shipStation'])) {
            $responseData['shipstation_order_id'] = $providerOrders->orders['shipStation']->orderId ?? '';
        }

        if (Snipcart::$plugin->getSettings()->sendOrderNotificationEmail) {
            Snipcart::$plugin->getOrders()->sendOrderEmailNotification($order, [
                'providerOrders' => $providerOrders->orders ?? null,
            ], Orders::NOTIFICATION_TYPE_ADMIN);
        }

        if (Snipcart::$plugin->getSettings()->sendCustomerOrderNotificationEmail) {
            Snipcart::$plugin->getOrders()->sendOrderEmailNotification($order, [
                'providerOrders' => $providerOrders->orders ?? null,
            ], Orders::NOTIFICATION_TYPE_CUSTOMER);
        }

        if ($responseData['errors'] === []) {
            unset($responseData['errors']);
        }

        return $responseData;
    }

    public function handleShippingRatesFetch(): array
    {
        $order = $this->getCleanOrder();
        $rates = Snipcart::$plugin->getShipments()->collectRatesForOrder($order);

        if (Snipcart::$plugin->getSettings()->logCustomRates) {
            $shippingQuoteLog = new ShippingQuoteLog();
            $shippingQuoteLog->siteId = Craft::$app->sites->currentSite->id;
            $shippingQuoteLog->token = $order->token;
            $shippingQuoteLog->body = $rates;
            $shippingQuoteLog->save();
        }

        return $rates;
    }

    public function handleOrderStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus = $this->getData()->to;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_STATUS_CHANGED)) {
            $this->trigger(self::EVENT_ON_ORDER_STATUS_CHANGED, new OrderStatusEvent([
                'order' => $order,
                'fromStatus' => $fromStatus,
                'toStatus' => $toStatus,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleOrderPaymentStatusChange(): array
    {
        $fromStatus = $this->getData()->from;
        $toStatus = $this->getData()->to;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED)) {
            $this->trigger(self::EVENT_ON_ORDER_PAYMENT_STATUS_CHANGED, new OrderStatusEvent([
                'order' => $order,
                'fromStatus' => $fromStatus,
                'toStatus' => $toStatus,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleOrderTrackingNumberChange(): array
    {
        $trackingNumber = $this->getData()->trackingNumber;
        $trackingUrl = $this->getData()->trackingUrl;
        $order = $this->getCleanOrder();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_TRACKING_CHANGED)) {
            $this->trigger(self::EVENT_ON_ORDER_TRACKING_CHANGED, new OrderTrackingEvent([
                'order' => $order,
                'trackingNumber' => $trackingNumber,
                'trackingUrl' => $trackingUrl,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleSubscriptionCreated(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CREATED)) {
            $this->trigger(self::EVENT_ON_SUBSCRIPTION_CREATED, new SubscriptionEvent([
                'subscription' => $subscription,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleSubscriptionCancelled(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_CANCELLED)) {
            $this->trigger(self::EVENT_ON_SUBSCRIPTION_CANCELLED, new SubscriptionEvent([
                'subscription' => $subscription,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleSubscriptionPaused(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_PAUSED)) {
            $this->trigger(self::EVENT_ON_SUBSCRIPTION_PAUSED, new SubscriptionEvent([
                'subscription' => $subscription,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleSubscriptionResumed(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_RESUMED)) {
            $this->trigger(self::EVENT_ON_SUBSCRIPTION_RESUMED, new SubscriptionEvent([
                'subscription' => $subscription,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleSubscriptionInvoiceCreated(): array
    {
        $subscription = $this->getCleanSubscription();

        if ($this->hasEventHandlers(self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED)) {
            $this->trigger(self::EVENT_ON_SUBSCRIPTION_INVOICE_CREATED, new SubscriptionEvent([
                'subscription' => $subscription,
            ]));
        }

        return $this->nonResponse();
    }

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

    public function handleCustomerUpdated(): array
    {
        $customer = $this->getCleanCustomer();

        if ($this->hasEventHandlers(self::EVENT_ON_CUSTOMER_UPDATE)) {
            $this->trigger(self::EVENT_ON_CUSTOMER_UPDATE, new CustomerEvent([
                'customer' => $customer,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleRefundCreated(): array
    {
        $refund = $this->getCleanRefund();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_REFUND_CREATED)) {
            $this->trigger(self::EVENT_ON_ORDER_REFUND_CREATED, new OrderRefundEvent([
                'refund' => $refund,
            ]));
        }

        return $this->nonResponse();
    }

    public function handleNotificationCreated(): array
    {
        $notification = $this->getCleanNotification();

        if ($this->hasEventHandlers(self::EVENT_ON_ORDER_NOTIFICATION_CREATED)) {
            $this->trigger(self::EVENT_ON_ORDER_NOTIFICATION_CREATED, new OrderNotificationEvent([
                'notification' => $notification,
            ]));
        }

        return $this->nonResponse();
    }

    
    // Private Methods
    // =========================================================================

    private function getCleanOrder(): Order
    {
        return ModelHelper::safePopulateModel($this->getData()->content, Order::class);
    }

    private function getCleanSubscription(): Subscription
    {
        return ModelHelper::safePopulateModel($this->getData()->content, Subscription::class);
    }

    private function getCleanCustomer(): Customer
    {
        return ModelHelper::safePopulateModel($this->getData()->content, Customer::class);
    }

    private function getCleanRefund(): Refund
    {
        return ModelHelper::safePopulateModel($this->getData()->content, Refund::class);
    }

    private function getCleanNotification(): Notification
    {
        return ModelHelper::safePopulateModel($this->getData()->content, Notification::class);
    }

    private function logWebhookTransaction(): void
    {
        $webhookLog = new WebhookLog();

        $webhookLog->siteId = Craft::$app->sites->currentSite->id;
        $webhookLog->type = $this->getData()->eventName;
        $webhookLog->body = $this->getData();
        $webhookLog->mode = strtolower($this->getMode());

        $webhookLog->save();
    }

    private function nonResponse(): array
    {
        return [
            'success' => true,
        ];
    }
}
