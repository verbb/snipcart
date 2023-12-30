<?php
namespace verbb\snipcart\models\snipcart;

use craft\base\Model;

class Address extends Model
{
    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?string $firstName = null;
    public ?string $fullName = null;
    public ?string $companyName = null;
    public ?string $company = null;
    public ?string $address1 = null;
    public ?string $address2 = null;
    public ?string $fullAddress = null;
    public ?string $city = null;
    public ?string $country = 'US';
    public ?string $province = null;
    public ?string $postalCode = null;
    public ?string $phone = null;
    public ?string $vatNumber = null;
    public ?string $email = null;
    public ?bool $hasMinimalRequiredInfo = null;


    // Public Methods
    // =========================================================================

    public function getFormattedPhone(): ?string
    {
        $num = $this->phone;

        if (strlen($num) === 10 && is_numeric($num)) {
            // format US phone numbers (555) 555-5555
            return ($num !== '' && $num !== '0') ? '(' . substr($num,0,3) . ') ' . substr($num,3,3) . '-' . substr($num,6,4) : '&nbsp;';
        }

        return $num;
    }
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['name', 'companyName', 'address1', 'address2', 'city', 'country', 'province', 'postalCode', 'phone', 'email'], 'string', 'max' => 255];
        $rules[] = [['name', 'address1', 'city', 'country', 'province', 'postalCode'], 'required'];
        $rules[] = [['country', 'province'], 'string', 'length' => 2];

        return $rules;
    }
}
