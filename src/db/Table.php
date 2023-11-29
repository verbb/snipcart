<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2020 Working Concept Inc.
 */

namespace fostercommerce\snipcart\db;

abstract class Table
{
    public const WEBHOOK_LOG = '{{%snipcart_webhook_log}}';

    public const SHIPPING_QUOTES = '{{%snipcart_shipping_quotes}}';

    public const PRODUCT_DETAILS = '{{%snipcart_product_details}}';
}
