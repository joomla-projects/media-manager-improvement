/**
 * A vue plugin for binding an api service
 * @param Vue
 * @param options
 * @todo use the api service only in the app component
 */
export default function plugin(Vue, options) {

    const service = options.service;

    if (typeof service === 'undefined') {
        console.warn("[Media API]: API service not defined.");
    }

    // Register a helper prototype property for store access.
    Object.defineProperty(Vue.prototype, '$api', {
        get() {
            return service;
        }
    });
}
