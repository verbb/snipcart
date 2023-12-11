<?php
namespace verbb\snipcart\fields;

use verbb\snipcart\Snipcart;
use verbb\snipcart\assetbundles\ProductDetailsFieldAsset;
use verbb\snipcart\models\ProductDetails as ProductDetailsModel;
use verbb\snipcart\validators\ProductDetailsValidator;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

use yii\db\Schema;

use LitEmoji\LitEmoji;
use Stringable;

class ProductDetails extends Field
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Product Details');
    }

    // Properties
    // =========================================================================

    public array $columnType = [
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

    public bool $displayShippableSwitch = false;
    public bool $displayTaxableSwitch = false;
    public bool $displayInventory = false;
    public bool $defaultShippable = false;
    public bool $defaultTaxable = false;
    public ?string $defaultWeight = null;
    public ?string $defaultWeightUnit = null;
    public ?string $defaultLength = null;
    public ?string $defaultWidth = null;
    public ?string $defaultHeight = null;
    public ?string $defaultDimensionsUnit = null;
    public string $skuDefault = '';


    // Public Methods
    // =========================================================================

    public function getContentColumnType(): array|string
    {
        return $this->columnType;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return Snipcart::$plugin->getFields()->getProductDetailsField($this, $element, $value);
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
                } else if ($v instanceof Stringable) {
                    $value[$k] = LitEmoji::unicodeToShortcode($v->__toString());
                } else {
                    $value[$k] = $v;
                }
            }
        }

        return $value;
    }

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

        TypeLoader::registerType($typeName, static fn(): mixed => $productDetailsType);

        return $productDetailsType;
    }

    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        Craft::$app->getView()->registerAssetBundle(ProductDetailsFieldAsset::class);

        return Craft::$app->getView()->renderTemplate('snipcart/fields/product-details/field', [
            'name' => $this->handle,
            'field' => $this,
            'element' => $element,
            'value' => $value,
            'settings' => $this->getSettings(),
            'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
            'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
        ]);
    }

    public function getSettingsHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(ProductDetailsFieldAsset::class);

        return Craft::$app->getView()->renderTemplate('snipcart/fields/product-details/settings', [
            'field' => $this,
            'weightUnitOptions' => ProductDetailsModel::getWeightUnitOptions(),
            'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
        ]);
    }

    public function getElementValidationRules(): array
    {
        return [[ProductDetailsValidator::class]];
    }
}
