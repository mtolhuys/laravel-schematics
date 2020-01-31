<template>
    <div
        class="model-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg overflow-y-auto">
        <span
            :id="table"
            :data-table="table"
            :data-model="model"
            class="model absolute text-left flex justify-between
                bg-white hover:bg-gray-100 text-black font-bold
                border-b-4 border-purple-300 hover:border-purple-400 rounded shadow-lg
            ">

            <i class="fas fa-project-diagram icon"/> {{ model }}

            <span
                @click="hide()"
                class="px-4 model-hide cursor-pointer">
                <i class="far fa-eye-slash text-gray-400 hover:text-purple-700"/>
            </span>

            <span
                @click="edit(model, table)"
                class="cursor-pointer edit">
                <i class="fas fa-search text-gray-400 hover:text-purple-700"/>
            </span>
        </span>
    </div>
</template>

<script>
    export default {
        name: "model",

        props: {
            model: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                table: Schematics.tables[this.model]
            }
        },

        created() {},

        methods: {
            hide() {
                const $model = $(this.$el);

                $model.addClass('hidden-model', true).hide();

                localStorage.setItem(`schematics-settings-${$model.data('model')}-hidden`, 'true');

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            },

            edit(model, table) {
                EventBus.$emit('loading', true);

                $.get(`schematics/details/${table}`, function (fields) {
                    EventBus.$emit('modal-open', model, 'model-fields', fields);

                    EventBus.$emit('loading', false);
                }).fail(function (e) {
                    console.error(e);
                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>

<style scoped>
    .selected {
        border: 2px double rgba(255, 71, 58, 0.81);
    }

    .alert {
        z-index: 1200;
    }
</style>
