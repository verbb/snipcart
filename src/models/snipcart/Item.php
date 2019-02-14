<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use workingconcept\snipcart\records\ProductDetails;
use craft\elements\Entry;

class Item extends \craft\base\Model
{
    // Constants
    // =========================================================================

    
    // Properties
    // =========================================================================

    /**
     * @var string Snipcart's own unique ID for the item.
     */
    public $uniqueId;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string The product ID originally sent with the buy button.
     */
    public $id;

    /**
     * @var
     */
    public $subscriptionId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $originalPrice;

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
     * @var CustomField[]|null
     */
    public $customFields;

    /**
     * @var string|null
     */
    public $customFieldsJson;

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
    public $startsOn;

    /**
     * @var \DateTime
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
    public $metadata;

    /**
     * @var PaymentSchedule
     */
    public $paymentSchedule;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['modificationDate'];
    }

    /**
     * Get a Craft Element that's uniquely related to this Item, if possible.
     *
     * @return Entry|null
     */
    public function getRelatedElement()
    {
        if ($record = ProductDetails::findOne([ 'sku' => $this->id ]))
        {
            if ($element = Entry::findOne([ 'id' => $record->elementId ]))
            {
                return $element;
            }
        }

        return null;
    }

}
