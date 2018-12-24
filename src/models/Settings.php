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
 * Settings model
 *
 * @package workingconcept\snipcart\models
 * @property Address $shipFrom
 * @property Package[] $packagingTypes
 */
class Settings extends Model
{
    // Constants
    // =========================================================================

    const PROVIDER_SHIPSTATION = 'shipStation';
    const PROVIDER_SHIPPO = 'shippo';

    const CURRENCY_USD = 'usd';
    const CURRENCY_CAD = 'cad';
    const CURRENCY_EUR = 'eur';


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
     * @var array
     */
    public $enabledCurrencies = [ self::CURRENCY_USD ];

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
     * @var Address
     */
    private $_shipFrom;

    /**
     * @var array
     */
    public $shipFromAddress = [];

    /**
     * @var Package[]
     */
    private $_packagingTypes = [];

    /**
     * @var array
     */
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

    /**
     * @inheritdoc
     */
    public function rules(): array
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
                self::PROVIDER_SHIPSTATION,
                self::PROVIDER_SHIPPO
            ]],

            // TODO: validate shipFrom
            // TODO: validate packagingTypes
        ];
    }

    public function beforeValidate(): bool
    {
        /**
         * If the `notificationEmails` value came from a table in the settings UI,
         * convert it to a clean, one-dimensional array of email addresses.
         */
        if (
            is_array($this->notificationEmails) && 
            count($this->notificationEmails) &&
            is_array($this->notificationEmails[0])
        )
        {
            $arrayFromTableData = [];

            foreach ($this->notificationEmails as $row)
            {
                $arrayFromTableData[] = trim($row[0]);
            }

            $this->notificationEmails = $arrayFromTableData;
        }

        return true;
    }

    /**
     * Format an array of email addresses (`['gob@bluth.com', 'george@bluth.com']`) for the table in
     * the control panel settings (`[[0 => 'gob@bluth.com'], [0 => 'george@bluth.com']]`).
     *
     * @return array
     */
    public function getNotificationEmailsForTable(): array
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

    /**
     * Get custom packaging type definitions.
     *
     * @return Package[]
     */
    public function getPackagingTypes(): array
    {
        // use the customPackaging field that would've come from a static config
        if ( ! empty($this->customPackaging))
        {
            $this->setPackagingTypes($this->customPackaging);
        }
        
        return $this->_packagingTypes;
    }

    /**
     * @param $packagingTypes
     *
     * @return Package[]
     */
    public function setPackagingTypes($packagingTypes): array
    {
        foreach ($packagingTypes as $name => $values)
        {
            if ( ! is_a($values, Package::class))
            {
                $values['name'] = $name;
            
                $this->_packagingTypes[$name] = new Package($values);
            }
        }

        return $this->_packagingTypes;
    }

    /**
     * Convert custom packaging types into a multi-dimensional array for the control panel's settings UI.
     *
     * @return array
     */
    public function getPackagingTypesForTable(): array
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

    /**
     * @return Address
     */
    public function getShipFrom(): Address
    {
        // use the customPackaging field that would've come from a static config
        if ( ! empty($this->shipFromAddress))
        {
            $this->setShipFrom($this->shipFromAddress);
        }

        return $this->_shipFrom;
    }

    /**
     * @param $address
     *
     * @return Address
     */
    public function setShipFrom($address)
    {
        return $this->_shipFrom = new Address($address);
    }

}
