<?php
namespace verbb\snipcart\models;

use verbb\snipcart\models\snipcart\Address;

use Craft;
use craft\base\Model;
use craft\helpers\App;

class Settings extends Model
{
    // Constants
    // =========================================================================

    public const CURRENCY_USD = 'usd';
    public const CURRENCY_CAD = 'cad';
    public const CURRENCY_EUR = 'eur';
    public const CURRENCY_GBP = 'gbp';
    public const CURRENCY_CHF = 'chf';


    // Properties
    // =========================================================================

    public ?string $publicApiKey = null;
    public ?string $publicTestApiKey = null;
    public ?string $secretApiKey = null;
    public ?string $secretTestApiKey = null;
    public bool $sendOrderNotificationEmail = false;
    public array $notificationEmails = [];
    public ?string $notificationEmailTemplate = null;
    public bool $sendCustomerOrderNotificationEmail = false;
    public ?string $customerNotificationEmailTemplate = null;
    public ?string $defaultCurrency = null;
    public array $enabledCurrencies = [self::CURRENCY_USD];
    public ?string $orderGiftNoteFieldName = null;
    public ?string $orderCommentsFieldName = null;
    public bool $reduceQuantitiesOnOrder = false;
    public bool $cacheResponses = true;
    public int $cacheDurationLimit = 300; // 5 minutes
    public bool $logCustomRates = false;
    public bool $logWebhookRequests = false;
    public array $shipFromAddress = [];
    public array $providers = [];
    public array $providerSettings = [];
    public int $reFeedAttemptWindow = 15;
    public ?bool $testMode = false;
    public ?bool $sendTestModeEmail = false;
    
    private ?Address $_shipFrom = null;
    private array $_providers = [];


    // Public Methods
    // =========================================================================

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


    // Public Methods
    // =========================================================================

    public function getPublicTestApiKey(): string
    {
        return App::parseEnv($this->publicTestApiKey);
    }

    public function getPublicApiKey(): string
    {
        return App::parseEnv($this->publicApiKey);
    }

    public function getPublicKey(): string
    {
        return $this->testMode ? $this->getPublicTestApiKey() : $this->getPublicApiKey();
    }

    public function getSecretTestApiKey(): string
    {
        return App::parseEnv($this->secretTestApiKey);
    }

    public function getSecretApiKey(): string
    {
        return App::parseEnv($this->secretApiKey);
    }

    public function getSecretKey(): string
    {
        return $this->testMode ? $this->secretTestApiKey : $this->secretApiKey;
    }

    public function isConfigured(): bool
    {
        if ($this->testMode) {
            return $this->getPublicTestApiKey() && $this->getSecretTestApiKey();
        }

        return $this->getPublicApiKey() && $this->getSecretApiKey();
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['publicApiKey', 'secretApiKey', 'publicTestApiKey', 'secretTestApiKey', 'orderGiftNoteFieldName', 'orderCommentsFieldName'], 'string'],
            [['publicApiKey', 'secretApiKey'], 'required'],
            [['cacheDurationLimit'], 'number', 'integerOnly' => true],
            ['notificationEmails', 'each', 'rule' => ['email']],
        ]);
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        $validates = parent::validate($attributeNames, $clearErrors);

        if ($this->hasEnabledProviders()) {
            if (!$this->validateProviderSettings()) {
                $validates = false;
            }

            if (!$this->validateShipFrom()) {
                $validates = false;
            }
        }

        return $validates;
    }

    public function validateShipFrom(): bool
    {
        if ($this->hasEnabledProviders() && ! $this->getShipFrom()->validate()) {
            $this->addError('shipFrom', 'Please enter required Ship From details.');
            
            return false;
        }

        return true;
    }

    public function validateProviderSettings(): bool
    {
        $request = Craft::$app->getRequest();

        if ($this->hasEnabledProviders()) {
            foreach ($this->getProviders() as $provider) {
                if ($request->getBodyParam('providers')[$provider->refHandle()]['enabled'] && ! $provider->getSettings()->validate()) {
                    $this->addError('providerSettings', 'Provider settings are missing.');
                    return false;
                }
            }
        }

        return true;
    }

    public function beforeValidate(): bool
    {
        $this->getNotificationEmailsFromTable();

        return parent::beforeValidate();
    }

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

    public function getShipFrom(): ?Address
    {
        if ($this->shipFromAddress !== []) {
            $this->setShipFrom($this->shipFromAddress);
        }

        return $this->_shipFrom;
    }

    public function setShipFrom($address): Address
    {
        return $this->_shipFrom = new Address($address);
    }

    public function getDefaultCurrency(): string
    {
        if (!empty($this->defaultCurrency)) {
            return $this->defaultCurrency;
        }

        return $this->enabledCurrencies[0];
    }

    public function getDefaultCurrencySymbol(): string
    {
        $defaultCurrency = $this->getDefaultCurrency();

        if (!array_key_exists($defaultCurrency, self::getCurrencySymbols())) {
            return '';
        }

        return self::getCurrencySymbols()[$defaultCurrency];
    }

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


    // Private Methods
    // =========================================================================

    private function getNotificationEmailsFromTable(): void
    {
        if (is_array($this->notificationEmails) && count($this->notificationEmails) && is_array($this->notificationEmails[0])) {
            $arrayFromTableData = [];

            foreach ($this->notificationEmails as $notificationEmail) {
                $arrayFromTableData[] = trim($notificationEmail[0]);
            }

            $this->notificationEmails = $arrayFromTableData;
        }
    }

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
