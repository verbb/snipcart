<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\providers;

use craft\base\Component;
use GuzzleHttp\Client;
use workingconcept\snipcart\models\Order as SnipcartOrder;
use workingconcept\snipcart\models\Package;

class ShippingProvider extends Component implements ShippingProviderInterface
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

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function createOrder(SnipcartOrder $snipcartOrder)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getClient(): Client
    {
        return new Client();
    }

    /**
     * @inheritdoc
     */
    public function getOrderById($providerId)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getOrderBySnipcartInvoice(string $snipcartInvoice)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function get(string $endpoint, array $params = [])
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function post(string $endpoint, array $data = [])
    {
        return null;
    }

}
