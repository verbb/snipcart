<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

/**
 * https://docs.snipcart.com/api-reference/discounts
 */

class SnipcartDiscount extends Model
{
    // Constants
    // =========================================================================


    // Properties
    // =========================================================================

    public $id;
    public $name;
    public $trigger;
    public $code;
    public $itemId;
    public $totalToReach;
    public $type;
    public $rate;
    public $amount;
    public $alternatePrice;
    public $maxNumberOfUsages;
    public $expires;
    public $numberOfUsages;
    public $numberOfUsagesUncompleted;
    public $shippingDescription;
    public $shippingCost;
    public $shippingGuaranteedDaysToDelivery;


    // Public Methods
    // =========================================================================

}
