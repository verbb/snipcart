<?php
namespace verbb\snipcart\models\snipcart;

/**
 * Class Tax
 * https://docs.snipcart.com/v2/webhooks/taxes
 *
 * @package verbb\snipcart\models
 */
class Tax extends \craft\base\Model
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $numberForInvoice;

}
