<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class Tax extends Model
{
    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?float $amount = null;
    public ?string $numberForInvoice = null;
}
