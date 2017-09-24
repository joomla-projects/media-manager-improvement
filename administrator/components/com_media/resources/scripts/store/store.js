import Vue from 'vue'
import Vuex from 'vuex'
import state from './state';
import * as getters from './getters';
import * as actions from './actions';
import mutations from './mutations';
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

const persistedStateOptions = {
    key: 'joomla.mediamanager',
    paths: [
        'selectedDirectory',
        'showInfoBar',
        'listView',
        'gridSize',
    ],
    storage: window.sessionStorage,
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
    state,
    getters,
    actions,
    mutations,
    plugins: [createPersistedState(persistedStateOptions)],
    strict: false
})