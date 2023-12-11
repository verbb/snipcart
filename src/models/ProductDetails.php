<?php
namespace verbb\snipcart\models;

use verbb\snipcart\fields\ProductDetails as ProductDetailsField;
use verbb\snipcart\helpers\MeasurementHelper;
use verbb\snipcart\records\ProductDetails as ProductDetailsRecord;

use Craft;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\base\Model;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\helpers\Template as TemplateHelper;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidConfigException;

use DateTime;

class ProductDetails extends Model
{
    // Constants
    // =========================================================================

    public const WEIGHT_UNIT_GRAMS = 'grams';
    public const WEIGHT_UNIT_POUNDS = 'pounds';
    public const WEIGHT_UNIT_OUNCES = 'ounces';
    public const DIMENSIONS_UNIT_CENTIMETERS = 'centimeters';
    public const DIMENSIONS_UNIT_INCHES = 'inches';
    

    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?int $siteId = null;
    public ?string $sku = null;
    public ?float $price = null;
    public bool $shippable = false;
    public bool $taxable = false;
    public ?float $weight = null;
    public ?string $weightUnit = null;
    public ?float $length = null;
    public ?float $width = null;
    public ?float $height = null;
    public ?int $inventory = null;
    public ?string $dimensionsUnit = null;
    public array $customOptions = [];
    public ?int $elementId = null;
    public ?int $fieldId = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;
    public ?string $uid = null;


    // Public Methods
    // =========================================================================

    public function getElement(bool $entryOnly = false): ?ElementInterface
    {
        if (!$this->elementId) {
            return null;
        }

        $element = Craft::$app->elements->getElementById($this->elementId);
        $isMatrix = isset($element) && $element instanceof MatrixBlock;

        if ($isMatrix && $entryOnly) {
            return $element->getOwner();
        }

        return $element;
    }

    public function getField(): ProductDetailsField|FieldInterface|null
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

    public function validateSku($attribute): bool
    {
        $isUnique = $this->skuIsUniqueElementAttribute($attribute);

        if (!$isUnique) {
            $this->addError($attribute, Craft::t('snipcart', 'SKU must be unique.'));
        }

        return $isUnique;
    }

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

