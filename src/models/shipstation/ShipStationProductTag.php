<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

/**
 * ShipStation Product Tag Model
 * https://www.shipstation.com/developer-api/#/reference/model-product-tag
 */

class ShipStationProductTag extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var number The system generated identifier for the product tag.
     */
    public $tagId;

    /**
     * @var string Name or description for the product tag.
     */
    public $name;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagId'], 'number', 'integerOnly' => true],
            [['name'], 'string'],
        ];
    }

}