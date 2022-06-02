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
		// test for empty SKU
		if($value['sku'] === null || $value['sku'] ===''){
			$this->addError($model, $attribute, 'SKU cannot be blank');
		}
		// test for empty inventory
		if($value['inventory'] !== null && $value['inventory'] < 0){
			$this->addError($model, $attribute, 'Inventory cannot be less than 0');
		}
		// test for empty price
		if($value['price'] === null || $value['price'] ===''){
			$this->addError($model, $attribute, 'Price cannot be blank');
		}
		// test for negative price
		if($value['price'] < 0){
			$this->addError($model, $attribute, 'Price cannot be negative');
		}
		// test for non numeric price
		if($value['price'] !== null && !is_numeric($value['price'])){
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
	
	public function isNegative($value): bool
	{
		return $value < 0;
	}
	
	public function isInteger($value): bool
	{
		return is_int($value);
	}
}

