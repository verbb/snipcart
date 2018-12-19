<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

class Refund extends \craft\base\Model
{
    // Properties
    // =========================================================================

    /**
     * @var string "2223490d-84c1-480c-b713-50cb0b819313"
     */
    public $id;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var bool
     */
    public $refundedByPaymentGateway;

    // Public Methods
    // =========================================================================

}
