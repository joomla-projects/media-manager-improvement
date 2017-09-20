class Notifications {
    /* Send a notification */
    notify(message, options) {

        const alert = document.createElement('joomla-alert');
        alert.setAttribute('level', options.level || 'info');
        alert.setAttribute('dismiss', options.dismiss || true);
        alert.innerHTML = message || '';

        const messageContainer = document.getElementById('system-message');
        messageContainer.appendChild(alert);
    }
}

export let notifications = new Notifications();