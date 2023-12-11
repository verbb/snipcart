<?php
namespace verbb\snipcart\base;

use verbb\snipcart\Snipcart;
use verbb\snipcart\models\snipcart\Order as SnipcartOrder;
use verbb\snipcart\models\snipcart\Package;

use Craft;
use craft\base\Component;
use craft\base\Model;
use craft\helpers\Json;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

use Psr\Http\Message\StreamInterface;

class ShippingProvider extends Component implements ShippingProviderInterface
{
    // Properties
    // =========================================================================

    protected Client $client;

    private Model|bool|null $settingsModel = null;


    // Static Methods
    // =========================================================================

    public static function refHandle(): ?string
    {
        return '';
    }

    public static function apiBaseUrl(): string
    {
        return '';
    }


    // Public Methods
    // =========================================================================

    public function getSettings(): Model
    {
        if ($this->settingsModel === null && $this->createSettingsModel()) {
            $this->settingsModel = $this->createSettingsModel();

            $pluginSettings = Snipcart::$plugin->getSettings();
            $providerSettings = $pluginSettings->providerSettings[static::refHandle()] ?? [];

            $this->settingsModel->setAttributes($providerSettings);
        }

        return $this->settingsModel;
    }

    public function setSettings(array $settings): void
    {
        if ($this->getSettings()) {
            $this->getSettings()->setAttributes($settings, false);
        }
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        return [];
    }

    public function createOrder(SnipcartOrder $snipcartOrder): mixed
    {
        return null;
    }

    public function getClient(): Client
    {
        return Craft::createGuzzleClient();
    }

    public function getOrderById(string|int $providerId): mixed
    {
        return null;
    }

    public function getOrderBySnipcartInvoice(string $snipcartInvoice): mixed
    {
        return null;
    }

    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder): mixed
    {
        return null;
    }

    public function get(string $endpoint, array $params = []): mixed
    {
        if ($params !== []) {
            $endpoint .= '?' . http_build_query($params);
        }

        try {
            $response = $this->getClient()->get($endpoint);
            return $this->prepResponseData(
                $response->getBody()
            );
        } catch (RequestException $requestException) {
            $this->handleRequestException($requestException, $endpoint);
            return null;
        }
    }

    public function post(string $endpoint, array $data = []): mixed
    {
        try {
            $response = $this->getClient()->post($endpoint, [
                RequestOptions::JSON => $data,
            ]);

            return $this->prepResponseData($response->getBody());
        } catch (RequestException $requestException) {
            $this->handleRequestException($requestException, $endpoint);
            return null;
        }
    }

    public function getValueFromCustomFields(?array $customFields, string $fieldName, bool $emptyAsNull = false): string|null
    {
        if (!is_array($customFields)) {
            return null;
        }

        foreach ($customFields as $customField) {
            if ($customField->name === $fieldName) {
                if ($emptyAsNull && empty($customField->value)) {
                    return null;
                }

                return $customField->value;
            }
        }

        return null;
    }

    public function prepResponseData(mixed $body): mixed
    {
        return Json::decode($body, false);
    }

    public function handleRequestException(RequestException $exception, string $endpoint): mixed
    {
        $statusCode = $exception->getResponse()->getStatusCode() ?? null;
        $reason = $exception->getResponse()->getBody() ?? null;

        if ($statusCode !== null && $reason instanceof StreamInterface) {
            Snipcart::error('{name} API responded with {code}: {reason}', [
                'name' => self::displayName(),
                'code' => $statusCode,
                'reason' => $reason,
            ]);
        } else {
            Snipcart::error('{name} API request to {endpoint} failed', [
                'name' => self::displayName(),
                'endpoint' => $endpoint,
            ]);
        }

        return null;
    }

    protected function createSettingsModel(): Model|bool|null
    {
        return null;
    }
}
