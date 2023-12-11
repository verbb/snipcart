<?php
namespace verbb\snipcart\models;

use craft\base\Model;

class ShippingQuoteLog extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?int $siteId = null;
    public ?string $token = null;
    public ?string $body = null;
    public ?DateTime $dateCreated = null;
    public ??DateTime $dateUpdated = null;
    public ?string $uid = null;
}
