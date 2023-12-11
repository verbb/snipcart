<?php
namespace verbb\snipcart\models\snipcart;

use verbb\snipcart\Snipcart;

use craft\base\Model;
use craft\helpers\UrlHelper;

use DateTime;

class Subscription extends Model
{
    // Constants
    // =========================================================================

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_PAUSED = 'Paused';
    public const STATUS_CANCELED = 'Canceled';


    // Properties
    // =========================================================================
    
    public ?string $user = null;
    public ?string $initialOrderToken = null;
    public ?string $firstInvoiceReceivedOn = null;
    public ?string $schedule = null;
    public ?string $itemId = null;
    public ?string $id = null;
    public ?string $name = null;
    public ?DateTime $creationDate = null;
    public ?DateTime $modificationDate = null;
    public ?string $cancelledOn = null;
    public ?string $amount = null;
    public ?string $quantity = null;
    public ?string $userDefinedId = null;
    public ?string $totalSpent = null;
    public ?string $status = null;
    public ?string $gatewayId = null;
    public ?string $metadata = null;
    public ?string $cartId = null;
    public ?string $recurringShipping = null;
    public ?string $shippingCharged = null;
    public ?DateTime $nextBillingDate = null;
    public ?string $upcomingPayments = null;
    public ?string $invoiceNumber = null;


    // Public Methods
    // =========================================================================

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
        return Snipcart::$plugin->getSubscriptions()->getSubscriptionInvoices($this->id);
    }
}
