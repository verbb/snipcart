<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

/**
 * Class ShippingQuoteLog
 *
 * @package workingconcept\snipcart\models
 */
class ShippingQuoteLog extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $body;

    /**
     * @var
     */
    public $dateCreated;

    /**
     * @var
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;
}
