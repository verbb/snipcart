<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

class Refund extends \craft\base\Model
{
    /**
     * @var string The refund's unique identifier.
     */
    public $id;

    /**
     * @var string The order's unique identifier.
     */
    public $orderToken;

    /**
     * @var float The amount of the refund.
     */
    public $amount;

    /**
     * @var string The reason for the refund.
     */
    public $comment;

    /**
     * @var bool
     */
    public $refundedByPaymentGateway;

    /**
     * @var bool Whether the customer should be notified if this is a new refund.
     */
    public $notifyCustomer;

    /**
     * @var bool Whether or not the customer has been notified by email about the refund.
     */
    public $notifiedCustomerByEmail;

    /**
     * @var string The currency of the order that is getting refunded.
     */
    public $currency;

    /**
     * @var
     */
    public $creationDate;

    /**
     * @var
     */
    public $modificationDate;

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate'];
    }

    /**
     * @return array
     */
    public function getPayloadForPost(): array
    {
        $payload = $this->toArray();

        unset(
            $payload['id'],
            $payload['orderToken'],
            $payload['refundedByPaymentGateway'],
            $payload['creationDate'],
            $payload['modificationDate'],
            $payload['notifiedCustomerByEmail'],
            $payload['currency'],
        );

        $payload['token'] = $this->orderToken;

        return $payload;
    }

}
