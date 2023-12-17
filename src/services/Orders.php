<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;
use verbb\snipcart\errors\ShippingRateException;
use verbb\snipcart\events\ShippingRateEvent;
use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\models\snipcart\Notification;
use verbb\snipcart\models\snipcart\Order;
use verbb\snipcart\models\snipcart\Package;
use verbb\snipcart\models\snipcart\Refund;

use Craft;
use craft\base\Component;

use DateTime;
use stdClass;
use Exception;
use Throwable;

class Orders extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_REQUEST_SHIPPING_RATES = 'beforeRequestShippingRates';
    public const NOTIFICATION_TYPE_ADMIN = 'notifyAdmin';
    public const NOTIFICATION_TYPE_CUSTOMER = 'notifyCustomer';


    // Public Methods
    // =========================================================================

    public function getOrder(String $orderId): ?Order
    {
        if ($orderData = Snipcart::$plugin->getApi()->get("orders/$orderId")) {
            return ModelHelper::safePopulateModel((array)$orderData, Order::class);
        }

        return null;
    }

    public function getOrders(array $params = []): array
    {
        return ModelHelper::safePopulateArrayWithModels((array)$this->fetchOrders($params)->items, Order::class);
    }

    public function getAllOrders(array $params = []): array
    {
        $collection = [];
        $collected = 0;
        $offset = 0;
        $finished = false;

        while ($finished === false) {
            $params['offset'] = $offset;

            if ($result = $this->fetchOrders($params)) {
                $currentItems = (array)$result->items;
                $collected += count($currentItems);
                $collection[] = $currentItems;

                if ($result->totalItems > $collected) {
                    ++$offset;
                } else {
                    $finished = true;
                }
            } else {
                $finished = true;
            }
        }

        $items = array_merge(...$collection);

        return ModelHelper::safePopulateArrayWithModels($items, Order::class);
    }

    public function getOrderNotifications(string $orderId): array
    {
        return ModelHelper::safePopulateArrayWithModels((array) Snipcart::$plugin->getApi()->get("orders/$orderId/notifications"), Notification::class);
    }

    public function getOrderRefunds(string $orderId): array
    {
        return ModelHelper::safePopulateArrayWithModels((array) Snipcart::$plugin->getApi()->get("orders/$orderId/refunds"), Refund::class);
    }

    public function listOrders(int $page = 1, int $limit = 25, array $params = []): stdClass
    {
        $params['offset'] = ($page - 1) * $limit;
        $params['limit'] = $limit;

        $response = $this->fetchOrders($params);

        // convert the data from an stdClass to an array
        $items = json_decode(json_encode($response->items, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        return (object) [
            'items' => ModelHelper::safePopulateArrayWithModels($items, Order::class),
            'totalItems' => $response->totalItems,
            'offset' => $response->offset,
            'limit' => $limit,
        ];
    }

    public function updateProductsFromOrder(Order $order): bool
    {
        if (Snipcart::$plugin->getSettings()->reduceQuantitiesOnOrder) {
            foreach ($order->items as $orderItem) {
                Snipcart::$plugin->getProducts()->reduceInventory($orderItem);
                // TODO: reduce product inventory in ShipStation if necessary
            }
        }

        return true;
    }

    public function getOrderPackaging(Order $order): Package
    {
        $package = new Package();

        if ($this->hasEventHandlers(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES)) {
            $shippingRateEvent = new ShippingRateEvent([
                'order' => $order,
                'package' => $package,
            ]);

            $this->trigger(self::EVENT_BEFORE_REQUEST_SHIPPING_RATES, $shippingRateEvent);
            
            if (!$shippingRateEvent->isValid) {
                throw new ShippingRateException($shippingRateEvent);
            }

            $package = $shippingRateEvent->package;
        }

        return $package;
    }

    public function sendOrderEmailNotification(Order $order, array $extra = [], string $type = self::NOTIFICATION_TYPE_ADMIN): bool|array
    {
        $templateSettings = $this->selectNotificationTemplate($type);

        $emailVars = array_merge([
            'order' => $order,
            'settings' => Snipcart::$plugin->getSettings(),
        ], $extra);

        Snipcart::$plugin->getNotifications()->setEmailTemplate($templateSettings['path'], null, $templateSettings['user']);

        Snipcart::$plugin->getNotifications()->setNotificationVars($emailVars);

        $toEmails = [];
        $subject = Craft::t('snipcart', '{name} just placed an order', [
            'name' => $order->billingAddressName,
        ]);

        if ($type === self::NOTIFICATION_TYPE_ADMIN) {
            $toEmails = Snipcart::$plugin->getSettings()->notificationEmails;
        } else if ($type === self::NOTIFICATION_TYPE_CUSTOMER) {
            $toEmails = [$order->email];
            $subject = Craft::t('snipcart', '{siteName} Order #{invoiceNumber}', [
                'siteName' => Craft::$app->getSites()->getCurrentSite()->name,
                'invoiceNumber' => $order->invoiceNumber,
            ]);
        }

        if (!Snipcart::$plugin->getNotifications()->sendEmail($toEmails, $subject)) {
            return Snipcart::$plugin->getNotifications()->getErrors();
        }

        return true;
    }

    public function refundOrder(string $orderId, float $amount, string $comment = '', bool $notifyCustomer = false): mixed
    {
        $refund = new Refund([
            'orderToken' => $orderId,
            'amount' => $amount,
            'comment' => $comment,
            'notifyCustomer' => $notifyCustomer,
        ]);

        return Snipcart::$plugin->getApi()->post("orders/$orderId/refunds", $refund->getPayloadForPost());
    }


    // Private Methods
    // =========================================================================

    private function fetchOrders(array $params = []): array|stdClass
    {
        $validParams = [
            'offset',
            'limit',
            'from',
            'to',
            'status',
            'invoiceNumber',
            'placedBy',
        ];

        $apiParams = [];
        $hasCacheParam = isset($params['cache']) && is_bool($params['cache']);
        $cacheSetting = $hasCacheParam ? $params['cache'] : true;
        $dateTimeFormat = 'Y-m-d\TH:i:sP';

        if (isset($params['from']) && $params['from'] instanceof DateTime) {
            $params['from'] = $params['from']->format($dateTimeFormat);
        }

        if (isset($params['to']) && $params['to'] instanceof DateTime) {
            $params['to'] = $params['to']->format($dateTimeFormat);
        }

        foreach ($params as $key => $value) {
            if (in_array($key, $validParams, true)) {
                $apiParams[$key] = $value;
            }
        }

        return Snipcart::$plugin->getApi()->get('orders', $apiParams, $cacheSetting);
    }

    private function selectNotificationTemplate(string $type): array
    {
        $model = Snipcart::$plugin->getSettings();
        $defaultTemplatePath = '';
        $customTemplatePath = '';

        if ($type === self::NOTIFICATION_TYPE_ADMIN) {
            $defaultTemplatePath = 'snipcart/email/order';
            $customTemplatePath = $model->notificationEmailTemplate;
        } else if ($type === self::NOTIFICATION_TYPE_CUSTOMER) {
            $defaultTemplatePath = 'snipcart/email/customer-order';
            $customTemplatePath = $model->customerNotificationEmailTemplate;
        }

        $useCustom = ! empty($customTemplatePath);
        $templatePath = $useCustom ? $customTemplatePath : $defaultTemplatePath;

        return [
            'path' => $templatePath,
            'user' => $useCustom,
        ];
    }
}
