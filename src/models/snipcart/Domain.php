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
 * https://docs.snipcart.com/v2/api-reference/domains
 */
class Domain extends Model
{
    /**
     * @var
     */
    public $domain;

    /**
     * @var
     */
    public $protocol;
}
