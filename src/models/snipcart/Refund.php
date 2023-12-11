<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

use DateTime;

class Refund extends Model
{
    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $orderToken = null;
    public ?float $amount = null;
    public ?string $comment = null;
    public ?bool $refundedByPaymentGateway = null;
    public ?bool $notifyCustomer = null;
    public ?bool $notifiedCustomerByEmail = null;
    public ?string $currency = null;
    public ?DateTime $creationDate = null;
    public ?DateTime $modificationDate = null;


    // Public Methods
    // =========================================================================

    public function getPayloadForPost(): array
    {
        $payload = $this->toArray();

        unset(
            $payload['id'],
            $payload['orderToken'],
            $payload['refundedByPaymentGateway'],
            $payload['creationDate'],
            $payload['modificationDate'],
            $payload['notifiedCustomerByEmail'],
            $payload['currency']
        );

        $payload['token'] = $this->orderToken;

        return $payload;
    }
}
