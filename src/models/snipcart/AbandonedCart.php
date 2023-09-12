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
use fostercommerce\snipcart\behaviors\BillingAddressBehavior;
use fostercommerce\snipcart\behaviors\ShippingAddressBehavior;
use fostercommerce\snipcart\helpers\ModelHelper;

/**
 * https://docs.snipcart.com/v2/api-reference/abandoned-carts
 *
 * @property Address $billingAddress
 * @property Address $shippingAddress */
class AbandonedCart extends Model
{
    public const STATUS_IN_PROGRESS = 'InProgress';

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
     * @var \DateTime
     */
    public $modificationDate;

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

    private ?Address $_billingAddress = null;

    private ?Address $_shippingAddress = null;

    public function getBillingAddress(): ?Address
    {
        return $this->_billingAddress;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->_shippingAddress;
    }

    /**
     * @param Address|array $address
     * @return Address
     */
    public function setBillingAddress($address): ?Address
    {
        if (! $address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties(
                $address,
                Address::class
            );

            $address = new Address((array) $addrData);
        }

        return $this->_billingAddress = $address;
    }

    /**
     * @param Address|array $address
     * @return Address
     */
    public function setShippingAddress($address): ?Address
    {
        if (! $address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties(
                $address,
                Address::class
            );
            $addressModel = new Address((array) $addrData);
        }

        return $this->_shippingAddress = $addressModel;
    }

    public function datetimeAttributes(): array
    {
        return ['modificationDate', 'completionDate'];
    }

    /**
     * @inheritdoc
     *
     * Proxy all our billingAddress* and shippingAddress* fields without having
     * to use a whole bunch of getters and setters on this model.
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['billingAddress'] = [
            'class' => BillingAddressBehavior::class,
        ];

        $behaviors['shippingAddress'] = [
            'class' => ShippingAddressBehavior::class,
        ];

        return $behaviors;
    }

    /**
     * Returns the Craft control panel URL for the detail page.
     */
    public function getCpUrl(): string
    {
        return UrlHelper::cpUrl('snipcart/abandoned/' . $this->token);
    }

    public function getDashboardUrl(): string
    {
        return 'https://app.snipcart.com/dashboard/abandoned/' . $this->token;
    }

    public function extraFields(): array
    {
        return [
            'billingAddress',
            'shippingAddress',
        ];
    }
}
