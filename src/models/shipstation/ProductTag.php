<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class ProductTag extends Model
{
    // Properties
    // =========================================================================

    public mixed $tagId = null;
    public ?string $name = null;
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['tagId'], 'number', 'integerOnly' => true];

        return $rules;
    }
}