    public static function getWeightUnitOptions(): array
    {
        return [
            self::WEIGHT_UNIT_GRAMS => 'Grams',
            self::WEIGHT_UNIT_OUNCES => 'Ounces',
            self::WEIGHT_UNIT_POUNDS => 'Pounds',
        ];
    }

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
        return (float)$this->{$dimension};
    }

    public function hasDimensions(ProductDetails $model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) || ! empty($instance->width) || ! empty($instance->height);
    }

    public function hasAllDimensions(ProductDetails $model = null): bool
    {
        $instance = $model ?? $this;
        return ! empty($instance->length) && ! empty($instance->width) && ! empty($instance->height);
    }

    public function isShippable(ProductDetails $model = null): bool
    {
        return ($model ?? $this)->shippable;
    }

    public function getWeightInGrams(): float
    {
        if ($this->weightUnit === self::WEIGHT_UNIT_GRAMS) {
            // Already in grams, safe to return.
            return $this->weight;
        }

        if ($this->weightUnit === self::WEIGHT_UNIT_OUNCES) {
            return MeasurementHelper::ouncesToGrams($this->weight);
        }

        if ($this->weightUnit === self::WEIGHT_UNIT_POUNDS) {
            return MeasurementHelper::poundsToGrams($this->weight);
        }

        return 0.0;
    }

    public function getBuyNowButton(array $params = []): Markup
    {
        $params = $this->getBuyButtonParams($params);

        return TemplateHelper::raw($this->renderFieldTemplate('snipcart/fields/front-end/buy-now', [
            'fieldData' => $this,
            'params' => $params,
        ]));
    }


    // Private Methods
    // =========================================================================

    private function getBuyButtonParams(array $params = []): array
    {
        $element = $this->getElement(true);

        // Provide some options as top-level for good DX and backwards-compatibility
        $price = ArrayHelper::remove($params, 'price', $this->price);
        $class = ArrayHelper::remove($params, 'classes', []);
        $image = ArrayHelper::remove($params, 'image');
        $name = ArrayHelper::remove($params, 'name', ($element->title ?? $this->sku));
        $url = ArrayHelper::remove($url, 'image', ($element->url ?? ''));
        $quantity = ArrayHelper::remove($params, 'quantity', 1);

        $options = [
            'href' => '#',
            'text' => Craft::t('snipcart', 'Buy Now'),
            'class' => array_merge(['snipcart-add-item'], $class),
            'data-item-image' => $image,
            'data-item-id' => $this->sku,
            'data-item-name' => $name,
            'data-item-price' => is_array($price) ? Json::encode($price) : $price,
            'data-item-url' => $url,
            'data-item-quantity' => $quantity,
            'data-item-taxable' => $this->taxable ? true : false,
            'data-item-shippable' => $this->shippable ? true : false,
        ];

        if ($this->shippable) {
            $options['data-item-width'] = round($this->getDimensionInCentimeters('width'));
            $options['data-item-length'] = round($this->getDimensionInCentimeters('length'));
            $options['data-item-height'] = round($this->getDimensionInCentimeters('height'));
            $options['data-item-weight'] = $this->getWeightInGrams();
        }

        $customOptions = ArrayHelper::remove($params, 'customOptions', []);

        foreach ($customOptions as $key => $customOption) {
            $index = $key + 1;

            $options["data-item-custom$index-name"] = $customOption['name'] ?? '';
            $options["data-item-custom$index-required"] = ($customOption['required'] ?? null) ? true : false;

            $optionsData = [];

            foreach (($customOption['options'] ?? []) as $option) {
                $optionName = $option['name'] ?? $option;
                $optionPrice = ($option['price'] ?? null);

                if ($optionPrice && $optionPrice > 0 && !str_starts_with($optionPrice, '+')) {
                    $optionPrice = '+' . $optionPrice;
                }

                $optionsData[] = $optionPrice ? $optionName . '[' . $optionPrice . ']' : $optionName;
            }

            if ($optionsData) {
                $options["data-item-custom$index-options"] = implode('|', $optionsData);
            }
        }

        return array_replace_recursive($options, $params);
    }

    private function skuIsUniqueRecordAttribute($attribute): bool
    {
        $duplicateCount = ProductDetailsRecord::find()
            ->where([
                $attribute => $this->{$attribute},
            ])
            ->andWhere(['!=', 'elementId', $this->elementId])
            ->count();

        return (int)$duplicateCount === 0;
    }

    private function skuIsUniqueElementAttribute($attribute): bool
    {
        $hasConflict = false;
        $currentElement = $this->getElement();

        $potentialDuplicates = Entry::find()
            ->id(['not', $currentElement->id])
            ->sectionId($currentElement->section->id)
            ->all();

        foreach ($potentialDuplicates as $potentialDuplicate) {
            $duplicateElement = Craft::$app->elements->getElementById($potentialDuplicate->elementId);

            if ($duplicateElement === null) {
                continue;
            }

            if (is_a($duplicateElement, ElementInterface::class) === false) {
                continue;
            }

            if (!$currentElement instanceof ElementInterface || $duplicateElement::class !== $currentElement::class) {
                // Different element types with the same SKU are a conflict, as are new and existing.
                $hasConflict = true;
                break;
            }

            $getCanonicalId = static function($element): int {
                return (int) $element->canonicalId;
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
                if ((int) $duplicateElement->fieldId !== (int)$currentElement->fieldId) {
                    $hasConflict = true;
                    break;
                }

                // Duplicate within same Matrix field on the same Entry.
                $sameSource = $getCanonicalId($duplicateElement->getOwner()) === $getCanonicalId($currentElement->getOwner());
                $sameOwner = (int) $duplicateElement->ownerId === (int)$currentElement->ownerId;

                if ($sameSource && $sameOwner) {
                    $hasConflict = true;
                    break;
                }
            }
        }

        return $hasConflict === false;
    }

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
