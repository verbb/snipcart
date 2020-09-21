<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */

namespace workingconcept\snipcart\db;

abstract class Table
{
    const WEBHOOK_LOG = '{{%snipcart_webhook_log}}';
    const SHIPPING_QUOTES = '{{%snipcart_shipping_quotes}}';
    const PRODUCT_DETAILS = '{{%snipcart_product_details}}';
}