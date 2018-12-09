<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

/**
 * https://docs.snipcart.com/api-reference/abandoned-carts
 */

class SnipcartAbandonedCart extends Model
{

    // Properties
    // =========================================================================

    /**
     * @var
     */
    public $token;

    /**
     * @var string
     */
    public $email;

    /**
     * @var
     */
    public $mode;

    /**
     * @var
     */
    public $status;

    /**
     * @var
     */
    public $shipToBillingAddress;

    /**
     * @var
     */
    public $billingAddress;

    /**
     * @var
     */
    public $modificationDate;

    /**
     * @var
     */
    public $shippingAddress;

    /**
     * @var
     */
    public $completionDate;

    /**
     * @var
     */
    public $invoiceNumber;
    /*
    "shippingInformation": {
      "provider": null,
      "fees": 10,
      "method": "Fast custom shipping"
    },
    */
    public $paymentMethod;
    /*
    summary": {
      "subtotal": 20,
      "taxableTotal": 20,
      "total": 30,
      "paymentMethod": 0,
      "taxes": [],
      "adjustedTotal": 30
    },
    */

    /**
     * @var
     */
    public $metadata;

    /**
     * @var
     */
    public $items;

    /**
     * @var
     */
    public $discounts;

    /**
     * @var
     */
    public $customFields;

    /**
     * @var
     */
    public $plans;

    /**
     * @var
     */
    public $refunds;

    /**
     * @var
     */
    public $currency;

    /**
     * @var
     */
    public $totalWeight;

    /**
     * @var
     */
    public $total;
}
