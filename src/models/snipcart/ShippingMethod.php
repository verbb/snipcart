<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

use DateTime;

class ShippingMethod extends Model
{
    // Properties
    // =========================================================================
    
    public ?string $id = null;
    public ?DateTime $creationDate = null;
    public ?string $name = null;
    public ?DateTime $modificationDate = null;
    public ?string $postalCodeRegex = null;
    public ?string $guaranteedEstimatedDelivery = null;
    public ?string $location = null;
    public ?string $rates = null;
}
