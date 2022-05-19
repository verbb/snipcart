<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\records;

use craft\db\ActiveRecord;
use fostercommerce\snipcart\db\Table;

/**
 * Class WebhookLog
 *
 * @package fostercommerce\snipcart\records
 *
 * @property int    $id
 * @property int    $siteId
 * @property string $type   The webhook event name.
 * @property string $body   The posted payload.
 * @property string $mode   An enum that can either be 'live' or 'test'.
 *                          Note that we store in lowercase, while Snipcart
 *                          sends `Live` or `Test`.
 */
class WebhookLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return Table::WEBHOOK_LOG;
    }
}
