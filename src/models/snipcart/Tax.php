<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;
/**
 * Class Tax
 * https://docs.snipcart.com/v2/webhooks/taxes
 *
 * @package fostercommerce\snipcart\models
 */
class Tax extends Model
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
