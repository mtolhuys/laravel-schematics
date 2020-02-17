import Vue from 'vue';
import Helpers from './lib/Helpers';
import Elements from './lib/Elements';
import Shortcuts from "./lib/Shortcuts";
import Schematics from './components/Schematics.vue';

window.EventBus = new Vue({});

Vue.mixin(Helpers);
Vue.mixin(Elements);
Vue.mixin(Shortcuts);

const App = new Vue({
    el: '#app',

    components: {
        'schematics': Schematics,
    },
});

export default App;
