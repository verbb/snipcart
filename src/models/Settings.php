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
use workingconcept\snipcart\helpers\VersionHelper;

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
     * @var string Snipcart public test API key
     */
    public $publicTestApiKey = '';

    /**
     * @var string Snipcart secret API key
     */
    public $secretApiKey = '';

    /**
     * @var string Snipcart secret test API key
     */
    public $secretTestApiKey = '';

    /**
     * @var bool Whether to send an email notification to store admins if an
     *           order is completed and email addresses are specified
     */
    public $sendOrderNotificationEmail = false;
    
    /**
     * @var array Valid email addresses
     */
    public $notificationEmails = [];

    /**
     * @var string Optional path to a custom template to be used for admin order
     *             notification emails
     */
    public $notificationEmailTemplate = '';

    /**
     * @var bool Whether to send each customer a custom email notification after
     *           an order is completed
     */
    public $sendCustomerOrderNotificationEmail = false;

    /**
     * @var string Optional path to a custom template to be used for customer
     *             order notification emails
     */
    public $customerNotificationEmailTemplate = '';

    /**
     * @var string `usd`, `cad`, or `eur`. Defaults to `usd` unless another
     *             value is saved.
     */
    public $defaultCurrency;

    /**
     * @var array
     */
    public $enabledCurrencies = [ self::CURRENCY_USD ];

    /**
     * @var string Name of custom field sent to Snipcart for order gift notes
     */
    public $orderGiftNoteFieldName;

    /**
     * @var string Name of field sent to Snipcart for order comments
     */
    public $orderCommentsFieldName;

    /**
     * @var bool Whether to reduce inventory values for Product Details fields
     *           when orders are completed
     */
    public $reduceQuantitiesOnOrder = false;

    /**
     * @var bool Whether to cache GET responses and improve control panel
     *           performance
     */
    public $cacheResponses = true;

    /**
     * @var int Maximum number of seconds to keep cached GET responses
     */
    public $cacheDurationLimit = 300; // 5 minutes

    /**
     * @var bool Whether to log custom rate quotes for matching any ShipStation
     *           discrepancies between checkout and order completion
     */
    public $logCustomRates = false;

    /**
     * @var bool Whether to log received webhook requests for troubleshooting
     */
    public $logWebhookRequests = false;

    /**
     * @var Address Origin shipping address
     */
    private $_shipFrom;

    /**
     * @var array Used for storage of $_shipFrom
     */
    public $shipFromAddress = [];

    /**
     * @var array Key-value array of refHandle => instance of each
     *            registered provider
     */
    public $providers = [];

    /**
     * @var array Indexed array used for storing provider settings
     */
    public $providerSettings = [];


    // Static Methods
    // =========================================================================

    /**
     * Returns an indexed array of store currency options.
     *
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
     * Is the plugin ready to attempt Snipcart REST API requests?
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->_hasNonEmptyEnvValue('publicApiKey') &&
            $this->_hasNonEmptyEnvValue('secretApiKey');
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

    /**
     * Jumps into the validation flow and make sure provider and ship from
     * settings are agreeable.
     *
     * @inheritdoc
     */
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

    /**
     * Requires valid ship from Address if we're using any shipping providers.
     *
     * @return bool
     */
    public function validateShipFrom(): bool
    {
        if ($this->_hasEnabledProviders() && ! $this->getShipFrom()->validate())
        {
            $this->addError('shipFrom', 'Please enter required Ship From details.');
            return false;
        }

        return true;
    }

    /**
     * Validates shipping provider settings, which are basically like
     * sub-plugins with their own settings models.
     *
     * @return bool
     */
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
                        $this->addError(
                            'providerSettings',
                            'Provider settings are missing.'
                        );

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Grabs email addresses posted from a table input and reformat them before
     * this model is validated.
     *
     * @return bool
     */
    public function beforeValidate(): bool
    {
        $this->_getNotificationEmailsFromTable();
        return parent::beforeValidate();
    }

    /**
     * Formats an array of email addresses
     * (`['gob@bluth.com', 'george@bluth.com']`) for a control panel table input
     * (`[[0 => 'gob@bluth.com'], [0 => 'george@bluth.com']]`).
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
     * Gets the ship from address.
     *
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
     * Sets the ship from address.
     *
     * @param $address
     *
     * @return Address
     */
    public function setShipFrom($address): Address
    {
        return $this->_shipFrom = new Address($address);
    }

    /**
     * Gets the default (first listed) currency.
     *
     * @return string
     */
    public function getDefaultCurrency(): string
    {
        if ( ! empty($this->defaultCurrency))
        {
            return $this->defaultCurrency;
        }

        return $this->enabledCurrencies[0];
    }

    /**
     * Gets the symbol for the default currency.
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
     * Sets the array of enabled currencies to the supplied value, since we
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

    /**
     * Returns `true` if the setting has a value and that value isn't
     * simply an unparsed environment variable.
     *
     * @param $property
     * @return bool
     */
    private function _hasNonEmptyEnvValue($property): bool
    {
        // value stored on the model
        $settingValue = $this->{$property};

        if (empty($settingValue))
        {
            // null or false or empty string
            return false;
        }

        // first character is `@`
        $isAlias = $settingValue[0] === '@';

        // first character is `$`
        $isEnvVar = $settingValue[0] === '$';

        if ($isAlias || $isEnvVar)
        {
            // have Craft parse aliases and environment variables
            $parsedSettingValue = VersionHelper::isCraft31() ?
                Craft::parseEnv($settingValue) :
                Craft::getAlias($settingValue);

            // does the stored setting get expanded?
            $parses = $settingValue !== $parsedSettingValue;

            if ( ! $parses || empty($parsedSettingValue))
            {
                /**
                 * If it parsed to an empty value or is just
                 * an unparsed variable, don't consider that
                 * "non-empty"!
                 */
                return false;
            }
        }

        return true;
    }

    /**
     * Takes email addresses posted from a table input and formats them into a
     * clean, one-dimensional array.
     */
    private function _getNotificationEmailsFromTable()
    {
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

    /**
     * Did the posted settings include any enabled shipping providers?
     *
     * @return bool
     */
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

}
