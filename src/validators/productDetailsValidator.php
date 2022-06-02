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
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
   
	if($value['sku'] === null || $value['sku'] ===''){
		$this->addError($model, $attribute, 'SKU cannot be blank');
	}
       
    }

    public function isEmpty($value)
    {
        if ($this->isEmpty !== null) {
            return parent::isEmpty($value);
        }

        return empty($value);
    }
}
