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
 * ShipStation Webhook Model
 * https://www.shipstation.com/developer-api/#/reference/model-webhook
 */

class ShipStationWebhook extends Model
{
    // Constants
    // =========================================================================

    const TYPE_ORDER_NOTIFY      = 'ORDER_NOTIFY';
    const TYPE_ITEM_ORDER_NOTIFY = 'ITEM_ORDER_NOTIFY';
    const TYPE_SHIP_NOTIFY       = 'SHIP_NOTIFY';
    const TYPE_ITEM_SHIP_NOTIFY  = 'ITEM_SHIP_NOTIFY';


    // Properties
    // =========================================================================

    /**
     * @var string This URL can be used to get the resource which triggered the webhook. 200 character limit. The URL can be accessed with ShipStation API Basic Authentication credentials.
     */
    public $resource_url;

    /**
     * @var The event type that triggered the webhook. Will be one of the following values: ORDER_NOTIFY, ITEM_ORDER_NOTIFY, SHIP_NOTIFY, ITEM_SHIP_NOTIFY
     */
    public $resource_type;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['resource_url', 'resource_type'], 'string', 'max' => 200],
            [['resource_url'], 'url'],
            [['resource_type'], 'in', 'range' => [self::TYPE_ORDER_NOTIFY, self::TYPE_ITEM_ORDER_NOTIFY, self::TYPE_SHIP_NOTIFY, self::TYPE_ITEM_SHIP_NOTIFY]],
        ];
    }

}