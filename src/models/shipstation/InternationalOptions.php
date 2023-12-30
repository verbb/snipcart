<?php
namespace verbb\snipcart\models\shipstation;

use craft\base\Model;

class InternationalOptions extends Model
{
    // Constants
    // =========================================================================

    public const CONTENTS_MERCHANDISE = 'merchandise';
    public const CONTENTS_DOCUMENTS = 'documents';
    public const CONTENTS_GIFT = 'gift';
    public const CONTENTS_RETURNED_GOODS = 'returned_goods';
    public const CONTENTS_SAMPLE = 'sample';
    public const NON_DELIVERY_RETURN_TO_SENDER = 'return_to_sender';
    public const NON_DELIVERY_TREAT_AS_ABANDONED = 'treat_as_abandoned';


    // Properties
    // =========================================================================

    public ?string $contents = null;
    public ?string $nonDelivery = null;

    private array $customsItems = [];


    // Public Methods
    // =========================================================================

    public function getCustomsItems(): array
    {
        if ($this->customsItems !== null) {
            return $this->customsItems;
        }

        $this->customsItems = [];

        return $this->customsItems;
    }

    public function setCustomsItems(?array $customsItems): ?array
    {
        return $this->customsItems = $customsItems;
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
                
        $rules[] = [['contents'], 'in', 'range' => [
            self::CONTENTS_MERCHANDISE,
            self::CONTENTS_DOCUMENTS,
            self::CONTENTS_GIFT,
            self::CONTENTS_RETURNED_GOODS,
            self::CONTENTS_SAMPLE,
        ]];

        $rules[] = [['nonDelivery'], 'in', 'range' => [
            self::NON_DELIVERY_RETURN_TO_SENDER,
            self::NON_DELIVERY_TREAT_AS_ABANDONED,
        ]];

        return $rules;
    }
}
