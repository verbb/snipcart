<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/notifications
 */

class Notification extends \craft\base\Model
{
    const TYPE_INVOICE               = 'Invoice';
    const TYPE_COMMENT               = 'Comment';
    const TYPE_TRACKING_NUMBER       = 'TrackingNumber';
    const TYPE_ORDER_CANCELLED       = 'OrderCancelled';
    const TYPE_REFUND                = 'Refund';
    const TYPE_ORDER_SHIPPED         = 'OrderShipped';
    const TYPE_ORDER_RECEIVED        = 'OrderReceived';
    const TYPE_ORDER_PAYMENT_EXPIRED = 'OrderPaymentExpired';
    const TYPE_ORDER_STATUS_CHANGED  = 'OrderStatusChanged';
    const TYPE_RECOVERY_CAMPAIGN     = 'RecoveryCampaign';
    const TYPE_DIGITAL_DOWNLOAD      = 'DigitalDownload';
    const TYPE_LOGS                  = 'Logs';
    const TYPE_OTHER                 = 'Other';

    const DELIVERY_METHOD_EMAIL = 'Email';
    const DELIVERY_METHOD_NONE  = 'None';

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

    /**
     * @inheritdoc
     */
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
