<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\models;

use Craft;
use craft\base\Model;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\models\snipcart\Address;

/**
 * Settings model
 *
 * @package fostercommerce\snipcart\models
 * @property Address $shipFrom
 */
class Settings extends Model
{
    public const CURRENCY_USD = 'usd';

    public const CURRENCY_CAD = 'cad';

    public const CURRENCY_EUR = 'eur';

    public const CURRENCY_GBP = 'gbp';

    public const CURRENCY_CHF = 'chf';

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
    public $enabledCurrencies = [self::CURRENCY_USD];

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
     * @var array Used for storage of
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

    /**
     * @var int Limit in minutes for re-sending order details to shipping
     *          providers after failure. Once the order is X minutes old,
     *          the plugin will stop trying to re-send the data.
     */
    public $reFeedAttemptWindow = 15;

    /**
     * @var boolean Whether to use `publicApiKey` and `secretApiKey` or
     *              `publicTestApiKey` and `secretTestApiKey` for API
     *              interactions with Snipcart.
     */
    public $testMode = false;

    /**
     * @var boolean Whether to send email notifications when `testMode` = true.
     */
    public $sendTestModeEmail = false;

    /**
     * @var Address Origin shipping address
     */
    private ?Address $_shipFrom = null;

    /**
     * @var array Key-value array of refHandle => instance of each
     *            registered provider
     */
    private array $_providers = [];

    /**
     * Returns an indexed array of store currency options.
     */
    public static function getCurrencyOptions(): array
    {
        return [
            self::CURRENCY_USD => Craft::t('snipcart', 'U.S. Dollar'),
            self::CURRENCY_CAD => Craft::t('snipcart', 'Canadian Dollar'),
            self::CURRENCY_EUR => Craft::t('snipcart', 'Euro'),
            self::CURRENCY_GBP => Craft::t('snipcart', 'Pound Sterling'),
            self::CURRENCY_CHF => Craft::t('snipcart', 'Swiss Franc'),
        ];
    }

    /**
     * Returns an indexed array of store currency options.
     */
    public static function getCurrencySymbols(): array
    {
        return [
            self::CURRENCY_USD => Craft::t('snipcart', '$'),
            self::CURRENCY_CAD => Craft::t('snipcart', '$'),
            self::CURRENCY_EUR => Craft::t('snipcart', '€'),
            self::CURRENCY_GBP => Craft::t('snipcart', '£'),
            self::CURRENCY_CHF => Craft::t('snipcart', 'CHF'),
        ];
    }

    /**
     * Returns the stored public API key value depending on testMode.
     */
    public function publicKey(): string
    {
        return $this->testMode ? $this->publicTestApiKey : $this->publicApiKey;
    }

    /**
     * Returns the stored secret API key value depending on testMode.
     */
    public function secretKey(): string
    {
        return $this->testMode ? $this->secretTestApiKey : $this->secretApiKey;
    }

