<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use workingconcept\snipcart\Snipcart;
use craft\elements\Entry;

class Item extends \craft\base\Model
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
        $productIdentifier = Snipcart::$plugin->getSettings()->productIdentifier;

        if ($productIdentifier === 'id')
        {
            $element = Entry::find()
                ->id($this->id)
                ->one();
        }
        else
        {
            $element = Entry::find()
                ->where($productIdentifier, $this->id)
                ->one();
        }

        if ( ! empty($element))
        {
            if (is_array($element))
            {
                return $element[0];
            }

            return $element;
        }

        return null;
    }

}
