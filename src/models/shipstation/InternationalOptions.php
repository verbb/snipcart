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
 * ShipStation International Options Model
 * https://www.shipstation.com/developer-api/#/reference/model-internationaloptions
 */
class InternationalOptions extends Model
{
    public const CONTENTS_MERCHANDISE = 'merchandise';

    public const CONTENTS_DOCUMENTS = 'documents';

    public const CONTENTS_GIFT = 'gift';

    public const CONTENTS_RETURNED_GOODS = 'returned_goods';

    public const CONTENTS_SAMPLE = 'sample';

    public const NON_DELIVERY_RETURN_TO_SENDER = 'return_to_sender';

    public const NON_DELIVERY_TREAT_AS_ABANDONED = 'treat_as_abandoned';

    /**
     * @var string|null Contents of international shipment. Available options:
     *                  "merchandise", "documents", "gift", "returned_goods",
     *                  or "sample".
     */
    public $contents;

    /**
     * @var string|null Non-Delivery option for international shipment.
     *                  Available options are: "return_to_sender" or
     *                  "treat_as_abandoned". Please note: If the shipment is
     *                  created through the Orders/CreateLabelForOrder endpoint
     *                  and the nonDelivery field is not specified then value
     *                  defaults based on the International Setting in the UI.
     *                  If the call is being made to the Shipments/CreateLabel
     *                  endpoint and the nonDelivery field is not specified then
     *                  the value will default to "return_to_sender".
     */
    public $nonDelivery;

    /**
     * @var CustomsItem[]|null An array of customs items. Please note: If you
     *                         wish to supply customsItems in the CreateOrder
     *                         call and have the values not be overwritten
     *                         by ShipStation, you must have the
     *                         International Settings > Customs Declarations set
     *                         to "Leave blank (Enter Manually)" in the UI:
     *                         https://ss.shipstation.com/#/settings/international
     */
    private $customsItems;


    // Public Methods
    // =========================================================================

    /**
     * Gets customs items.
     *
     * @return CustomsItem[]
     */
    public function getCustomsItems(): array
    {
        if ($this->customsItems !== null) {
            return $this->customsItems;
        }

        $this->customsItems = [];

        return $this->customsItems;
    }

    /**
     * Sets customs items.
     *
     * @param CustomsItem[]|null $customsItems Customs items to be set.
     *
     * @return CustomsItem[]|null
     */
    public function setCustomsItems($customsItems)
    {
        return $this->customsItems = $customsItems;
    }

    public function rules(): array
    {
        return [
            [['contents', 'nonDelivery'], 'string'],
            [['contents'],
                'in',
                'range' => [self::CONTENTS_MERCHANDISE, self::CONTENTS_DOCUMENTS, self::CONTENTS_GIFT, self::CONTENTS_RETURNED_GOODS, self::CONTENTS_SAMPLE],
            ],
            [['nonDelivery'],
                'in',
                'range' => [self::NON_DELIVERY_RETURN_TO_SENDER, self::NON_DELIVERY_TREAT_AS_ABANDONED],
            ],
        ];
    }
}
