<template>
    <div class="schema"
         id="schema"
    >
        <side-bar/>

        <div v-for="model in models">
            <model :model="model"/>
        </div>
    </div>
</template>

<script>
    import DragSelect from 'dragselect';
    import SideBar from './SideBar.vue';
    import ModelPositioning from '../lib/ModelPositioning';
    import Plumbing from "../lib/Plumbing";
    import Model from './Model.vue';

    export default {
        name: "schema",

        mixins: [
            ModelPositioning,
            Plumbing,
        ],

        components: {
            'model': Model,
            'side-bar': SideBar,
        },

        data() {
            return {
                selectedItems: [],
                models: Schematics.models,
            }
        },

        mounted() {
            EventBus.$emit('loading', true);

            this.setModelsPosition();

            EventBus.$emit('plumb');

            new DragSelect({
                selectables: this.$models().all(),
                multiSelectKeys: ['altKey', 'shiftKey'],
                onElementSelect: this.select,
            });
        },

        methods: {
            select(model) {
                const $model = $(model).not('.hidden-model, .filtered');

                $model.addClass('selected');

                jsPlumb.addToDragSelection($model);
            },
        },

        watch: {
            'models': {
                deep: true,
                handler() {
                    this.setModelsPosition();
                }
            }
        }
    }
</script>
