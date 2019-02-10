// TODO: use Vue

var triggerInput = document.getElementById('trigger');
var codeField = document.getElementById('code-field');
var itemIdField = document.getElementById('itemId-field');
var totalToReachField = document.getElementById('totalToReach-field');

triggerInput.onchange = updateTriggerOptions;

function updateTriggerOptions() {
    if (triggerInput.value === 'Total') {
        codeField.style.display = 'none';
        itemIdField.style.display = 'none';
        totalToReachField.style.display = 'block';
    } else if (triggerInput.value === 'Code') {
        codeField.style.display = 'block';
        itemIdField.style.display = 'none';
        totalToReachField.style.display = 'none';
    } else if (triggerInput.value === 'Product') {
        codeField.style.display = 'none';
        itemIdField.style.display = 'block';
        totalToReachField.style.display = 'none';
    }
}

updateTriggerOptions();

var typeInput = document.getElementById('type');
var amountField = document.getElementById('amount-field');
var productIdsField = document.getElementById('productIds-field');
var rateField = document.getElementById('rate-field');
var alternatePriceField = document.getElementById('alternatePrice-field');
var shippingDescriptionField = document.getElementById('shippingDescription-field');
var shippingCostField = document.getElementById('shippingCost-field');
var shippingGuaranteedDaysToDeliveryField = document.getElementById('shippingGuaranteedDaysToDelivery-field');

typeInput.onchange = updateTypeOptions;

function updateTypeOptions() {
    if (typeInput.value === 'FixedAmount') {
        amountField.style.display = 'block';
        productIdsField.style.display = 'none';
        rateField.style.display = 'none';
        alternatePriceField.style.display = 'none';
        shippingDescriptionField.style.display = 'none';
        shippingCostField.style.display = 'none';
        shippingGuaranteedDaysToDeliveryField.style.display = 'none';
    } else if (typeInput.value === 'FixedAmountOnItems') {
        amountField.style.display = 'none';
        productIdsField.style.display = 'block';
        rateField.style.display = 'none';
        alternatePriceField.style.display = 'none';
        shippingDescriptionField.style.display = 'none';
        shippingCostField.style.display = 'none';
        shippingGuaranteedDaysToDeliveryField.style.display = 'none';
    } else if (typeInput.value === 'Rate') {
        amountField.style.display = 'none';
        productIdsField.style.display = 'none';
        rateField.style.display = 'block';
        alternatePriceField.style.display = 'none';
        shippingDescriptionField.style.display = 'none';
        shippingCostField.style.display = 'none';
        shippingGuaranteedDaysToDeliveryField.style.display = 'none';
    } else if (typeInput.value === 'AlternatePrice') {
        amountField.style.display = 'none';
        productIdsField.style.display = 'none';
        rateField.style.display = 'none';
        alternatePriceField.style.display = 'block';
        shippingDescriptionField.style.display = 'none';
        shippingCostField.style.display = 'none';
        shippingGuaranteedDaysToDeliveryField.style.display = 'none';
    } else if (typeInput.value === 'Shipping') {
        amountField.style.display = 'none';
        productIdsField.style.display = 'none';
        rateField.style.display = 'none';
        alternatePriceField.style.display = 'none';
        shippingDescriptionField.style.display = 'block';
        shippingCostField.style.display = 'block';
        shippingGuaranteedDaysToDeliveryField.style.display = 'block';
    }
}

updateTypeOptions();

