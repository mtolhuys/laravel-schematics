<template>
    <div class="schema" id="schema">
        <div v-for="model in models">
            <model :model="model"/>
        </div>
    </div>
</template>

<script>
    import DragSelect from 'dragselect';
    import ModelPositioning from '../ModelPositioning';
    import Plumbing from "../Plumbing";
    import Model from './Model.vue';

    export default {
        name: "schema",

        mixins: [
            ModelPositioning,
            Plumbing
        ],

        components: {
            'model': Model,
        },

        data() {
            return {
                selectedItems: [],
                models: Schematics.models,
            }
        },

        mounted() {
            this.positionModels();

            new DragSelect({
                selectables: $('.model'),
                multiSelectKeys: ['altKey', 'shiftKey'],
                onElementSelect: this.select,
                onElementUnselect: this.unSelect,
            });
        },

        methods: {
            select(model) {
                const $model = $(model).not('.hidden-model, .filtered');

                $model.addClass('selected');

                jsPlumb.addToDragSelection($model);
            },

            unSelect(model) {
                const $model = $(model).not('.hidden-model, .filtered');

                $model.removeClass('selected');

                jsPlumb.removeFromDragSelection($model);
            },
        }
    }
</script>
