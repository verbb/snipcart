<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;
/**
 * https://docs.snipcart.com/v2/api-reference/custom-shipping-methods
 */
class ShippingMethod extends Model
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

    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate'];
    }
}
