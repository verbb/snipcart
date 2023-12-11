<?php
namespace verbb\snipcart\models\shipstation;

/**
 * ShipStation Product Tag Model
 * https://www.shipstation.com/developer-api/#/reference/model-product-tag
 */

class ProductTag extends \craft\base\Model
{
    /**
     * @var number The system generated identifier for the product tag.
     */
    public $tagId;

    /**
     * @var string Name or description for the product tag.
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['tagId'], 'number', 'integerOnly' => true],
            [['name'], 'string'],
        ];
    }

}