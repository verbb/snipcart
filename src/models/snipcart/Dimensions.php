<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models\snipcart;

use craft\base\Model;

class Dimensions extends Model
{
    /**
     * @var
     */
    public $width;

    /**
     * @var
     */
    public $height;

    /**
     * @var
     */
    public $length;

    /**
     * @var
     */
    public $weight;
}
