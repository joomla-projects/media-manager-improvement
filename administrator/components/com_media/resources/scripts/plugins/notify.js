/**
 * Notify mixin
 */
const Notify = {
    methods: {
        /* Send and success notification */
        notifySuccess(message, options) {
            this.notify(message, Object.assign({
                level: 'success',
                dismiss: true
            }, options));
        },
        /* Send an error notification */
        notifyError(message, options) {
            this.notify(message, Object.assign({
                level: 'error',
                dismiss: true
            }, options));
        },
        /* Send a notification */
        notify(message, options) {
            const alert = document.createElement('joomla-alert');
            alert.setAttribute('level', options.level || 'info');
            alert.setAttribute('dismiss', options.dismiss || true);
            alert.innerHTML = message || '';

            const messageContainer = document.getElementById('system-message');
            messageContainer.appendChild(alert);
        }
    },
}

export default Notify;