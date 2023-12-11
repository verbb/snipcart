<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;
use DateTime;

class Notification extends Model
{
    // Constants
    // =========================================================================

    public const TYPE_INVOICE = 'Invoice';
    public const TYPE_COMMENT = 'Comment';
    public const TYPE_TRACKING_NUMBER = 'TrackingNumber';
    public const TYPE_ORDER_CANCELLED = 'OrderCancelled';
    public const TYPE_REFUND = 'Refund';
    public const TYPE_ORDER_SHIPPED = 'OrderShipped';
    public const TYPE_ORDER_RECEIVED = 'OrderReceived';
    public const TYPE_ORDER_PAYMENT_EXPIRED = 'OrderPaymentExpired';
    public const TYPE_ORDER_STATUS_CHANGED = 'OrderStatusChanged';
    public const TYPE_RECOVERY_CAMPAIGN = 'RecoveryCampaign';
    public const TYPE_DIGITAL_DOWNLOAD = 'DigitalDownload';
    public const TYPE_LOGS = 'Logs';
    public const TYPE_OTHER = 'Other';
    public const DELIVERY_METHOD_EMAIL = 'Email';
    public const DELIVERY_METHOD_NONE = 'None';


    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?DateTime $creationDate = null;
    public ?string $type = null;
    public ?string $orderToken = null;
    public ?string $notificationType = null;
    public ?string $deliveryMethod = null;
    public ?bool $sentByEmail = null;
    public ?DateTime $sentByEmailOn = null;
    public ?string $body = null;
    public ?string $message = null;
    public ?string $subject = null;
    public ?string $sentOn = null;
    public ?string $resourceUrl = null;


    // Public Methods
    // =========================================================================

    public function getNotificationTypes(): array
    {
        return [
            self::TYPE_INVOICE,
            self::TYPE_COMMENT,
            self::TYPE_TRACKING_NUMBER,
            self::TYPE_ORDER_CANCELLED,
            self::TYPE_REFUND,
            self::TYPE_ORDER_SHIPPED,
            self::TYPE_ORDER_RECEIVED,
            self::TYPE_ORDER_PAYMENT_EXPIRED,
            self::TYPE_ORDER_STATUS_CHANGED,
            self::TYPE_RECOVERY_CAMPAIGN,
            self::TYPE_DIGITAL_DOWNLOAD,
            self::TYPE_LOGS,
            self::TYPE_OTHER,
        ];
    }
}
