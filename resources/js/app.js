import Vue from 'vue';
import Schematics from './Schematics.vue';

window.EventBus = new Vue({});

const App = new Vue({
    el: '#app',

    components: {
        'schematics': Schematics,
    },
});

export default App;
