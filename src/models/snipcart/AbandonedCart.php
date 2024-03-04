<?php
namespace verbb\snipcart\models\snipcart;

use verbb\snipcart\behaviors\BillingAddressBehavior;
use verbb\snipcart\behaviors\ShippingAddressBehavior;
use verbb\snipcart\helpers\ModelHelper;

use craft\base\Model;
use craft\helpers\UrlHelper;

use DateTime;
use stdClass;

class AbandonedCart extends Model
{
    // Constants
    // =========================================================================

    public const STATUS_IN_PROGRESS = 'InProgress';


    // Properties
    // =========================================================================
    
    public ?string $id = null;
    public ?string $token = null;
    public ?string $accountId = null;
    public ?string $location = null;
    public ?string $email = null;
    public ?string $mode = null;
    public ?string $status = null;
    public ?string $shipToBillingAddress = null;
    public ?DateTime $modificationDate = null;
    public ?DateTime $completionDate = null;
    public ?string $invoiceNumber = null;
    public ?stdClass $shippingInformation = null;
    public ?string $paymentMethod = null;
    public ?stdClass $summary = null;
    public stdClass|array|null $metadata = null;
    public array $items = [];
    public array $discounts = [];
    public array $customFields = [];
    public ?string $plans = null;
    public array $refunds = [];
    public ?string $currency = null;
    public ?string $totalWeight = null;
    public ?string $total = null;
    public ?string $ipAddress = null;
    public ?string $userAgent = null;
    public ?string $lang = null;
    public ?string $version = null;
    public ?stdClass $recoveryCampaignStatus = null;
    public ?bool $hasItemsShippable = null;
    public array $taxes = [];
    public array $defaultTaxes = [];
    public ?string $baseTotal = null;
    public ?string $discountsTotal = null;
    public ?string $itemsTotal = null;
    public ?string $itemsTotalWithoutTaxes = null;
    public ?string $taxesTotal = null;
    public ?string $taxProvider = null;
    public ?string $paymentGatewayUsed = null;
    public ?string $gatewayResponseData = null;
    public ?string $paymentGatewayTransactionId = null;
    public ?string $paymentGatewayInvoiceId = null;
    public ?string $exported = null;
    public ?bool $isRecurringInvoice = null;
    public ?string $parentCartId = null;
    public ?string $guest = null;
    public ?string $shippingCharged = null;
    public ?string $partitionKey = null;
    public ?DateTime $creationDate = null;
    public ?string $_etag = null;
    public ?string $userId = null;
    public ?stdClass $user = null;
    public ?stdClass $compatibilitySwitches = null;
    public array $notifications = [];
    public ?string $totalPriceWithoutDiscountsAndTaxes = null;

    private ?Address $_billingAddress = null;
    private ?Address $_shippingAddress = null;


    // Public Methods
    // =========================================================================

    public function getBillingAddress(): ?Address
    {
        return $this->_billingAddress;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->_shippingAddress;
    }

    public function setBillingAddress(array|stdClass|Address $address): ?Address
    {
        if (!$address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties($address, Address::class);

            $address = new Address((array)$addrData);
        }

        return $this->_billingAddress = $address;
    }

    public function setShippingAddress(array|stdClass|Address $address): ?Address
    {
        if (!$address instanceof Address) {
            if ($address === null) {
                $address = [];
            }

            $addrData = ModelHelper::stripUnknownProperties($address, Address::class);
            $addressModel = new Address((array)$addrData);
        }

        return $this->_shippingAddress = $addressModel;
    }

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
