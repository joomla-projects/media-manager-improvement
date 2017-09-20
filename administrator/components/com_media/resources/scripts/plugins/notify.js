import {notifications} from "../app/Notifications";

/**
 * Notify mixin
 */
const Notify = {
    methods: {
        /* Send and success notification */
        notifySuccess(message, options) {
            notifications.notify(message, Object.assign({
                level: 'success',
                dismiss: true
            }, options));
        },
        
        /* Send an error notification */
        notifyError(message, options) {
            notifications.notify(message, Object.assign({
                level: 'error',
                dismiss: true
            }, options));
        },
    },
}

export default Notify;