<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

use workingconcept\snipcart\Snipcart;

/**
 * Class Subscription
 * https://docs.snipcart.com/v2/api-reference/subscriptions
 *
 * @package workingconcept\snipcart\models
 */
class Subscription extends \craft\base\Model
{
    const STATUS_ACTIVE   = 'Active';
    const STATUS_PAUSED   = 'Paused';
    const STATUS_CANCELED = 'Canceled';

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

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate', 'cancelledOn', 'nextBillingDate', 'firstInvoiceReceivedOn'];
    }

    public function getCpUrl(): string
    {
        return \craft\helpers\UrlHelper::cpUrl('snipcart/subscription/' . $this->id);
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
