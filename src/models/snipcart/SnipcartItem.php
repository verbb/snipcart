<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

class SnipcartItem extends Model
{
    // Constants
    // =========================================================================

    
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $uniqueId;

    /**
     * @var string
     */
    public $token;

    /**
     * @var
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $description;

    /**
     * @var
     */
    public $fileGuid;

    /**
     * @var
     */
    public $initialData;

    /**
     * @var
     */
    public $categories;

    /**
     * @var
     */
    public $url;

    /**
     * @var int
     */
    public $weight;

    /**
     * @var
     */
    public $image;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var int|null
     */
    public $minQuantity;

    /**
     * @var int|null
     */
    public $maxQuantity;

    /**
     * @var bool
     */
    public $stackable;

    /**
     * @var bool
     */
    public $shippable;

    /**
     * @var bool
     */
    public $taxable;

    /**
     * @var
     */
    public $taxes;

    /**
     * @var SnipcartCustomField[]|null
     */
    public $customFields;

    /**
     * @var bool
     */
    public $duplicatable;

    /**
     * @var
     */
    public $alternatePrices;

    /**
     * @var bool
     */
    public $hasDimensions;

    /**
     * @var float
     */
    public $unitPrice;

    /**
     * @var float
     */
    public $totalPrice;

    /**
     * @var
     */
    public $totalWeight;

    /**
     * @var string
     */
    public $addedOn;

    /**
     * @var string
     */
    public $modificationDate;

    /**
     * @var float
     */
    public $width;

    /**
     * @var float
     */
    public $height;

    /**
     * @var float
     */
    public $length;

    /**
     * @var
     */
    public $metaData;

    /**
     * @var SnipcartPaymentSchedule
     */
    public $paymentSchedule;


    // Public Methods
    // =========================================================================

}
