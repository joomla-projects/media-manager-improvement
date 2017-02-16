import Vue from "vue";
import App from "./components/app.vue";
import Tree from "./components/tree/tree.vue";
import TreeItem from "./components/tree/item.vue";
import Toolbar from "./components/toolbar/toolbar.vue";
import Breadcrumb from "./components/breadcrumb/breadcrumb.vue";
import Browser from "./components/browser/browser.vue";
import BrowserItem from "./components/browser/items/item";
// Plugins
import Store from "./plugins/store";
// App Services
import MediaStore from "./app/Store";

/* Whether or not the app is currently in debug mode */
const isDebug = true;

// Register the vue components
Vue.component('media-tree', Tree);
Vue.component('media-tree-item', TreeItem);
Vue.component('media-toolbar', Toolbar);
Vue.component('media-breadcrumb', Breadcrumb);
Vue.component('media-browser', Browser);
Vue.component('media-browser-item', BrowserItem);

// Register plugins
Vue.use(Store, {store: new MediaStore(isDebug)});

// Create the root Vue instance
document.addEventListener("DOMContentLoaded",
    (e) => new Vue({
        el: '#com-media',
        render: h => h(App)
    })
)
