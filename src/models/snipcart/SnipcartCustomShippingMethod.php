<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

/**
 * https://docs.snipcart.com/api-reference/custom-shipping-methods
 */

class SnipcartCustomShippingMethod extends \craft\base\Model
{
    // Constants
    // =========================================================================


    // Properties
    // =========================================================================

    public $id;
    public $creationDate;
    public $name;
    public $modificationDate;
    public $postalCodeRegex;
    public $guaranteedEstimatedDelivery;
        // minimumDaysForDelivery 10
        // maximumDaysForDelivery null
    public $location;
        // country null,
        // province null
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

    // Public Methods
    // =========================================================================

}
