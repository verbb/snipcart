<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\base\Model;

class Settings extends Model
{
    // Constants
    // =========================================================================

    const PROVIDER_SHIPSTATION = 'shipStation';
    const PROVIDER_SHIPPO = 'shippo';


    // Properties
    // =========================================================================

    /**
     * @var string Snipcart public API key
     */
    public $publicApiKey = '';

    /**
     * @var string Snipcart secret API key
     */
    public $secretApiKey = '';

    /**
     * @var array valid email addresses
     */
    public $notificationEmails = [];

    /**
     * @var string
     */
    public $productIdentifier;

    /**
     * @var string
     */
    public $productInventoryField;

    /**
     * @var string Name of custom field sent to Snipcart for order gift notes.
     */
    public $orderGiftNoteFieldName;

    /**
     * @var string Name of field sent to Snipcart for order comments.
     */
    public $orderCommentsFieldName;

    /**
     * @var bool
     */
    public $reduceQuantitiesOnOrder = false;

    /**
     * @var bool
     */
    public $cacheResponses = true;

    /**
     * @var int
     */
    public $cacheDurationLimit = 300; // 5 minutes

    /**
     * @var bool
     */
    public $logCustomRates = false;

    /**
     * @var bool
     */
    public $logWebhookRequests = false;

    /**
     * @var SnipcartAddress
     */
    private $_shipFrom;

    public $shipFromAddress = [];

    /**
     * @var SnipcartPackage[]
     */
    private $_packagingTypes = [];

    public $customPackaging = [];

    /**
     * @var array
     */
    public $enabledProviders = [];

    /**
     * @var array
     */
    public $providers = [
        'shipStation' => [
            'apiKey' => '',
            'apiSecret' => '',
            'defaultCarrierCode' => '', // can be empty
            'defaultPackageCode' => '', // can be empty
            'defaultCountry' => 'US', // must be set
            'defaultWarehouseId' => 0, // must be set
            'defaultOrderConfirmation' => 'delivery', // must be set
        ],
        'shippo' => [
            'apiToken' => '',
        ],
    ];


    // Methods
    // =========================================================================

    public function rules()
    {
        return [
            [['publicApiKey', 'secretApiKey', 'productIdentifier', 'productInventoryField', 'orderGiftNoteFieldName', 'orderCommentsFieldName'], 'string'],
            [['publicApiKey', 'secretApiKey', 'productIdentifier'], 'required'],
            [['reduceQuantitiesOnOrder', 'cacheResponses', 'logCustomRates', 'logWebhookRequests'], 'boolean'],
            [['cacheDurationLimit'], 'number', 'integerOnly' => true],
            [['cacheDurationLimit'], 'default', 'value' => 300],
            [['reduceQuantitiesOnOrder'], 'default', 'value' => false],
            [['cacheResponses'], 'default', 'value' => true],
            [['logCustomRates'], 'default', 'value' => false],
            [['logWebhookRequests'], 'default', 'value' => false],
            ['notificationEmails', 'each', 'rule' => ['email']],
            [['enabledProviders'], 'in', 'range' => [
                self::SHIPPING_PROVIDER_SHIPSTATION,
                self::SHIPPING_PROVIDER_SHIPPO
            ]],

            // TODO: validate shipFrom
            // TODO: validate packagingTypes
        ];
    }

    public function beforeValidate()
    {        
        if (
            is_array($this->notificationEmails) && 
            count($this->notificationEmails) &&
            is_array($this->notificationEmails[0])
        )
        {
            $flattenedArrayFromTableData = [];

            foreach ($this->notificationEmails as $row)
            {
                $flattenedArrayFromTableData[] = $row[0];
            }

            $this->notificationEmails = $flattenedArrayFromTableData;
        }

        return true;
    }

    public function getNotificationEmailsForTable()
    {
        $rows = [];

        foreach ($this->notificationEmails as $email)
        {
            $rows[] = [
                0 => $email,
            ];
        }

        return $rows;
    }

    public function getPackagingTypes()
    {
        // use the customPackaging field that would've come from a static config
        if ( ! empty($this->customPackaging))
        {
            $this->setPackagingTypes($this->customPackaging);
        }
        
        return $this->_packagingTypes;
    }

    public function setPackagingTypes($packagingTypes)
    {
        foreach ($packagingTypes as $name => $values)
        {
            if ( ! is_a($values, SnipcartPackage::class))
            {
                $values['name'] = $name;
            
                $this->_packagingTypes[$name] = new SnipcartPackage($values);
            }
        }

        return $this->_packagingTypes;
    }

    public function getpackagingTypesForTable()
    {
        $rows = [];

        foreach ($this->packagingTypes as $name => $values)
        {
            $rows[] = [
                0 => $name,
                1 => $values->length,
                2 => $values->width,
                3 => $values->height,
                4 => $values->weight,
            ];
        }

        return $rows;
    }

    public function getShipFrom()
    {
        // use the customPackaging field that would've come from a static config
        if ( ! empty($this->shipFromAddress))
        {
            $this->setShipFrom($this->shipFromAddress);
        }

        return $this->_shipFrom;
    }

    public function setShipFrom($address)
    {
        return $this->_shipFrom = new SnipcartAddress($address);
    }

}
