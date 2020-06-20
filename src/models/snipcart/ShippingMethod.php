<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/custom-shipping-methods
 */

class ShippingMethod extends \craft\base\Model
{
    /**
     * @var
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $creationDate;

    /**
     * @var
     */
    public $name;

    /**
     * @var \DateTime
     */
    public $modificationDate;

    /**
     * @var
     */
    public $postalCodeRegex;

    /**
     * @var
     */
    public $guaranteedEstimatedDelivery;
        // minimumDaysForDelivery 10
        // maximumDaysForDelivery null

    /**
     * @var
     */
    public $location;
        // country null,
        // province null

    /**
     * @var
     */
    public $rates;
        // {
        //   "cost": 20,
        //   "weight": {
        //     "to": 1000
        //   }
        // },
        // {
        //   "cost": 30,
        //   "weight": {
        //     "from": 1000,
        //     "to": 2000
        //   }
        // },
        // {
        //   "cost": 60,
        //   "weight": {
        //     "from": 3000
        //   }
        // }

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate'];
    }

}
