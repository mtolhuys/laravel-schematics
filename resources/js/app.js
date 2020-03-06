require('../../node_modules/@fortawesome/fontawesome-free/js/all.min');

import jQuery from 'jquery';
import Vue from 'vue';
import jsPlumb from 'jsplumb';
import Helpers from './lib/Helpers';
import Config from './lib/Config';
import Elements from './lib/Elements';
import Shortcuts from "./lib/Shortcuts";
import Schematics from './components/Schematics.vue';

window.$ = jQuery;
window.jsPlumb = jsPlumb.jsPlumb;
window.EventBus = new Vue({});

Vue.mixin(Helpers);
Vue.mixin(Elements);
Vue.mixin(Shortcuts);
Vue.mixin(Config);

const App = new Vue({
    el: '#app',

    components: {
        'schematics': Schematics,
    },
});

export default App;
