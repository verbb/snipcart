<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\shipstation;

use craft\base\Model;
/**
 * ShipStation Product Tag Model
 * https://www.shipstation.com/developer-api/#/reference/model-product-tag
 */
class ProductTag extends Model
{
    /**
     * @var number The system generated identifier for the product tag.
     */
    public $tagId;

    /**
     * @var string Name or description for the product tag.
     */
    public $name;

    public function rules(): array
    {
        return [
            [['tagId'],
                'number',
                'integerOnly' => true,
            ],
            [['name'], 'string'],
        ];
    }
}
