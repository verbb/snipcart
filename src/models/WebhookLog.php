<?php
namespace verbb\snipcart\models;

use craft\base\Model;

use DateTime;

class WebhookLog extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?int $siteId = null;
    public ?string $type = null;
    public ?string $body = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;
    public ?string $uid = null;
}
