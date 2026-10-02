import '../css/settings-plugin.css';
import ClipboardJS from 'clipboard';

const inputField = document.getElementById('webhookEndpoint');
const inputContainer = inputField.parentElement;
const copyButton = document.createElement('a');

copyButton.className = 'copy-btn';
copyButton.setAttribute('data-clipboard-target', '#webhookEndpoint');

inputContainer.appendChild(copyButton);

const clipboard = new ClipboardJS('.copy-btn');

clipboard.on('success', function(e) {
    e.trigger.classList.add('success');
    setTimeout(function(){
        e.trigger.classList.remove('success');
    }, 3000);
    e.clearSelection();
});

const shipStationSettingsDeleteBtn = document.getElementById('shipstation-settings-delete');
const shipStationSettings = document.getElementById('shipstation-provider-settings');
const shipStationEnabledField = document.getElementById('shipstation-panel-enabled');
const shipStationAddBtn = document.getElementById('shipstation-add-btn');
const shipFromFields = document.getElementById('ship-from-fields');

shipStationSettingsDeleteBtn.onclick = removeShipStationSettings;
shipStationAddBtn.onclick = addShipStationSettings;

function removeShipStationSettings() {
    shipStationSettings.classList.add('hidden');
    shipStationAddBtn.classList.remove('hidden');

    resetShipStationFieldValues();

    shipFromFields.classList.add('hidden');
    shipStationEnabledField.setAttribute('value', 0);
}

function addShipStationSettings() {
    shipStationSettings.classList.remove('hidden');
    shipStationAddBtn.classList.add('hidden');
    shipFromFields.classList.remove('hidden');
    shipStationEnabledField.setAttribute('value', 1);
}

function resetShipStationFieldValues() {
    const textFields = shipStationSettings.querySelectorAll('input[type=text]');
    const lightswitchFields = shipStationSettings.querySelectorAll('.lightswitch');

    textFields.forEach(function(field){
        field.setAttribute('value', '');
    });

    lightswitchFields.forEach(function(field){
        const input = field.querySelector('input[type=hidden]');
        input.removeAttribute('value');

        field.classList.remove('on');
    });
}
