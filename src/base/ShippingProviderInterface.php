<?php
namespace verbb\snipcart\base;

use verbb\snipcart\models\snipcart\Order as SnipcartOrder;
use verbb\snipcart\models\snipcart\Package;

use craft\base\ComponentInterface;
use craft\base\Model;

use GuzzleHttp\Client;

interface ShippingProviderInterface extends ComponentInterface
{
    // Static Methods
    // =========================================================================

    public static function refHandle(): ?string;
    public static function apiBaseUrl(): string;
    

    // Public Methods
    // =========================================================================

    public function getSettings(): Model|bool|null;
    public function setSettings(array $settings): void;
    public function isConfigured(): bool;
    public function getRatesForOrder(SnipcartOrder $snipcartOrder, Package $package): array;
    public function createOrder(SnipcartOrder $snipcartOrder): mixed;
    public function getClient(): Client;
    public function getOrderById(string|int $providerId): mixed;
    public function getOrderBySnipcartInvoice(string $snipcartInvoice): mixed;
    public function createShippingLabelForOrder(SnipcartOrder $snipcartOrder): mixed;
    public function get(string $endpoint, array $params = []): mixed;
    public function post(string $endpoint, array $data = []): mixed;
}
