<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;
use craft\helpers\UrlHelper;
use fostercommerce\snipcart\Snipcart;

/**
 * Class Subscription
 * https://docs.snipcart.com/v2/api-reference/subscriptions
 *
 * @package fostercommerce\snipcart\models
 */
class Subscription extends Model
{
    public const STATUS_ACTIVE = 'Active';

    public const STATUS_PAUSED = 'Paused';

    public const STATUS_CANCELED = 'Canceled';

    /**
     * @var
     */
    public $user;

    /**
     * @var
     */
    public $initialOrderToken;

    /**
     * @var
     */
    public $firstInvoiceReceivedOn;

    /**
     * @var
     */
    public $schedule;

    /**
     * @var
     */
    public $itemId;

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $creationDate;

    /**
     * @var
     */
    public $modificationDate;

    /**
     * @var
     */
    public $cancelledOn;

    /**
     * @var
     */
    public $amount;

    /**
     * @var
     */
    public $quantity;

    /**
     * @var
     */
    public $userDefinedId;

    /**
     * @var
     */
    public $totalSpent;

    /**
     * @var
     */
    public $status;

    /**
     * @var
     */
    public $gatewayId;

    /**
     * @var
     */
    public $metadata;

    /**
     * @var
     */
    public $cartId;

    /**
     * @var
     */
    public $recurringShipping;

    /**
     * @var
     */
    public $shippingCharged;

    /**
     * @var
     */
    public $nextBillingDate;

    /**
     * @var
     */
    public $upcomingPayments;

    /**
     * @var
     */
    public $invoiceNumber;

    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate', 'cancelledOn', 'nextBillingDate', 'firstInvoiceReceivedOn'];
    }

    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/subscription/' . $this->id);
    }

    public function getDashboardUrl(): string
    {
        return 'https://app.snipcart.com/dashboard/subscriptions/' . $this->id;
    }

    public function getInvoices(): array
    {
        return Snipcart::$plugin->subscriptions->getSubscriptionInvoices($this->id);
    }
}
