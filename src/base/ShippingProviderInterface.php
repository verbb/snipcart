<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\base;

use craft\base\Model;
use craft\base\ComponentInterface;
use fostercommerce\snipcart\models\snipcart\Order as SnipcartOrder;
use fostercommerce\snipcart\models\snipcart\Package;
use fostercommerce\snipcart\models\snipcart\ShippingRate as SnipcartRate;
use GuzzleHttp\Client;

interface ShippingProviderInterface extends ComponentInterface
{
    /**
     * Gets the "camelCase" name of the provider.
     *
     * @return string|null
     */
    public static function refHandle();

    /**
     * Gets the base URL for the provider's REST API, used by client.
     */
    public static function apiBaseUrl(): string;

    /**
     * Gets the provider settings model, null if it's not ready, false
     * if there isn’t one.
     *
     * @return Model|bool|null
     */
    public function getSettings();

    /**
     * Sets the provider settings.
     *
     * @param array $settings Stored plugin settings that should populate the settings model
     */
    public function setSettings(array $settings);

    /**
     * Whether the provider is ready to go.
     */
    public function isConfigured(): bool;

    /**
     * Gets shipping rates for the provided Snipcart order.
     *
     * @return SnipcartRate[]
     */
    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array;

    /**
     * Creates an equivalent order in the provider’s system.
     *
     * @return mixed|null The created order model.
     */
    public function createOrder(SnipcartOrder $snipcartOrder);

    /**
     * Gets an instance of the Guzzle client.
     */
    public function getClient(): Client;

    /**
     * Gets an order by the provider’s unique ID.
     *
     * @param string|int $providerId
     * @return mixed provider order model or null
     */
    public function getOrderById($providerId);

    /**
     * Gets an order by Snipcart invoice number.
     *
     * @return mixed provider order model or null
     */
    public function getOrderBySnipcartInvoice(string $snipcartInvoice);

    /**
     * Creates a shipping label for the provided order.
     *
     * @return string|null URL to the label
     * @todo decide on sensible uniform return value
     */
    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder);

    /**
     * Performs GET request with the provider’s REST API, returning the response
     * as an object, array of objects, or null.
     *
     * @param $endpoint
     * @return mixed
     */
    public function get(string $endpoint, array $params = []);

    /**
     * Performs GET request with the provider’s REST API, returning the response
     * as an object, array of objects, or null.
     *
     * @param $endpoint
     * @return mixed
     */
    public function post(string $endpoint, array $data = []);
}
