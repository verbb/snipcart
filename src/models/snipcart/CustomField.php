<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class CustomField extends Model
{
    // Properties
    // =========================================================================
    
    public ?string $name = null;
    public ?string $operation = null;
    public ?string $type = null;
    public array $options = [];
    public bool $required = false;
    public mixed $value = null;
    public array $optionsArray = [];
}
