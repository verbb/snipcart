<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\shipstation;

/**
 * ShipStation Product Category Model
 * https://www.shipstation.com/developer-api/#/reference/model-product-category
 */

class ProductCategory extends \craft\base\Model
{
    /**
     * @var number The system generated identifier for the product category.
     */
    public $categoryId;

    /**
     * @var string Name or description for the product category.
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['categoryId'], 'number', 'integerOnly' => true],
            [['name'], 'string'],
        ];
    }

}