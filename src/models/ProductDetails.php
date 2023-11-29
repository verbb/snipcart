<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models;

use Craft;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\base\Model;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\Template as TemplateHelper;
use fostercommerce\snipcart\fields\ProductDetails as ProductDetailsField;
use fostercommerce\snipcart\helpers\MeasurementHelper;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\records\ProductDetails as ProductDetailsRecord;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidConfigException;

/**
 * This model is used explicitly for storing Product Details field data and
 * making some Twig functions available for convenience.
 *
 * @package fostercommerce\snipcart\models
 */
class ProductDetails extends Model
{
    public const WEIGHT_UNIT_GRAMS = 'grams';

    public const WEIGHT_UNIT_POUNDS = 'pounds';

    public const WEIGHT_UNIT_OUNCES = 'ounces';

    public const DIMENSIONS_UNIT_CENTIMETERS = 'centimeters';

    public const DIMENSIONS_UNIT_INCHES = 'inches';

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $siteId;

    /**
     * @var
     */
    public $dateCreated;

    /**
     * @var
     */
    public $dateUpdated;

    /**
     * @var
     */
    public $uid;

    /**
     * @var string Unique product identifier passed on to Snipcart.
     */
    public $sku;

    /**
     * @var float Product price.
     */
    public $price;

    /**
     * @var bool Whether or not the product is something that can be shipped.
     */
    public $shippable = false;

    /**
     * @var bool Whether or not the product should be taxed.
     */
    public $taxable = false;

    /**
     * @var float Total product shipping weight.
     */
    public $weight;

    /**
     * @var string Unit that applies to provided weight.
     */
    public $weightUnit;

    /**
     * @var float Total product shipping length.
     */
    public $length;

    /**
     * @var float Total product shipping width.
     */
    public $width;

    /**
     * @var float Total product shipping height.
     */
    public $height;

    /**
     * @var int Number of items in stock or on hand.
     */
    public $inventory;

    /**
     * @var string Unit that applies to provided length, width,
     *             and height dimensions.
     */
    public $dimensionsUnit;

    /**
     * @var
     */
    public $customOptions = [];

    /**
     * @var
     */
    public $elementId;

    /**
     * @var
     */
    public $fieldId;

    /**
     * Get the parent Element that's using the field.
     *
     * @param bool $entryOnly Whether to return the immediately-associated
     *                        Element, like a Matrix block, or the closest Entry.
     *
     * @return ElementInterface|null
     */
    public function getElement($entryOnly = false)
    {
        // Probably a new Entry.
        if (! $this->elementId) {
            return null;
        }

        $element = Craft::$app->elements->getElementById($this->elementId);
        $isMatrix = isset($element) && $element instanceof MatrixBlock;

        if ($isMatrix && $entryOnly) {
            return $element->getOwner();
        }

        return $element;
    }

    /**
     * Get the relevant Field instance.
     *
     * @return FieldInterface|ProductDetailsField|null
     */
    public function getField()
    {
        return Craft::$app->fields->getFieldById($this->fieldId);
    }

    public function rules(): array
    {
        return [
            ['sku', 'validateSku'],
            [['sku', 'weightUnit', 'dimensionsUnit'], 'string'],
            [['length', 'width', 'height', 'weight'],
                'number',
                'integerOnly' => false,
            ],
            [['elementId', 'fieldId', 'inventory'],
                'number',
                'integerOnly' => true,
            ],
            [['shippable'], 'boolean'],
            [['taxable'], 'boolean'],
            [['sku'], 'required'],
            [['weightUnit'],
                'in',
                'range' => [
                    self::WEIGHT_UNIT_GRAMS,
                    self::WEIGHT_UNIT_OUNCES,
                    self::WEIGHT_UNIT_POUNDS,
                ],
            ],
            [['dimensionsUnit'],
                'in',
                'range' => [
                    self::DIMENSIONS_UNIT_CENTIMETERS,
                    self::DIMENSIONS_UNIT_INCHES,
                ],
            ],
            [['weight', 'weightUnit'],
                'required',
                'when' => fn($model): bool => $this->isShippable($model),
                'message' => '{attribute} is required when product is shippable.',
            ],
            [['length', 'width', 'height'],
                'required',
                'when' => fn($model): bool => $this->hasDimensions($model),
                'message' => '{attribute} required if there are other dimensions.',
            ],
            [['dimensionsUnit'],
                'required',
                'when' => fn($model): bool => $this->hasAllDimensions($model),
            ],
        ];
    }

    /**
     * Returns true if the given SKU is not used by another published Element.
     *
     * @param $attribute
     *
     * @throws InvalidConfigException
     */
    public function validateSku($attribute): bool
    {
        if (VersionHelper::isCraft32()) {
            $isUnique = $this->skuIsUniqueElementAttribute($attribute);
        } else {
            $isUnique = $this->skuIsUniqueRecordAttribute($attribute);
        }

        if (! $isUnique) {
            $this->addError($attribute, Craft::t(
                'snipcart',
                'SKU must be unique.'
            ));
        }

        return $isUnique;
    }

