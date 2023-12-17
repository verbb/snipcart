<?php
namespace verbb\snipcart\models\snipcart;

use verbb\snipcart\helpers\ModelHelper;
use verbb\snipcart\records\ProductDetails as ProductDetailsRecord;

use Craft;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\elements\MatrixBlock;

use DateTime;
use stdClass;

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
    public ?string $metadata = null;
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

    public function getRelatedElement(bool $entryOnly = false): ?ElementInterface
    {
        // get related record by SKU
        if (!($record = ProductDetailsRecord::findOne([
            'sku' => $this->id,
        ])) instanceof ProductDetailsRecord) {
            // bail without a Record, which can happen if the product's details
            // aren't stored in our Product Details field type
            return null;
        }

        if ($element = Craft::$app->getElements()->getElementById($record->elementId)) {
            $isMatrix = $element && $element instanceof MatrixBlock;

            if ($isMatrix && $entryOnly) {
                return $element->getOwner();
            }

            return $element;
        }

        // Record without an Element
        return null;
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
}
