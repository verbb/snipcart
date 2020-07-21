<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/abandoned-carts
 */

class AbandonedCart extends \craft\base\Model
{
    const STATUS_IN_PROGRESS = 'InProgress';

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $token;

    /**
     * @var
     */
    public $accountId;

    /**
     * @var
     */
    public $location;

    /**
     * @var string
     */
    public $email;

    /**
     * @var
     */
    public $mode;

    /**
     * @var
     */
    public $status;

    /**
     * @var
     */
    public $shipToBillingAddress;

    /**
     * @var
     */
    public $billingAddress;

    /**
     * @var \DateTime
     */
    public $modificationDate;

    /**
     * @var
     */
    public $shippingAddress;

    /**
     * @var \DateTime
     */
    public $completionDate;

    /**
     * @var
     */
    public $invoiceNumber;

    public $shippingInformation;
    /*
    "shippingInformation": {
      "provider": null,
      "fees": 10,
      "method": "Fast custom shipping"
    },
    */

    public $paymentMethod;

    public $summary;
    /*
    summary": {
      "subtotal": 20,
      "taxableTotal": 20,
      "total": 30,
      "paymentMethod": 0,
      "taxes": [],
      "adjustedTotal": 30
    },
    */

    /**
     * @var
     */
    public $metadata;

    /**
     * @var
     */
    public $items;

    /**
     * @var
     */
    public $discounts;

    /**
     * @var
     */
    public $customFields;

    /**
     * @var
     */
    public $plans;

    /**
     * @var
     */
    public $refunds;

    /**
     * @var
     */
    public $currency;

    /**
     * @var
     */
    public $totalWeight;

    /**
     * @var
     */
    public $total;

    /**
     * @var
     */
    public $ipAddress;

    /**
     * @var
     */
    public $userAgent;

    /**
     * @var
     */
    public $lang;

    /**
     * @var
     */
    public $version;

    /**
     * @var
     */
    public $recoveryCampaignStatus;

    /**
     * @var
     */
    public $hasItemsShippable;

    /**
     * @var
     */
    public $taxes;

    /**
     * @var
     */
    public $defaultTaxes;

    /**
     * @var
     */
    public $baseTotal;

    /**
     * @var
     */
    public $discountsTotal;

    /**
     * @var
     */
    public $itemsTotal;

    /**
     * @var
     */
    public $itemsTotalWithoutTaxes;

    /**
     * @var
     */
    public $taxesTotal;

    /**
     * @var
     */
    public $taxProvider;

    /**
     * @var
     */
    public $paymentGatewayUsed;

    /**
     * @var
     */
    public $gatewayResponseData;

    /**
     * @var
     */
    public $paymentGatewayTransactionId;

    /**
     * @var
     */
    public $paymentGatewayInvoiceId;

    /**
     * @var
     */
    public $exported;

    /**
     * @var
     */
    public $isRecurringInvoice;

    /**
     * @var
     */
    public $parentCartId;

    /**
     * @var
     */
    public $guest;

    /**
     * @var
     */
    public $shippingCharged;

    /**
     * @var
     */
    public $partitionKey;

    /**
     * @var
     */
    public $creationDate;

    /**
     * @var
     */
    public $_etag;

    /**
     * @var
     */
    public $userId;

    /**
     * @var
     */
    public $user;

    /**
     * @var
     */
    public $compatibilitySwitches;

    /**
     * @var
     */
    public $notifications;

    /**
     * @var
     */
    public $totalPriceWithoutDiscountsAndTaxes;

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['modificationDate', 'completionDate'];
    }

    /**
     * Returns the Craft control panel URL for the detail page.
     * @return string
     */
    public function getCpUrl(): string
    {
        return \craft\helpers\UrlHelper::cpUrl('snipcart/abandoned/' . $this->token);
    }

    public function getDashboardUrl(): string
    {
        return 'https://app.snipcart.com/dashboard/abandoned/' . $this->token;
    }

}
