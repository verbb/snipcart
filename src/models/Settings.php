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
 * Settings model
 *
 * @package workingconcept\snipcart\models
 * @property Address $shipFrom
 */
class Settings extends Model
{
    // Constants
    // =========================================================================

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
     * @var bool
     */
    public $sendOrderNotificationEmail = false;
    
    /**
     * @var array valid email addresses
     */
    public $notificationEmails = [];

    /**
     * @var string optional path to a custom template to be used for admin order
     *             notification emails
     */
    public $notificationEmailTemplate = '';

    /**
     * @var bool
     */
    public $sendCustomerOrderNotificationEmail = false;

    /**
     * @var string optional path to a custom template to be used for customer
     *             order notification emails
     */
    public $customerNotificationEmailTemplate = '';

    /**
     * @var array
     */
    public $enabledCurrencies = [ self::CURRENCY_USD ];

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
     * @var array Used for storage of $_shipFrom
     */
    public $shipFromAddress = [];

    /**
     * @var array Key-value array of refHandle => instance of each registered provider.
     */
    public $providers = [];

    /**
     * @var array Indexed array used for storing provider settings.
     */
    public $providerSettings = [];


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

    public function isConfigured(): bool
    {
        return ! empty($this->publicApiKey) &&
            ! empty($this->secretApiKey);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['publicApiKey', 'secretApiKey', 'orderGiftNoteFieldName', 'orderCommentsFieldName'], 'string'],
            [['publicApiKey', 'secretApiKey'], 'required'],
            [['reduceQuantitiesOnOrder', 'cacheResponses', 'logCustomRates', 'logWebhookRequests'], 'boolean'],
            [['cacheDurationLimit'], 'number', 'integerOnly' => true],
            [['cacheDurationLimit'], 'default', 'value' => 300],
            [['reduceQuantitiesOnOrder'], 'default', 'value' => false],
            [['cacheResponses'], 'default', 'value' => true],
            [['logCustomRates'], 'default', 'value' => false],
            [['logWebhookRequests'], 'default', 'value' => false],
            ['notificationEmails', 'each', 'rule' => ['email']],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        $validates = parent::validate($attributeNames, $clearErrors);

        if ($this->_hasEnabledProviders())
        {
            if ( ! $this->validateProviderSettings())
            {
                $validates = false;
            }

            if ( ! $this->validateShipFrom())
            {
                $validates = false;
            }
        }

        return $validates;
    }

    private function _hasEnabledProviders(): bool
    {
        $request = Craft::$app->getRequest();

        if ($providers = $request->getBodyParam('providers'))
        {
            foreach ($providers as $handle => $settings)
            {
                if ($settings['enabled'])
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function validateShipFrom(): bool
    {
        if ($this->_hasEnabledProviders() && ! $this->getShipFrom()->validate())
        {
            $this->addError('shipFrom', 'Please enter required Ship From details.');
            return false;
        }

        return true;
    }

    public function validateProviderSettings(): bool
    {
        $request = Craft::$app->getRequest();

        if ($this->_hasEnabledProviders())
        {
            foreach ($this->providers as $provider)
            {
                if ($request->getBodyParam('providers')[$provider->refHandle()]['enabled'])
                {
                    if (! $provider->getSettings()->validate())
                    {
                        $this->addError('providerSettings', 'Provider settings are missing.');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function beforeValidate(): bool
    {
        $this->_getNotificationEmailsFromTable();
        return parent::beforeValidate();
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

        if (empty($this->notificationEmails))
        {
            return $rows;
        }

        foreach ($this->notificationEmails as $email)
        {
            $rows[] = [
                0 => $email,
            ];
        }

        return $rows;
    }

    /**
     * @return Address|null
     */
    public function getShipFrom()
    {
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


    // Private Methods
    // =========================================================================

    private function _getNotificationEmailsFromTable()
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
    }

}
