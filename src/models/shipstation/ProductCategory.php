<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class ProductCategory extends Model
{
    // Properties
    // =========================================================================

    public mixed $categoryId = null;
    public ?string $name = null;
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['categoryId'], 'number', 'integerOnly' => true];

        return $rules;
    }
}
