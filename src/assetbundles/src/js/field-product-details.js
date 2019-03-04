import '../css/field-product-details.css';

var shippableSwitch = document.getElementById('fields-shippable');
var weightField = document.getElementById('fields-snipcart-weight-field');
var dimensionsField = document.getElementById('fields-snipcart-dimensions-field');

if (shippableSwitch) {
    shippableSwitch.onchange = togglePhysicalFields;
}

function togglePhysicalFields() {
    if (shippableSwitch.classList.contains('on')) {
        weightField.classList.remove('hidden');
        dimensionsField.classList.remove('hidden');
    } else {
        weightField.classList.add('hidden');
        dimensionsField.classList.add('hidden');
    }
}