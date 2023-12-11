<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class ProductCategory extends Model
{
    // Properties
    // =========================================================================

    public mixed $categoryId = null;
    public ?string $name = null;
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['categoryId'], 'number', 'integerOnly' => true],
            [['name'], 'string'],
        ];
    }
}
