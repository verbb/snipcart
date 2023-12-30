<?php
namespace verbb\snipcart\models\shipstation;

use verbb\snipcart\models\snipcart\Item as SnipcartItem;

use craft\base\Model;

use DateTime;
use Yii;

class OrderItem extends Model
{
    // Properties
    // =========================================================================

    public ?int $orderItemId = null;
    public ?string $lineItemKey = null;
    public ?string $sku = null;
    public string $name = null;
    public ?string $imageUrl = null;
    public ?int $quantity = null;
    public ?float $unitPrice = null;
    public ?float $taxAmount = null;
    public ?float $shippingAmount = null;
    public ?string $warehouseLocation = null;
    public ?int $productId = null;
    public ?string $fulfillmentSku = null;
    public ?bool $adjustment = null;
    public ?string $upc = null;
    public ?DateTime $createDate = null;
    public ?DateTime $modifyDate = null;

    private ?Weight $_weight = null;
    private array $_options = [];
    

    // Public Methods
    // =========================================================================

    public function getWeight(): ?Weight
    {
        return $this->_weight;
    }

    public function setWeight(array|Weight $weight): Weight
    {
        if (is_array($weight)) {
            $weight = new Weight($weight);
        }

        return $this->_weight = $weight;
    }

    public function getOptions(): array
    {
        if ($this->_options !== null) {
            return $this->_options;
        }

        $this->_options = [];

        return $this->_options;
    }

    public function setOptions(array $options): void
    {
        $this->_options = $options ?? [];
    }

    public static function populateFromSnipcartItem(SnipcartItem $item): self
    {
        if (!empty($item->customFields)) {
            $itemOptions = [];

            foreach ($item->customFields as $customField) {
                $itemOptions[] = ItemOption::populateFromSnipcartCustomField($customField);
            }
        }

        return new self([
            'lineItemKey' => $item->id,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'unitPrice' => $item->unitPrice,
            'weight' => Weight::populateFromSnipcartItem($item),
            'options' => $itemOptions ?? null,
        ]);
    }

    public function fields(): array
    {
        $fields = array_keys(Yii::getObjectVars($this));
        $fields = [...$fields, 'weight', 'options'];
        
        return array_combine($fields, $fields);
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['orderItemId', 'quantity', 'productId'], 'number', 'integerOnly' => true];
        $rules[] = [['unitPrice', 'taxAmount', 'shippingAmount'], 'number', 'integerOnly' => false];
        $rules[] = [['lineItemKey', 'sku', 'name', 'warehouseLocation', 'fulfillmentSku', 'upc', 'createDate', 'modifyDate'], 'string', 'max' => 255];
        $rules[] = [['name'], 'required'];
        $rules[] = [['imageUrl'], 'url'];

        return $rules;
    }
}
