<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\fields;

use craft\helpers\Localization;
use fostercommerce\snipcart\db\Table;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\Snipcart;
use fostercommerce\snipcart\models\ProductDetails as ProductDetailsModel;
use fostercommerce\snipcart\assetbundles\ProductDetailsFieldAsset;
use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Db;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use yii\base\UnknownPropertyException;

/**
 * ProductDetails
 *
 * @property ProductDetails $value
 */
class ProductDetails extends \craft\base\Field
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Product Details');
    }

    /**
     * @return bool
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @var bool Whether to display "shippable" option for this field instance
     *           and allow it to be set per entry.
     */
    public $displayShippableSwitch = false;

    /**
     * @var bool Whether to display "taxable" option for this field instance
     *           and allow it to be set per entry.
     *
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

    /**
     * Saves the Product Details data to table after element save.
     *
     * @inheritdoc
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        if ($element->isFieldDirty($this->handle)) {
            Snipcart::$plugin->fields->saveProductDetailsField(
                $this,
                $element
            );
        }
  
        parent::afterElementSave($element, $isNew);
    }

    /**
     * Saves the Product Details data to table after element propagation.
     *
     * @inheritdoc
     */
    public function afterElementPropagate(ElementInterface $element, bool $isNew)
    {
        if ($element->isFieldDirty($this->handle)) {
            Snipcart::$plugin->fields->saveProductDetailsField(
                $this,
                $element
            );
        }

        parent::afterElementPropagate($element, $isNew);
    }

    /**
     * Standardizes field values from several potential formats.
     *
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Snipcart::$plugin->fields->getProductDetailsField(
            $this,
            $element,
            $value
        );
    }

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, $value)
    {
        $queryable = [
            'sku',
            'price',
            'shippable',
            'taxable',
            'weight',
            'length',
            'width',
            'height',
            'inventory'
        ];

        $subQueries = [];

        if ($value !== null) {
            if (! is_array($value)) {
                return false;
            }

            foreach ($value as $key => $val) {
                if (! in_array($key, $queryable, false)) {
                    throw new UnknownPropertyException(
                        'Setting unknown property: ' . get_class($this) . '::' . $key
                    );
                }

                $subQueries['snipcart_product_details.' . $key] = $value;
            }

            if (count($subQueries) > 0) {
                /** @var ElementQuery $query */
                $query->subQuery->innerJoin(
                    Table::PRODUCT_DETAILS . ' snipcart_product_details',
                    '[[snipcart_product_details.elementId]] = [[elements.id]]'
                );

                $query->subQuery->andWhere(
                    Db::parseParam('snipcart_product_details.fieldId', $this->id)
                );

                foreach ($subQueries as $column => $val) {
                    $query->subQuery->andWhere(Db::parseParam($column, $val));
                }
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     * @since 3.3.0
     */
    public function getContentGqlType()
    {
        $typeName = $this->handle.'_SnipcartField';

        $productDetailsType = GqlEntityRegistry::getEntity($typeName)
            ?: GqlEntityRegistry::createEntity($typeName, new ObjectType([
                'name'   => $typeName,
                'fields' => [
                    'sku'            => Type::string(),
                    'price'          => Type::float(),
                    'shippable'      => Type::boolean(),
                    'taxable'        => Type::boolean(),
                    'weight'         => Type::float(),
                    'weightUnit'     => Type::string(),
                    'length'         => Type::float(),
                    'width'          => Type::float(),
                    'height'         => Type::float(),
                    'inventory'      => Type::int(),
                    'dimensionsUnit' => Type::string(),
                ],
            ]));

        TypeLoader::registerType(
            $typeName,
            static function () use ($productDetailsType) {
                return $productDetailsType;
            }
        );

        return $productDetailsType;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml(
        $value,
        ElementInterface $element = null
    ): string {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        
        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/field',
            [
                'name'                  => $this->handle,
                'field'                 => $this,
                'element'               => $element,
                'value'                 => $value,
                'settings'              => $this->getSettings(),
                'weightUnitOptions'     => ProductDetailsModel::getWeightUnitOptions(),
                'dimensionsUnitOptions' => ProductDetailsModel::getDimensionsUnitOptions(),
                'isCraft34'             => VersionHelper::isCraft34()
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(
            ProductDetailsFieldAsset::class
        );

        return Craft::$app->getView()->renderTemplate(
            'snipcart/fields/product-details/settings',
            [
                'field'                 => $this,
                'weightUnitOptions'     => ProductDetailsModel::getWeightUnitOptions(),
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
            'validateProductDetails',
        ];
    }

    /**
     * Validates the ProductDetails model, adding errors to the Element.
     *
     * @param  ElementInterface  $element
     */
    public function validateProductDetails(ElementInterface $element)
    {
        $productDetails = $element->getFieldValue($this->handle);

        if ($element->isFieldDirty($this->handle)) {
            // first normalize a new value that came from the control panel
            $productDetails->price = Localization::normalizeNumber($productDetails->price);
        }

        $productDetails->validate();

        $errors = $productDetails->getErrors();

        if (count($errors) > 0) {
            foreach ($errors as $subfield => $subErrors) {
                foreach ($subErrors as $message) {
                    $element->addError(
                        $this->handle.'['.$subfield.']',
                        $message
                    );
                }
            }
        }
    }
}

class_alias(ProductDetails::class, \workingconcept\snipcart\fields\ProductDetails::class);
