<?php


namespace fostercommerce\snipcart\validators;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\i18n\Locale;
use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * Class ProductDetailsValidator.
 *
 *
 * @author Foster Commerce
 * @since 1.5.7
 */
class ProductDetailsValidator extends Validator
{
  

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute): void
    {

		
        $value = $model->$attribute;
		
		
		$sectionHandle = $model->section->handle;
		
		// Remove prefix from field handle
        //$fieldHandle = preg_replace('/^field:/', '', $attribute);  
		

	
		
		/* SKU field validations */
		// test for empty SKU
		if($value['sku'] === null || trim($value['sku']) ===''){
			$this->addError($model, $attribute, 'SKU cannot be blank');
		}
		// test for unique SKU
		// query for all product details SKU fields
		
		/*
		if(!$model->$attribute->validateSku('sku')){
			$this->addError($model, $attribute, 'SKU must be unique');
		}
		*/
		
		
		/* Inventory field validations */
		
		if($value['inventory'] !== null){
			if($value['inventory'] < 0){
				$this->addError($model, $attribute, 'Inventory cannot be less than 0');
			} elseif(!is_numeric($value['inventory'])){
				$this->addError($model, $attribute, 'Inventory must be a number');
			}
		}
	
	
		/* Price field validations */
		if($value['price'] === null || trim($value['price'] ==='')){
			$this->addError($model, $attribute, 'Price cannot be blank');
		} elseif($value['price'] !== null && $value['price'] < 0){
			$this->addError($model, $attribute, 'Price cannot be negative');
		} elseif($value['price'] !== null && !is_numeric($value['price'])){
			$this->addError($model, $attribute, 'Price must be numeric');
		}
		
	}

    public function isEmpty($value): bool
    {
        if ($this->isEmpty !== null) {
            return parent::isEmpty($value);
        }

        return empty($value);
    }
	
	
	public function skuIsUnique($sku, $sectionHandle, $fieldHandle): bool
	{
		
		
		/*
		$entryQuery = craft\elements\Entry::find()
			->section($sectionHandle)
			->where(["field_${fieldHandle}_mduolzrl" => $sku]);
			
		$entries = $entryQuery->count();
		*/
		
		$entryQuery = craft\elements\Entry::find()
			->section($sectionHandle);
			
		$entryQuery->subQuery->andWhere(Db::parseParam($fieldHandle, $sku));
			
		$entries = $entryQuery->count();
		
		return !$entries;
	}
}