    /**
     * Sets default values according to what’s configured on the field instance.
     */
    public function populateDefaults(): void
    {
        $field = $this->getField();
        $isProductDetails = $field instanceof ProductDetailsField;

        if ($field && $isProductDetails) {
            $this->shippable = $field->defaultShippable;
            $this->taxable = $field->defaultTaxable;
            $this->weight = $field->defaultWeight;
            $this->weightUnit = $field->defaultWeightUnit;
            $this->length = $field->defaultLength;
            $this->width = $field->defaultWidth;
            $this->height = $field->defaultHeight;
            $this->dimensionsUnit = $field->defaultDimensionsUnit;
        }
    }

    /**
     * Returns weight unit options for menus.
     */
    public static function getWeightUnitOptions(): array
    {
        return [
            self::WEIGHT_UNIT_GRAMS => 'Grams',
            self::WEIGHT_UNIT_OUNCES => 'Ounces',
            self::WEIGHT_UNIT_POUNDS => 'Pounds',
        ];
    }

    /**
     * Gets dimension unit options for menus.
     */
    public static function getDimensionsUnitOptions(): array
    {
        return [
            self::DIMENSIONS_UNIT_CENTIMETERS => 'Centimeters',
            self::DIMENSIONS_UNIT_INCHES => 'Inches',
        ];
    }

    public function getDimensionInCentimeters($dimension): float
    {
        if ($this->dimensionsUnit === self::DIMENSIONS_UNIT_INCHES) {
            return MeasurementHelper::inchesToCentimeters((float) $this->{$dimension});
        }

        // Already centimeters, safe to return.
        return (float) $this->{$dimension};
    }