    /**
     * Is the plugin ready to attempt Snipcart REST API requests with
     * honoring our `testMode` setting?
     */
    public function isConfigured(): bool
    {
        if ($this->testMode) {
            return $this->hasNonEmptyEnvValue('publicTestApiKey') &&
                $this->hasNonEmptyEnvValue('secretTestApiKey');
        }

        return $this->hasNonEmptyEnvValue('publicApiKey') &&
            $this->hasNonEmptyEnvValue('secretApiKey');
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['publicApiKey', 'secretApiKey', 'publicTestApiKey', 'secretTestApiKey', 'orderGiftNoteFieldName', 'orderCommentsFieldName'], 'string'],
            [['publicApiKey', 'secretApiKey'], 'required'],
            [['cacheDurationLimit'],
                'number',
                'integerOnly' => true,
            ],
            [
                'notificationEmails',
                'each',
                'rule' => ['email'],
            ],
        ]);
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

        if ($this->hasEnabledProviders()) {
            if (! $this->validateProviderSettings()) {
                $validates = false;
            }

            if (! $this->validateShipFrom()) {
                $validates = false;
            }
        }

        return $validates;
    }

    /**
     * Requires valid ship from Address if we're using any shipping providers.
     */
    public function validateShipFrom(): bool
    {
        if ($this->hasEnabledProviders() && ! $this->getShipFrom()->validate()) {
            $this->addError('shipFrom', 'Please enter required Ship From details.');
            return false;
        }

        return true;
    }

    /**
     * Validates shipping provider settings, which are basically like
     * sub-plugins with their own settings models.
     */
    public function validateProviderSettings(): bool
    {
        $request = Craft::$app->getRequest();

        if ($this->hasEnabledProviders()) {
            foreach ($this->getProviders() as $provider) {
                if ($request->getBodyParam('providers')[$provider->refHandle()]['enabled'] && ! $provider->getSettings()->validate()) {
                    $this->addError(
                        'providerSettings',
                        'Provider settings are missing.'
                    );
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Grabs email addresses posted from a table input and reformat them before
     * this model is validated.
     */
    public function beforeValidate(): bool
    {
        $this->getNotificationEmailsFromTable();
        return parent::beforeValidate();
    }

    /**
     * Formats an array of email addresses
     * (`['gob@bluth.com', 'george@bluth.com']`) for a control panel table input
     * (`[[0 => 'gob@bluth.com'], [0 => 'george@bluth.com']]`).
     */
    public function getNotificationEmailsForTable(): array
    {
        $rows = [];

        if (empty($this->notificationEmails)) {
            return $rows;
        }

        foreach ($this->notificationEmails as $notificationEmail) {
            $rows[] = [
                0 => $notificationEmail,
            ];
        }

        return $rows;
    }

    /**
     * Gets the ship from address.
     */
    public function getShipFrom(): ?Address
    {
        if ($this->shipFromAddress !== []) {
            $this->setShipFrom($this->shipFromAddress);
        }

        return $this->_shipFrom;
    }

    /**
     * Sets the ship from address.
     *
     * @param $address
     */
    public function setShipFrom($address): Address
    {
        return $this->_shipFrom = new Address($address);
    }

    /**
     * Gets the default (first listed) currency.
     */
    public function getDefaultCurrency(): string
    {
        if (! empty($this->defaultCurrency)) {
            return $this->defaultCurrency;
        }

        return $this->enabledCurrencies[0];
    }

    /**
     * Gets the symbol for the default currency.
     */
    public function getDefaultCurrencySymbol(): string
    {
        $defaultCurrency = $this->getDefaultCurrency();

        if (! array_key_exists($defaultCurrency, self::getCurrencySymbols())) {
            return '';
        }

        return self::getCurrencySymbols()[$defaultCurrency];
    }

    /**
     * Sets the array of enabled currencies to the supplied value, since we
     * don’t yet support setting multiple currencies.
     *
     * @param $value
     */
    public function setCurrency($value): array
    {
        return $this->enabledCurrencies = [$value];
    }

    public function addProvider($handle, $instance): void
    {
        $this->_providers[$handle] = $instance;
    }

    public function getProvider($handle)
    {
        return $this->_providers[$handle] ?? null;
    }

    public function getProviders(): array
    {
        return $this->_providers;
    }

    /**
     * Returns `true` if the setting has a value and that value isn't
     * simply an unparsed environment variable.
     *
     * @param $property
     */
    private function hasNonEmptyEnvValue(string $property): bool
    {
        // value stored on the model
        $settingValue = $this->{$property};

        if (empty($settingValue)) {
            // null or false or empty string
            return false;
        }

        // first character is `@`
        $isAlias = $settingValue[0] === '@';

        // first character is `$`
        $isEnvVar = $settingValue[0] === '$';

        if ($isAlias || $isEnvVar) {
            // have Craft parse aliases and environment variables
            $parsedSettingValue = VersionHelper::isCraft31() ?
                Craft::parseEnv($settingValue) :
                Craft::getAlias($settingValue);

            // does the stored setting get expanded?
            $parses = $settingValue !== $parsedSettingValue;

            if (! $parses || empty($parsedSettingValue)) {
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
    private function getNotificationEmailsFromTable(): void
    {
        if (is_array($this->notificationEmails) &&
            count($this->notificationEmails) &&
            is_array($this->notificationEmails[0])
        ) {
            $arrayFromTableData = [];

            foreach ($this->notificationEmails as $notificationEmail) {
                $arrayFromTableData[] = trim($notificationEmail[0]);
            }

            $this->notificationEmails = $arrayFromTableData;
        }
    }

    /**
     * Did the posted settings include any enabled shipping providers?
     */
    private function hasEnabledProviders(): bool
    {
        $request = Craft::$app->getRequest();

        if ($providers = $request->getBodyParam('providers')) {
            foreach ($providers as $handle => $settings) {
                if ($settings['enabled']) {
                    return true;
                }
            }
        }

        return false;
    }
}
