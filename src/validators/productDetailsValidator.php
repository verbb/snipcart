<?php


namespace fostercommerce\snipcart\validators;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\i18n\Locale;
use craft\elements\Entry;
use craft\helpers\ElementHelper;
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
        $fieldHandle = preg_replace('/^field:/', '', $attribute);  
			
		// don't like this but...
		// we are using a money field so when we try to save the value
		// there is an issue with the column type in the database (DECIMAL)
		// and it doesn't like the value being passed to it
		// so we remove the comma from the value
		// there's definitely a better way to do this ¯\_(ツ)_/¯
		$value['price'] = str_replace(',','',$value['price']);
		
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
		
		if(!$this->skuIsUnique($value['sku'], $sectionHandle, $fieldHandle)){
			$this->addError($model, $attribute, 'SKU must be unique');
		}

		
		
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
		
		/*
		$entryQuery = craft\elements\Entry::find()
			->section($sectionHandle);
			
		//$entryQuery->subQuery->andWhere(Db::parseParam($fieldHandle, $sku));
		$entryQuery->andWhere("${fieldHandle} = '${sku}'");	
		//$entryQuery->andWhere("'elementId' = 352");
		
		$entries = $entryQuery->count();
		*/
		
		$entries = craft\elements\Entry::find()
		->section($sectionHandle)->all();
		
		foreach ($entries as $entry) {
			if (
				// Make sure it's not a draft.
				!ElementHelper::isDraft($entry) &&
				// Make sure it's enabled globally and for the current site.
				($entry->enabled && $entry->getEnabledForSite()) &&
				// Make sure it's not a provisional draft
				!ElementHelper::rootElement($entry)->isProvisionalDraft &&
				// Make sure it's not a revision
				!ElementHelper::isRevision($entry)
			) 
				{
				if ($entry->$fieldHandle->sku == $sku) {
					return false;
				}
			}
		}

		return true;
	}
}

