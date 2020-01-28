const App = new Vue({
    el: '#app',

    components: {
        'schematics': httpVueLoader(`${Schematics.components}/Schematics.vue`),
    },
});

window.EventBus = new Vue({});

export default App;
