import '../css/field-product-details.css';

var shippableSwitches = document.querySelectorAll(`[id*='fields-shippable']`);

if (shippableSwitches.length) {
    shippableSwitches.forEach(function(element) {
        element.onchange = function() {
            const parent = this.closest('.snipcart-product-details');
            const weightField = parent.querySelector('[id*=snipcart-weight-field]');
            const dimensionsField = parent.querySelector('[id*=snipcart-dimensions-field]');

            if (this.classList.contains('on')) {
                weightField.classList.remove('hidden');
                dimensionsField.classList.remove('hidden');
            } else {
                weightField.classList.add('hidden');
                dimensionsField.classList.add('hidden');
            }
        };
    });
}
