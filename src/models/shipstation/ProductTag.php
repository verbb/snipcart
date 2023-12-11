<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class ProductTag extends Model
{
    // Properties
    // =========================================================================

    public mixed $tagId = null;
    public ?string $name = null;
    

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['tagId'], 'number', 'integerOnly' => true],
            [['name'], 'string'],
        ];
    }
}
