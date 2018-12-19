<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use workingconcept\snipcart\models\Order as SnipcartOrder;
use workingconcept\snipcart\models\Package;
use workingconcept\snipcart\models\ShippingRate as SnipcartRate;
use GuzzleHttp\Client;

class ShippingProvider extends \craft\base\Component
{
    /**
     * @var \stdClass Settings specifically for this provider.
     * @todo consider making this a validated model, one per provider
     */
    protected $providerSettings;

    /**
     * @var Client Guzzle client instance.
     */
    protected $client;

    // Static Methods
    // =========================================================================

    /**
     * Whether the provider is ready to go.
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }


    // Public Methods
    // =========================================================================

    /**
     * Get shipping rates for the provided Snipcart order.
     *
     * @param SnipcartOrder $snipcartOrder
     * @param Package       $package
     * @return SnipcartRate[]
     */
    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        return [];
    }

    /**
     * Create an equivalent order in the provider's system.
     *
     * @param SnipcartOrder $snipcartOrder
     * @return mixed|null The created order model.
     */
    public function createOrder(SnipcartOrder $snipcartOrder)
    {
        return null;
    }

    /**
     * Get an instance of the Guzzle client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return new Client();
    }

    /**
     * Get an order by the provider's unique ID.
     *
     * @param string|int $providerId
     * @return mixed provider order model or null
     */
    public function getOrderById($providerId)
    {

    }

    /**
     * Get an order by Snipcart invoice number.
     *
     * @param string $snipcartInvoice
     * @return mixed provider order model or null
     */
    public function getOrderBySnipcartInvoice(string $snipcartInvoice)
    {

    }

    /**
     * Create a shipping label for the provided order.
     *
     * @param SnipcartOrder $snipcartOrder
     * @return string URL to the label
     * @todo decide on sensible uniform return value
     */
    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder)
    {

    }

    // Private Methods
    // =========================================================================

}
