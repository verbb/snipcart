<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;

/**
 * https://docs.snipcart.com/v2/api-reference/notifications
 */
class Notification extends Model
{
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

    /**
     * @var string "0c3ac0bb-a94a-45c5-a4d8-a7934a7f180a"
     */
    public $id;

    /**
     * @var \DateTime "2017-07-09T14:59:57.987Z"
     */
    public $creationDate;

    /**
     * @var string Synonymous with `$notificationType`.
     */
    public $type;

    /**
     * @var string The token of the refunded order.
     */
    public $orderToken;

    /**
     * @var string The type of the notification that has been added to the order.
     *
     * See `getNotificationTypes()` for possible values.
     */
    public $notificationType;

    /**
     * @var string `email`
     */
    public $deliveryMethod;

    /**
     * @var bool
     */
    public $sentByEmail;

    /**
     * @var \DateTime The send date of the email, if applicable.
     */
    public $sentByEmailOn;

    /**
     * @var string The body of the email message, if applicable.
     */
    public $body;

    /**
     * @var string The message or comment on the notification.
     */
    public $message;

    /**
     * @var string The subject of the email message, if applicable.
     */
    public $subject;

    /**
     * @var string "2017-06-07T19:09:31.933Z"
     */
    public $sentOn;

    /**
     * @var
     */
    public $resourceUrl;

    public function datetimeAttributes(): array
    {
        return ['creationDate', 'sentByEmailOn', 'sentOn'];
    }

    /**
     * Returns a list of all valid `notificationType` values.
     * @return string[]
     */
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
