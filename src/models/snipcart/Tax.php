<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * Class Tax
 * https://docs.snipcart.com/v2/webhooks/taxes
 *
 * @package workingconcept\snipcart\models
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
