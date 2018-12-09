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
 * ShipStation International Options Model
 * https://www.shipstation.com/developer-api/#/reference/model-internationaloptions
 */

class ShipStationInternationalOptions extends Model
{
    // Constants
    // =========================================================================

    const CONTENTS_MERCHANDISE = 'merchandise';
    const CONTENTS_DOCUMENTS = 'documents';
    const CONTENTS_GIFT = 'gift';
    const CONTENTS_RETURNED_GOODS = 'returned_goods';
    const CONTENTS_SAMPLE = 'sample';

    const NON_DELIVERY_RETURN_TO_SENDER = 'return_to_sender';
    const NON_DELIVERY_TREAT_AS_ABANDONED = 'treat_as_abandoned';


    // Properties
    // =========================================================================

    /**
     * @var string|null Contents of international shipment. Available options are: "merchandise", "documents", "gift", "returned_goods", or "sample".
     */
    public $contents;

    /**
     * @var ShipStationCustomsItem[]|null An array of customs items. Please note: If you wish to supply customsItems in the CreateOrder call and have the values not be overwritten by ShipStation, you must have the International Settings > Customs Declarations set to "Leave blank (Enter Manually)" in the UI: https://ss.shipstation.com/#/settings/international
     */
    private $_customsItems;

    /**
     * @var string|null Non-Delivery option for international shipment. Available options are: "return_to_sender" or "treat_as_abandoned". Please note: If the shipment is created through the Orders/CreateLabelForOrder endpoint and the nonDelivery field is not specified then value defaults based on the International Setting in the UI. If the call is being made to the Shipments/CreateLabel endpoint and the nonDelivery field is not specified then the value will default to "return_to_sender".
     */
    public $nonDelivery;


    // Public Methods
    // =========================================================================

    /**
     * Gets customs items.
     *
     * @return ShipStationCustomsItem[]
     */
    public function getCustomsItems(): array
    {
        if ($this->_customsItems !== null)
        {
            return $this->_customsItems;
        }

        $this->_customsItems = [];

        return $this->_customsItems;
    }


    /**
     * Sets customs items.
     *
     * @param ShipStationCustomsItem[] $customsItems Customs items to be set.
     *
     * @return ShipStationCustomsItem[]
     */
    public function setCustomsItems(array $customsItems)
    {
        return $this->_customsItems = $customsItems;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contents', 'nonDelivery'], 'string'],
            [['contents'], 'in', 'range' => [self::CONTENTS_MERCHANDISE, self::CONTENTS_DOCUMENTS, self::CONTENTS_GIFT, self::CONTENTS_RETURNED_GOODS, self::CONTENTS_SAMPLE]],
            [['nonDelivery'], 'in', 'range' => [self::NON_DELIVERY_RETURN_TO_SENDER, self::NON_DELIVERY_TREAT_AS_ABANDONED]],
        ];
    }

}