    /**
     * Returns true if at least one dimension (length, width, height) has a
     * non-zero value.
     *
     * @param ProductDetails $model
     */
    public function hasDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) || ! empty($instance->width) || ! empty($instance->height);
    }

    /**
     * Returns true if each dimension (length, width, height) as a non-zero value.
     * @param ProductDetails $model
     */
    public function hasAllDimensions($model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) && ! empty($instance->width) && ! empty($instance->height);
    }

    /**
     * Returns true if product is shippable.
     *
     * @param ProductDetails $model
     */
    public function isShippable($model = null): bool
    {
        return ($model ?? $this)->shippable;
    }

    /**
     * Return the current item's weight in grams.
     */
    public function getWeightInGrams(): float
    {
        if ($this->weightUnit === self::WEIGHT_UNIT_GRAMS) {
            // Already in grams, safe to return.
            return $this->weight;
        }

        if ($this->weightUnit === self::WEIGHT_UNIT_OUNCES) {
            return MeasurementHelper::ouncesToGrams((float) $this->weight);
        }

        if ($this->weightUnit === self::WEIGHT_UNIT_POUNDS) {
            return MeasurementHelper::poundsToGrams((float) $this->weight);
        }

        return 0.0;
    }

    /**
     * Get markup for a "buy now" button on the public end of the site.
     *
     * @param array $params
     * @throws
     */
    public function getBuyNowButton($params = []): Markup
    {
        $params = $this->getBuyButtonParams($params);

        return TemplateHelper::raw($this->renderFieldTemplate(
            'snipcart/fields/front-end/buy-now',
            [
                'fieldData' => $this,
                'templateParams' => $params,
            ]
        ));
    }

    /**
     * Configure parameters for a front-end buy button.
     *
     * Simple options format:
     *
     * ```
     * {{ entry.productDetails.getBuyNowButton({
     *    'customOptions': [
     *        {
     *            'name': 'Color',
     *            'required': true,
     *            'options': [ 'blue', 'green', 'red', 'pink' ]
     *        }
     *    ]
     * }) | raw }}
     * ```
     *
     * Options with price variations:
     *
     * ```
     * {{ entry.productDetails.getBuyNowButton({
     *    'customOptions': [
     *        {
     *            'name': 'Color',
     *            'required': true,
     *            'options': [
     *                  {
     *                      'name': 'bronzed',
     *                      'price': 5
     *                  },
     *                  {
     *                      'name': 'diamond-studded'
     *                      'price': 500
     *                  }
     *             ]
     *        }
     *    ]
     * }) | raw }}
     * ```
     *
     * @param array $params
     */
    private function getBuyButtonParams($params = []): array
    {
        $defaults = [
            'href' => '#',
            'target' => null,
            'rel' => null,
            'title' => null,
            'image' => null,
            'text' => 'Buy Now',
            'quantity' => 1,
            'classes' => ['btn'],
            'customOptions' => [],
        ];

        $params = array_merge($defaults, $params);

        /**
         * If we have a simple array without pricing, reformat it
         * for consistency and set all prices to 0.
         */
        if (is_array($params['customOptions']) &&
            $params['customOptions'] !== []
        ) {
            foreach ($params['customOptions'] as &$customOption) {
                $customOptionOptions = [];

                if (isset($customOption['options'])) {
                    foreach ($customOption['options'] as $option) {
                        if (! isset($option['name']) && ! isset($option['price'])) {
                            $customOptionOptions[] = [
                                'name' => $option,
                                'price' => 0,
                            ];
                        } else {
                            $customOptionOptions[] = $option;
                        }
                    }

                    $customOption['options'] = $customOptionOptions;
                }
            }
        }

        return $params;
    }

    /**
     * Returns true if the given attribute’s value is unique among
     * ProductDetailsRecord rows.
     *
     * This tests uniqueness of a SKU in Craft<=3.1.
     *
     * @param $attribute
     */
    private function skuIsUniqueRecordAttribute($attribute): bool
    {
        $duplicateCount = ProductDetailsRecord::find()
            ->where([
                $attribute => $this->{$attribute},
            ])
            ->andWhere(['!=', 'elementId', $this->elementId])
            ->count();

        return (int) $duplicateCount === 0;
    }

    /**
     * Returns true if the given attribute's value is unique among published
     * Elements.
     *
     * This tests uniqueness of a SKU in Craft>=3.2, where drafts become
     * individual Elements and therefore save far more ProductDetailsRecords.
     *
     * @param $attribute
     *
     * @throws InvalidConfigException|ExitException
     */
    private function skuIsUniqueElementAttribute($attribute): bool
    {
        $hasConflict = false;
        $currentElement = $this->getElement();

        /**
         * Get product details with matching SKUs on published Elements.
         */
        /*
        $potentialDuplicates = ProductDetailsRecord::find()
            ->leftJoin('{{%elements}} elements', '[[elements.id]] = {{%snipcart_product_details.elementId}}')
            ->where([$attribute => $this->{$attribute}])
            ->andWhere(['!=', 'elements.id', $this->elementId])
            ->andWhere([
                'elements.enabled' => true,
                'elements.archived' => false,
                'elements.draftId' => null,
                'elements.dateDeleted' => null,
            ])
            ->all();
        */
        $potentialDuplicates = Entry::find()
            ->id(['not', $currentElement->id])
            ->sectionId($currentElement->section->id)
            ->all();

        /**
         * Check each published Element to see if it’s a variation of the current
         * one or a totally separate one with a clashing SKU.
         */

        foreach ($potentialDuplicates as $potentialDuplicate) {
            $duplicateElement = Craft::$app->elements->getElementById($potentialDuplicate->elementId);
            // Let’s be paranoid.
            if ($duplicateElement === null) {
                continue;
            }

            if (is_a($duplicateElement, ElementInterface::class) === false) {
                continue;
            }

            if (! $currentElement instanceof ElementInterface ||
                $duplicateElement::class !== $currentElement::class
            ) {
                // Different element types with the same SKU are a conflict, as
                // are new and existing.
                $hasConflict = true;
                break;
            }

            $getCanonicalId = static function($element): int {
                if (version_compare(Craft::$app->getVersion(), '3.7', '>=')) {
                    return (int) $element->canonicalId;
                }

                return (int) $element->sourceId;
            };

            if ($duplicateElement instanceof Entry) {
                // Don’t worry about unpublished Elements.
                if ($duplicateElement->revisionId === null) {
                    continue;
                }

                // If a different Entry is using the SKU, that’s a conflict.
                if ($getCanonicalId($duplicateElement) !== $getCanonicalId($currentElement)) {
                    $hasConflict = true;
                    break;
                }
            }

            if ($duplicateElement instanceof MatrixBlock) {
                // A duplicate in a different field is a conflict.
                if ((int) $duplicateElement->fieldId !== (int) $currentElement->fieldId) {
                    $hasConflict = true;
                    break;
                }

                // Duplicate within same Matrix field on the same Entry.
                $sameSource = $getCanonicalId($duplicateElement->getOwner()) === $getCanonicalId($currentElement->getOwner());
                $sameOwner = (int) $duplicateElement->ownerId === (int) $currentElement->ownerId;

                if ($sameSource && $sameOwner) {
                    $hasConflict = true;
                    break;
                }
            }
        }

        return $hasConflict === false;
    }

    /**
     * Renders plugin Twig templates with provided data.
     *
     * @param $template
     * @param $data
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function renderFieldTemplate(string $template, array $data): string
    {
        $view = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        $html = $view->renderTemplate($template, $data);

        $view->setTemplateMode($templateMode);

        return TemplateHelper::raw($html);
    }
}
