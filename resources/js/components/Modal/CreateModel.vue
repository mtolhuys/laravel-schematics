<template>
    <span class="modal-content new-model w-full">
        <div v-for="field in fields" class="md:flex md:items-center">
            <div class="md:w-1/3">
                <input
                    @keydown.enter="save()"
                    @keydown.tab="tab"
                    v-model="field.name"
                    placeholder="Field name"
                    class="field bg-white appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 mr-4
                     text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    :class="{
                        'focus:border-purple-500' : ! field.error,
                        'focus:border-red-500 border-red-500' : field.error,
                    }"
                    type="text"
                >
            </div>

            <div class="md:w-2/3">
                <input
                    v-model="field.type"
                    @keydown.tab="tab"
                    placeholder="Default: string|max:255"
                    class="field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                    text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text"
                >
            </div>
        </div>

        <div class="flex text-lg mt-3 items-end outline-none">
            <div class="inline-block relative bg-transparent pl-5">
                <label
                    aria-label="Generate model migration" data-balloon-pos="down"
                    class="tooltip block text-gray-500 font-bold">
                    <input
                        v-model="options.hasMigration"
                        class="mr-2 leading-tight" type="checkbox"
                    >
                    <span class="text-sm">
                        Migration
                    </span>
                </label>
            </div>

            <div class="inline-block hidden relative bg-transparent pt-1 pl-5">
                <label
                    aria-label="Generate form request with fields" data-balloon-pos="down"
                    class="tooltip block text-gray-500 font-bold">
                    <input
                        v-model="options.hasFormRequest"
                        class="mr-2 leading-tight" type="checkbox"
                    >
                    <span class="text-sm">
                        Form request
                    </span>
                </label>
            </div>

            <div class="inline-block hidden relative bg-transparent pt-1 pl-5">
                <label
                    aria-label="Generate resource route with controller" data-balloon-pos="down"
                    class="tooltip block text-gray-500 font-bold">
                    <input
                        v-model="options.hasResource"
                        class="mr-2 leading-tight" type="checkbox"
                    >
                    <span class="text-sm">
                        Resource
                    </span>
                </label>
            </div>

            <div class="inline-block relative bg-transparent ml-5 pt-2 pl-5">
                <button @click="addField()"
                        class="tooltip text-black inline-flex items-center
                         focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>
            </div>

            <div v-if="fields.length > 1"
                 class="inline-block relative bg-transparent pt-2 pl-5">
                <button @click="removeField()"
                        class="tooltip text-black inline-flex items-center
                         focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-minus"/></span>
                </button>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button
                @click="save()"
                class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
            >
                Save
            </button>
        </div>
    </span>
</template>

<script>
    export default {
        name: "create-model",

        data() {
            return {
                fieldsErrors: false,
                options: {
                    hasMigration: true,
                    hasFormRequest: false,
                    hasResource: false,
                },
                fields: [{
                    name: '',
                    type: '',
                    error: false
                }]
            }
        },

        created() {
            EventBus.$on('new-model', () => {
                this.fields = [{
                    name: '',
                    type: '',
                    error: false
                }];
            });
        },

        destroyed() {
            EventBus.$off('new-model');
        },

        methods: {
            tab(e) {
                if ($(e.target).is('.field:last')) {
                    this.addField();
                }
            },

            addField() {
                this.fields.push({
                    name: '',
                    type: '',
                    error: false
                })
            },

            removeField() {
                this.fields.splice(-1, 1);
            },

            validFields() {
                let fieldErrors = this.fields.filter(
                    field => field.name.trim() === ''
                );

                this.fields.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            validName(name) {
                return name.trim().length
                    && ! Schematics.models.includes(Schematics.namespace + name)
            },

            save() {
                let $modelName = $('.new-model-name'),
                    name = $modelName.val();

                $modelName
                    .parent()
                    .toggleClass('focus:border-red-500 border-red-500', ! this.validName(name));

                if (! this.validName(name) || ! this.validFields()) return;

                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/create', {
                    'name': name,
                    'fields': this.fields,
                    'options': this.options,
                }, () => {
                    location.reload();
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>

<style>
    .tooltip {
        --balloon-color: #9F7AEA;
        z-index: 9999;
        overflow: visible;
    }
</style>
