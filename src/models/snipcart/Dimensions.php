<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class Dimensions extends Model
{
    // Properties
    // =========================================================================
    
    public ?string $width = null;
    public ?string $height = null;
    public ?string $length = null;
    public ?string $weight = null;
}
