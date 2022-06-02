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
use fostercommerce\snipcart\validators\ProductDetailsValidator;
use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Db;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use yii\base\UnknownPropertyException;
use yii\db\Schema;
use LitEmoji\LitEmoji;

/**
 * ProductDetails
 *
 * @property ProductDetails $value
 */
class ProductDetails extends Field
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
        return true;
    }
    
    
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
        'dimensionsUnit' => Schema::TYPE_STRING
    ];

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
    
    
    public function init(): void
    {
        parent::init();
        
    }
    
    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {   
        $rules = parent::defineRules();
        //$rules[] = ['sku', 'required'];
        //$rules[] = [['field:inventory'], 'integer', 'min' => 0];
        return $rules;
    }
    
    /**
     * @inheritdoc
     */
    public function getContentColumnType(): array|string
    {
        return $this->columnType;
    }
    
    

    /**
     * Standardizes field values from several potential formats.
     *
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null): mixed
    {
        /*
        return Snipcart::$plugin->fields->getProductDetailsField(
            $this,
            $element,
            $value
        );
        */
        return $value;
    }
    
    
   /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null): mixed
    {
        if ($value !== null) {
            foreach($value as $k => $v){
                $value[$k] = LitEmoji::unicodeToShortcode($v);
            }
        }
        return $value;
    }

   

    /**
     * @inheritdoc
     * @since 3.3.0
     */
    public function getContentGqlType(): array
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
    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
      
        return [
            [
                ProductDetailsValidator::class
            ],
        ];
    }

    /**
     * Validates the ProductDetails model, adding errors to the Element.
     *
     * @param  ElementInterface  $element
     */
    public function validateProductDetails(ElementInterface $element): void
    {   
        $productDetails = $element->getFieldValue($this->handle);
    
        
        /*
        $productDetails = $element->getFieldValue($this->handle);
        
        if ($element->isFieldDirty($this->handle)) {
            // first normalize a new value that came from the control panel
            $productDetails->price = Localization::normalizeNumber($productDetails->price);
        }
        
        //$productDetails->validate();
       foreach($productDetails as $k => $v){
            $this->_validateSubField($k, $v);
        }
        
        
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
        */
    }
    
    
    private function _validateSubField(string $type, $value, string &$error = null): bool
    {
        
        if ($value === null || $value === '') {
            return true;
        }
        
        /*
        switch ($type) {
            case 'sku':
                $validator = new skuValidator();
                break;
            case 'inventory':
                $validator = new inventoryValidator();
                break;
            default:
                return true;
        }
        */
        
        $validator->message = str_replace('{attribute}', '{value}', $validator->message);
        return $validator->validate($value, $error);
    }

    
}

class_alias(ProductDetails::class, \workingconcept\snipcart\fields\ProductDetails::class);
