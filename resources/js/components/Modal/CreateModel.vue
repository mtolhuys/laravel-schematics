<template>
    <fields-explanation v-if="explanation"/>
    <span v-else>
        <div class="flex justify-between items-center w-full pb-3">
            <p class="modal-title text-2xl font-bold" v-html="title"/>

            <div @click="close"
                 class="modal-close cursor-pointer z-50">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 18 18">
                    <path
                        d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"/>
                </svg>
            </div>
        </div>
        <div class="flex w-full">
            <span class="modal-content new-model w-full">
                <draggable v-model="fields">
                    <transition-group>
                        <div v-for="field in fields" :key="field.id" class="md:flex md:items-center">
                            <div class="md:w-1/3">
                                <input
                                    @keydown.enter="addField()"
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

                            <i style="cursor:move"
                               class="fas fa-arrows-alt-v text-gray-200 mx-1"/>

                            <div class="md:w-2/3">
                                <input
                                    v-model="field.type"
                                    @keydown.enter="addField()"
                                    placeholder="Default: string|max:255"
                                    class="field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                                    text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                                    type="text"
                                >
                            </div>

                            <button
                                @click="removeField(field)"
                                class="px-4 remove-field cursor-pointer text-gray-400 hover:text-purple-700">
                                <i class="fas fa-trash-alt"/>
                            </button>
                        </div>
                    </transition-group>
                </draggable>

                <div class="flex text-lg mt-3 items-end outline-none">
                    <div class="inline-block w-full relative bg-transparent mx-5 mb-10 pt-2 pl-5">
                        <span class="plus-minus">
                            <button @click="toggleExplanation()"
                                    class="tooltip text-black inline-flex items-center
                                 focus:outline-none text-purple-300 hover:text-purple-500">
                                <span class="mr-1"><i class="far fa-question-circle"/></span>
                            </button>

                            <button @click="addField()"
                                    class="tooltip text-black inline-flex items-center
                                 focus:outline-none text-purple-300 hover:text-purple-500">
                                <span class="mr-1"><i class="fas fa-plus"/></span>
                            </button>

                            <button
                                v-if="fields.length > 1"
                                @click="removeField()"
                                class="tooltip text-black inline-flex items-center
                                     focus:outline-none text-purple-300 hover:text-purple-500">
                                <span class="mr-1"><i class="fas fa-minus"/></span>
                            </button>
                        </span>

                        <div class="md:flex md:items-center">
                            <label class="block text-gray-500 font-bold">
                                <input
                                    v-model="actions.hasModelMigration"
                                    class="mr-2 leading-tight" type="checkbox">
                                <span class="text-sm w-2/3">
                                    Create migration
                                </span>
                            </label>

                            <label class="block text-gray-500 font-bold mx-10">
                                <input
                                    v-model="options.hasTimestamps"
                                    class="mr-2 leading-tight" type="checkbox"
                                    :disabled="! actions.hasModelMigration">
                                <span class="text-sm w-2/3">
                                    Timestamps
                                </span>
                            </label>
                        </div>

                        <div class="md:flex md:items-center">
                            <label class="block text-gray-500 font-bold">
                                <input
                                    v-model="actions.hasResourceController"
                                    class="mr-2 leading-tight" type="checkbox">
                                <span class="text-sm w-2/3">
                                    Resource Controller
                                </span>
                            </label>
                        </div>

                        <div class="md:flex md:items-center">
                            <label class="block text-gray-500 font-bold">
                                <input
                                    v-model="actions.hasFormRequest"
                                    class="mr-2 leading-tight" type="checkbox">
                                <span class="text-sm w-2/3">
                                    Form Request
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="save()"
                        class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
                    >
                        Save
                    </button>
                </div>
            </span>
        </div>
    </span>
</template>

<script>
    import Draggable from 'vuedraggable';
    import FieldsExplanation from './FieldsExplanation.vue';

    export default {
        name: "create-model",

        components: {
            Draggable,
            FieldsExplanation
        },

        props: {
            title: {
                type: String,
                required: true
            },
        },

        data() {
            return {
                explanation: false,
                fieldsErrors: false,
                actions: {
                    hasModelMigration: this.config('create.migration'),
                    hasFormRequest: this.config('create.form-request'),
                    hasResourceController: this.config('create.resource-controller'),
                },
                options: {
                    hasTimestamps: false,
                },
                fields: [{
                    id: this.uuid(),
                    name: '',
                    type: '',
                    error: false
                }]
            }
        },

        created() {
            EventBus.$on('new-model', () => {
                this.fields = [{
                    id: this.uuid(),
                    name: '',
                    type: '',
                    error: false
                }];
            });
        },

        mounted() {
            EventBus.$on('fields-explanation-back', this.toggleExplanation);
        },

        destroyed() {
            EventBus.$off('fields-explanation-back');
            EventBus.$off('new-model');
        },

        methods: {
            close() {
                EventBus.$emit('modal-close');
            },

            toggleExplanation() {
                this.explanation = ! this.explanation;
            },

            addField() {
                this.fields.push({
                    id: this.uuid(),
                    name: '',
                    type: '',
                    error: false
                })
            },

            removeField(field = null) {
                if (this.fields.length > 1) {
                    this.fields.splice(field ? this.fields.indexOf(field) : -1, 1);
                }
            },

            validFields() {
                let fieldErrors = this.fields.filter(
                    field => /\W/.test(field.name) || field.name.trim() === ''
                );

                this.fields.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            validName(name) {
                return /^[A-Za-z]+$/.test(name)
                    && name.trim().length
                    && !Schematics.models.includes(this.config('model-namespace') + name)
            },

            save() {
                let $modelName = $('.new-model-name'),
                    name = $modelName.val();

                $modelName
                    .parent()
                    .toggleClass('focus:border-red-500 border-red-500', !this.validName(name));

                if (!this.validName(name) || !this.validFields()) return;

                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/create', {
                    'name': name,
                    'fields': this.fields,
                    'actions': this.actions,
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

        watch: {
            'actions.hasModelMigration': {
                handler() {
                    if (! this.actions.hasModelMigration) {
                        this.options.hasTimestamps = false;
                    }
                }
            }
        }
    }
</script>

<style>
    .tooltip {
        --balloon-color: #9F7AEA;
        z-index: 9999;
        overflow: visible;
    }

    .plus-minus {
        position: absolute;
        right: 20px;
    }
</style>
