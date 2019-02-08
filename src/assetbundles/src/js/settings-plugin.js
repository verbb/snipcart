import '../css/settings-plugin.css';
import ClipboardJS from 'clipboard';

var inputField = document.getElementById('webhookEndpoint');
var inputContainer = inputField.parentElement;
var copyButton = document.createElement('a');

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
