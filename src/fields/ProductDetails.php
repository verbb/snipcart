<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\fields;

use craft\base\ElementInterface;
use Craft;
use craft\base\Field;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use fostercommerce\snipcart\assetbundles\ProductDetailsFieldAsset;
use fostercommerce\snipcart\db\Table;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\models\ProductDetails as ProductDetailsModel;
use fostercommerce\snipcart\Snipcart;
use fostercommerce\snipcart\validators\ProductDetailsValidator;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use LitEmoji\LitEmoji;
use yii\db\Schema;

/**
 * ProductDetails
 *
 * @property ProductDetails $value
 */
class ProductDetails extends Field
{
    /**
     * @var string The type of database column the field should have in the content table
     */
    public $columnType = [
        'sku' => Schema::TYPE_STRING,
        'inventory' => Schema::TYPE_INTEGER,
        'price' => Schema::TYPE_MONEY,
        'taxable' => Schema::TYPE_BOOLEAN,
        'shippable' => Schema::TYPE_BOOLEAN,
        'weight' => Schema::TYPE_FLOAT,
        'weightUnit' => Schema::TYPE_STRING,
        'length' => Schema::TYPE_FLOAT,
        'width' => Schema::TYPE_FLOAT,
        'height' => Schema::TYPE_FLOAT,
        'dimensionsUnit' => Schema::TYPE_STRING,
    ];

    /**
     * @var bool Whether to display "shippable" option for this field instance
     *           and allow it to be set per entry.
     */
    public $displayShippableSwitch = false;

    /**
     * @var bool Whether to display "taxable" option for this field instance
     *           and allow it to be set per entry.
     */
    public $displayTaxableSwitch = false;

    /**
     * @var bool Whether to display "inventory" option for this field instance.
     */
    public $displayInventory = false;

    /**
     * @var bool Default "shippable" value.
     */
    public $defaultShippable = false;

    /**
     * @var bool Default "taxable" value.
     */
    public $defaultTaxable = false;

    /**
     * @var
     */
    public $defaultWeight;

    /**
     * @var
     */
    public $defaultWeightUnit;

    /**
     * @var
     */
    public $defaultLength;

    /**
     * @var
     */
    public $defaultWidth;

    /**
     * @var
     */
    public $defaultHeight;

    /**
     * @var
     */
    public $defaultDimensionsUnit;

    /**
     * @var string
     */
    public $skuDefault = '';

    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Product Details');
    }

    public static function hasContentColumn(): bool
    {
        return true;
    }

    public function init(): void
    {
        parent::init();
    }

    public function getContentColumnType(): array|string
    {
        return $this->columnType;
    }

    /**
     * Standardizes field values from several potential formats.
     *
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return Snipcart::$plugin->fields->getProductDetailsField(
            $this,
            $element,
            $value
        );
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value !== null) {
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    continue;
                }

                if ($v === null) {
                    continue;
                }

                if (is_string($v)) {
                    $value[$k] = LitEmoji::unicodeToShortcode($v);
                } elseif ($v instanceof \Stringable) {
                    $value[$k] = LitEmoji::unicodeToShortcode($v->__toString());
                } else {
                    $value[$k] = $v;
                }
            }
        }

        return $value;
    }

    /**
     * @since 3.3.0
     */
    public function getContentGqlType(): array
    {
        $typeName = $this->handle . '_SnipcartField';

        $productDetailsType = GqlEntityRegistry::getEntity($typeName)
            ?: GqlEntityRegistry::createEntity($typeName, new ObjectType([
                'name' => $typeName,
                'fields' => [
                    'sku' => Type::string(),
                    'price' => Type::float(),
                    'shippable' => Type::boolean(),
                    'taxable' => Type::boolean(),
                    'weight' => Type::float(),
                    'weightUnit' => Type::string(),
                    'length' => Type::float(),
                    'width' => Type::float(),
                    'height' => Type::float(),
                    'inventory' => Type::int(),
                    'dimensionsUnit' => Type::string(),
                ],
            ]));

        TypeLoader::registerType(
            $typeName,
            static fn(): mixed => $productDetailsType
        );

        return $productDetailsType;
    }

    public function getInputHtml(
        mixed $value,
        ?ElementInterface $element = null,
    ): string {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/field',
            [
                'name' => $this->handle,
                'field' => $this,
                'element' => $element,
                'value' => $value,
                'settings' => $this->getSettings(),
                'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
                'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
                'isCraft34' => VersionHelper::isCraft34(),
            ]
        );
    }

    public function getSettingsHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/settings',
            [
                'field' => $this,
                'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
                'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
            ]
        );
    }

    /**
     * Adds a validation rule for the element for validating each of the
     * "sub-fields" we’re working with.
     *
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            [
                ProductDetailsValidator::class,
            ],
        ];
    }
}

//class_alias(ProductDetails::class, \fostercommerce\snipcart\fields\ProductDetails::class);
