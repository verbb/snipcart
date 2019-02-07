import '../css/settings-plugin.css';
import ClipboardJS from 'clipboard';

var inputField = document.getElementById('webhookEndpoint');
var inputContainer = inputField.parentElement;
var copyButton = document.createElement('a');

copyButton.className = 'copy-btn';
copyButton.setAttribute('data-clipboard-target', '#webhookEndpoint');

inputContainer.appendChild(copyButton);

// TODO: visually confirm copy with user
new ClipboardJS('.copy-btn');
