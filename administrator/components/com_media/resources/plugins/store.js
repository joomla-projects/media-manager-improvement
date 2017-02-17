/**
 * A vue plugin for a simple store
 * @param Vue
 * @returns {*}
 */
export default function plugin(Vue, options) {

    const store = options.store;
    const state = store.state;

    if (typeof state === 'undefined') {
        console.warn("[Media Store]: Store has no state.", options.store);
    }

    // Register a global mixin to manage the getters/setters for our store.
    Vue.mixin({

        /**
         * The 'beforeCreate' life-cycle hook
         * @return {void}
         */
        beforeCreate() {
            Vue.util.defineReactive(this, 'state', state);
            Vue.util.defineReactive(this, '$actions', (action, val) => {
                store[action](val);
            });
        },
    });
}
