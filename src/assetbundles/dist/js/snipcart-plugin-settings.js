var inputField = document.getElementById('webhookEndpoint');
var inputContainer = inputField.parentElement;
var copyButton = document.createElement('a');

copyButton.className = 'copy-btn';
copyButton.setAttribute('data-clipboard-target', '#webhookEndpoint');

inputContainer.appendChild(copyButton);

var clipboard = new ClipboardJS('.copy-btn');
