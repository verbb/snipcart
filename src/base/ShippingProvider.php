<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\base;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Craft;
use craft\base\Component;
use craft\base\Model;
use craft\helpers\Json;
use fostercommerce\snipcart\models\snipcart\Order as SnipcartOrder;
use fostercommerce\snipcart\models\snipcart\Package;
use fostercommerce\snipcart\Snipcart;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ShippingProvider extends Component implements ShippingProviderInterface
{
    /**
     * @var Client Guzzle client instance.
     */
    protected $client;

    /**
     * @var Model|bool|null Settings specifically for this provider.
     * @see getSettings()
     */
    private $settingsModel;

    public static function refHandle()
    {
        return '';
    }

    public static function apiBaseUrl(): string
    {
        return '';
    }

    public function getSettings()
    {
        if ($this->settingsModel === null && $this->createSettingsModel()) {
            /**
             * Initialize settings model.
             */
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

    public function createOrder(SnipcartOrder $snipcartOrder)
    {
        return null;
    }

    public function getClient(): Client
    {
        return Craft::createGuzzleClient();
    }

    public function getOrderById($providerId)
    {
        return null;
    }

    public function getOrderBySnipcartInvoice(string $snipcartInvoice)
    {
        return null;
    }

    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder)
    {
        return null;
    }

    public function get(string $endpoint, array $params = [])
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

    public function post(string $endpoint, array $data = [])
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

    /**
     * Extracts the value from a specific custom field, if it exists.
     *
     * @param array|null $customFields Custom fields data from Snipcart,
     *                                 an array of objects
     * @param string     $fieldName    Name of the field as seen in the order.
     * @param bool       $emptyAsNull  Return null rather than an empty value.
     *                                 (defaults to false)
     *
     * @return string|null
     */
    public function getValueFromCustomFields($customFields, $fieldName, $emptyAsNull = false)
    {
        if (! is_array($customFields)) {
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

    /**
     * Takes the raw response body and give it back as data that's ready to use.
     *
     * @param mixed  $body The raw response from the REST API.
     * @return mixed Appropriate PHP type, or null if json cannot be decoded
     *               or encoded data is deeper than the recursion limit.
     */
    public function prepResponseData(mixed $body)
    {
        return Json::decode($body, false);
    }

    /**
     * Handles a failed request.
     *
     * @param RequestException  $exception  the exception that was thrown
     * @param string            $endpoint   the endpoint that was queried
     */
    public function handleRequestException(
        $exception,
        string $endpoint,
    ) {
        /**
         * Get the status code, which should be 200 or 201 if things went well.
         */
        $statusCode = $exception->getResponse()->getStatusCode() ?? null;

        /**
         * If there's a response we'll use its body, otherwise default
         * to the request URI.
         */
        $reason = $exception->getResponse()->getBody() ?? null;

        if ($statusCode !== null && $reason instanceof StreamInterface) {
            // return code and message
            Craft::warning(sprintf(
                '%s API responded with %d: %s',
                self::displayName(),
                $statusCode,
                $reason
            ), 'snipcart');
        } else {
            // report mystery
            Craft::warning(sprintf(
                '%s API request to %s failed.',
                self::displayName(),
                $endpoint
            ), 'snipcart');
        }

        return null;
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Model|bool|null
     */
    protected function createSettingsModel()
    {
        return null;
    }
}
