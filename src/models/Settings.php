<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use craft\fields\Dropdown;
use craft\fields\Lightswitch;
use workingconcept\snipcart\fields\ProductDetails as ProductDetailsField;
use Craft;
use craft\base\Model;
use yii\base\InvalidConfigException;
use craft\fields\PlainText;
use craft\fields\Number;

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
     * @var string Snipcart public API key
     */
    public $publicTestApiKey = '';

    /**
     * @var string Snipcart secret API key
     */
    public $secretApiKey = '';

    /**
     * @var string Snipcart secret API key
     */
    public $secretTestApiKey = '';

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
     * @var string
     */
    public $productPriceField;

    /**
     * @var string
     */
    public $productWeightField;

    /**
     * @var string
     */
    public $productWeightUnitField;

    /**
     * @var string
     */
    public $productLengthField;

    /**
     * @var string
     */
    public $productWidthField;

    /**
     * @var string
     */
    public $productHeightField;

    /**
     * @var string
     */
    public $productDimensionsUnitField;

    /**
     * @var string
     */
    public $productShippableField;

    /**
     * @var string
     */
    public $productTaxableField;

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
    public $useCustomProductFields = false;

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


    // Static Methods
    // =========================================================================

    /**
     * @return array
     */
    public static function getCurrencyOptions(): array
    {
        return [
            self::CURRENCY_USD => Craft::t('snipcart', 'U.S. Dollar'),
            self::CURRENCY_CAD => Craft::t('snipcart','Canadian Dollar'),
            self::CURRENCY_EUR => Craft::t('snipcart','Euro'),
        ];
    }


    // Public Methods
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
//            [['enabledProviders'], 'in', 'range' => [
//                self::PROVIDER_SHIPSTATION,
//                self::PROVIDER_SHIPPO
//            ]],

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
    public function setShipFrom($address): Address
    {
        return $this->_shipFrom = new Address($address);
    }

    /**
     * Get the default (first listed) currency.
     *
     * @return string
     */
    public function getDefaultCurrency(): string
    {
        return $this->enabledCurrencies[0];
    }

    /**
     * Get the symbol for the default currency.
     *
     * @return string
     */
    public function getDefaultCurrencySymbol(): string
    {
        if (
            $this->getDefaultCurrency() === self::CURRENCY_USD ||
            $this->getDefaultCurrency() === self::CURRENCY_CAD
        )
        {
            return '$';
        }

        if ($this->getDefaultCurrency() === self::CURRENCY_EUR)
        {
            return '€';
        }

        return '';
    }

    /**
     * Set the array of enabled currencies to the supplied value, since we
     * don't yet support setting multiple currencies.
     *
     * @param $value
     * @return array
     */
    public function setCurrency($value): array
    {
        return $this->enabledCurrencies = [ $value ];
    }

    /**
     * Return established Craft field instances that can be used for the provided
     * Snipcart product field.
     *
     * @param $productFieldName
     * @return array Options for selectField
     * @throws InvalidConfigException
     */
    public function getFieldOptionsForProduct($productFieldName): array
    {
        return $this->_getSupportedFieldTypeOptionsForField(
            $productFieldName
        );
    }

    // Private Methods
    // =========================================================================

    /**
     * Return class names of fields that can be used as options for the provided
     * Settings field.
     *
     * @param $fieldName
     * @return array
     * @throws InvalidConfigException
     */
    private function _getSupportedFieldTypeOptionsForField($fieldName): array
    {
        $allFields = Craft::$app->fields->getAllFields();

        $supportedMap = [
            'productIdentifier' => [
                ProductDetailsField::class,
                PlainText::class,
                Number::class,
            ],
            'productInventoryField' => [
                Number::class,
            ],
            'productPriceField' => [
                Number::class,
            ],
            'productWeightField' => [
                Number::class,
            ],
            'productWeightUnitField' => [
                Dropdown::class,
                PlainText::class,
            ],
            'productLengthField' => [
                Number::class,
            ],
            'productWidthField' => [
                Number::class,
            ],
            'productHeightField' => [
                Number::class,
            ],
            'productDimensionsUnitField' => [
                Dropdown::class,
                PlainText::class,
            ],
            'productShippableField' => [
                Lightswitch::class,
            ],
            'productTaxableField' => [
                Lightswitch::class,
            ],
        ];

        if ( ! array_key_exists($fieldName, $supportedMap))
        {
            throw new InvalidConfigException(
                'Cannot get options for `' . $fieldName .'` field.`'
            );
        }

        $availableOptions = [];
        $supportedFieldClasses = $supportedMap[$fieldName];

        if ($fieldName === 'productIdentifier')
        {
            $availableOptions['id'] = 'Element ID';
        }
        else
        {
            $availableOptions[] = 'Choose …';
        }

        foreach ($allFields as $field)
        {
            if (in_array(get_class($field), $supportedFieldClasses, true))
            {
                // disallow multiline text as an option
                if (isset($field->multiline) && $field->multiline)
                {
                    continue;
                }

                $availableOptions[$field->handle] = $field->name;
            }
        }

        return $availableOptions;
    }

}
