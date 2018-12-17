<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

/**
 * https://docs.snipcart.com/api-reference/discounts
 */

class SnipcartDiscount extends \craft\base\Model
{
    // Constants
    // =========================================================================

    const TRIGGER_CODE = 'Code';
    const TYPE_RATE    = 'Rate';

    // Properties
    // =========================================================================

    /**
     * @var string "2223490d-84c1-480c-b713-50cb0b819313"
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $trigger;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $itemId;

    /**
     * @var
     */
    public $totalToReach;

    /**
     * @var
     */
    public $type;

    /**
     * @var int
     */
    public $rate;

    /**
     * @var
     */
    public $amount;

    /**
     * @var float|null
     */
    public $alternatePrice;

    /**
     * @var int
     */
    public $maxNumberOfUsages;

    /**
     * @var
     */
    public $expires;

    /**
     * @var int
     */
    public $numberOfUsages;

    /**
     * @var int
     */
    public $numberOfUsagesUncompleted;

    /**
     * @var
     */
    public $shippingDescription;

    /**
     * @var
     */
    public $shippingCost;

    /**
     * @var
     */
    public $shippingGuaranteedDaysToDelivery;


    // Public Methods
    // =========================================================================

}
