<?php
namespace verbb\snipcart\models\snipcart;

use verbb\snipcart\fields\ProductDetails as ProductDetailsField;
use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\records\ProductDetails as ProductDetailsRecord;
use verbb\snipcart\Snipcart;

use Craft;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\elements\Entry;

use DateTime;
use stdClass;
use Throwable;

class Item extends Model
{
    // Properties
    // =========================================================================

    public ?string $uniqueId = null;
    public ?string $token = null;
    public ?string $id = null;
    public ?string $subscriptionId = null;
    public ?string $name = null;
    public ?float $price = null;
    public ?float $originalPrice = null;
    public ?string $description = null;
    public ?string $fileGuid = null;
    public ?string $initialData = null;
    public array $categories = [];
    public ?string $url = null;
    public ?int $weight = null;
    public ?string $image = null;
    public ?int $quantity = null;
    public ?int $minQuantity = null;
    public ?int $maxQuantity = null;
    public ?bool $stackable = null;
    public ?bool $shippable = null;
    public ?bool $taxable = null;
    public array $taxes = [];
    public array $customFields = [];
    public ?string $customFieldsJson = null;
    public ?bool $duplicatable = null;
    public stdClass|array|null $alternatePrices = null;
    public ?bool $hasDimensions = null;
    public ?float $unitPrice = null;
    public ?float $totalPrice = null;
    public ?float $totalPriceWithoutTaxes = null;
    public ?string $totalWeight = null;
    public ?string $addedOn = null;
    public ?string $startsOn = null;
    public ?DateTime $modificationDate = null;
    public ?float $width = null;
    public ?float $height = null;
    public ?float $length = null;
    public stdClass|array|null $metadata = null;
    public ?string $hasTaxesIncluded = null;
    public ?string $totalPriceWithoutDiscountsAndTaxesLegacy = null;
    public ?string $totalPriceWithoutDiscountsAndTaxes = null;
    public ?string $pausingAction = null;
    public ?string $cancellationAction = null;

    private ?PaymentSchedule $paymentSchedule = null;


    // Public Methods
    // =========================================================================

    public function extraFields(): array
    {
        return ['paymentSchedule'];
    }

    public function getRelatedElement(): ?ElementInterface
    {
        if (!$this->id) {
            Snipcart::info('Could not resolve related element: item has no SKU/id.');

            return null;
        }

        try {
            // Prefer querying current Craft field storage by Product Details field handle.
            // Since Craft now stores field values in native content storage, this avoids
            // relying on the legacy `snipcart_product_details` table.
            if ($element = $this->findElementByFieldStorage($this->id)) {
                return $element;
            }

            Snipcart::info('No element found from field storage; trying legacy product details record.', [
                'sku' => $this->id,
            ]);

            // Fall back to legacy record lookup for older installs.
            return $this->findElementByLegacyRecord($this->id);
        } catch (Throwable $e) {
            Snipcart::error('Failed resolving related element for SKU `{sku}`: {message}', [
                'sku' => $this->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getPaymentSchedule(): ?PaymentSchedule
    {
        return $this->paymentSchedule;
    }

    public function setPaymentSchedule(PaymentSchedule|stdClass|array|null $paymentSchedule): ?PaymentSchedule
    {
        if ($paymentSchedule === null) {
            return $this->paymentSchedule = null;
        }

        if (!$paymentSchedule instanceof PaymentSchedule) {
            $paymentScheduleData = ModelHelper::stripUnknownProperties($paymentSchedule, PaymentSchedule::class);

            $paymentSchedule = new PaymentSchedule((array)$paymentScheduleData);
        }

        return $this->paymentSchedule = $paymentSchedule;
    }

    // Private Methods
    // =========================================================================

    private function findElementByFieldStorage(string $sku): ?ElementInterface
    {
        $fields = Craft::$app->getFields()->getAllFields(false);

        foreach ($fields as $field) {
            if (!$field instanceof ProductDetailsField) {
                continue;
            }

            try {
                $query = Entry::find()
                    ->status(null)
                    ->site('*')
                    ->limit(1);

                Craft::configure($query, [
                    $field->handle => ['sku' => $sku],
                ]);

                if (($element = $query->one()) instanceof Entry) {
                    Snipcart::info('Resolved related element from field storage.', [
                        'sku' => $sku,
                        'fieldHandle' => $field->handle,
                        'elementId' => $element->id,
                    ]);

                    return $element;
                }
            } catch (Throwable $e) {
                Snipcart::info('Skipping Product Details field during SKU lookup.', [
                    'sku' => $sku,
                    'fieldHandle' => $field->handle,
                    'reason' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    private function findElementByLegacyRecord(string $sku): ?ElementInterface
    {
        if (!($record = ProductDetailsRecord::findOne([
            'sku' => $sku,
        ])) instanceof ProductDetailsRecord) {
            Snipcart::info('No legacy product details record found for SKU.', [
                'sku' => $sku,
            ]);

            return null;
        }

        if (($element = Craft::$app->getElements()->getElementById($record->elementId)) instanceof ElementInterface) {
            Snipcart::info('Resolved related element from legacy product details record.', [
                'sku' => $sku,
                'elementId' => $record->elementId,
            ]);

            return $element;
        }

        Snipcart::info('Legacy product details record found, but element could not be loaded.', [
            'sku' => $sku,
            'elementId' => $record->elementId,
        ]);

        return null;
    }
}
