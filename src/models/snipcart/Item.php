<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

use craft\base\ElementInterface;
use workingconcept\snipcart\models\snipcart\PaymentSchedule;
use workingconcept\snipcart\helpers\ModelHelper;
use workingconcept\snipcart\records\ProductDetails as ProductDetailsRecord;
use craft\elements\MatrixBlock;

/**
 * Class Item
 *
 * @package workingconcept\snipcart\models\snipcart
 *
 * @property PaymentSchedule|null $paymentSchedule
 */
class Item extends \craft\base\Model
{
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
     * @var float
     */
    public $totalPriceWithoutTaxes;

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
     * @var
     */
    public $hasTaxesIncluded;
    
    /**
     * @var
     */
    public $totalPriceWithoutDiscountsAndTaxesLegacy;

    /**
     * @var
     */
    public $totalPriceWithoutDiscountsAndTaxes;

    /**
     * @var
     */
    public $pausingAction;

    /**
     * @var
     */
    public $cancellationAction;

    /**
     * @var PaymentSchedule
     */
    private $paymentSchedule;

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['modificationDate'];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return ['paymentSchedule'];
    }

    /**
     * Get a Craft Element that's uniquely related to this Item, if possible.
     *
     * @param bool $entryOnly Whether to return the immediately-associated
     *                        Element, like a Matrix block, or the closest Entry.
     *
     * @return ElementInterface|null
     */
    public function getRelatedElement($entryOnly = false)
    {
        // get related record by SKU
        if (! $record = ProductDetailsRecord::findOne([ 'sku' => $this->id ])) {
            // bail without a Record, which can happen if the product's details
            // aren't stored in our Product Details field type
            return null;
        }

        if ($element = \Craft::$app->getElements()->getElementById($record->elementId)) {
            $isMatrix = $element && get_class($element) === MatrixBlock::class;

            if ($isMatrix && $entryOnly) {
                return $element->getOwner();
            }

            return $element;
        }

        // Record without an Element
        return null;
    }

    /**
     * @return PaymentSchedule|null
     */
    public function getPaymentSchedule()
    {
        return $this->paymentSchedule;
    }

    /**
     * @param PaymentSchedule|array|null $paymentSchedule
     * @return PaymentSchedule|null
     */
    public function setPaymentSchedule($paymentSchedule)
    {
        if ($paymentSchedule === null) {
            return $this->paymentSchedule = null;
        }


        if (! $paymentSchedule instanceof PaymentSchedule) {
            $paymentScheduleData = ModelHelper::stripUnknownProperties(
                $paymentSchedule,
                PaymentSchedule::class
            );

            $paymentSchedule = new PaymentSchedule((array) $paymentScheduleData);
        }

        return $this->paymentSchedule = $paymentSchedule;
    }
}
