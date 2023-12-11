<?php
namespace verbb\snipcart\models;

use craft\base\Model;

/**
 * Class ShippingQuoteLog
 *
 * @package verbb\snipcart\models
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
