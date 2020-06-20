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
     * @var bool
     */
    public $notifyCustomer;

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
            $payload['modificationDate']
        );

        $payload['token'] = $this->orderToken;

        return $payload;
    }

}
