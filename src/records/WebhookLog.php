<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\records;

use craft\db\ActiveRecord;
use workingconcept\snipcart\db\Table;

/**
 * Class WebhookLog
 *
 * @package workingconcept\snipcart\records
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
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return Table::WEBHOOK_LOG;
    }
}
