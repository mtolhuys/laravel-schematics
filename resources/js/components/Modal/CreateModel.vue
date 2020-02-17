<template>
    <span class="modal-content new-model w-full">
        <draggable v-model="fields">
            <transition-group>
                <div v-for="field in fields" :key="field.id" class="md:flex md:items-center">
                    <div class="md:w-1/3">
                        <input
                            @keydown.enter="save()"
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
                            @keydown.enter="save()"
                            placeholder="Default: string|max:255"
                            class="field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                            text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                            type="text"
                        >
                    </div>

                    <button
                        @click="removeField(field)"
                        @keydown.tab="tab"
                        class="px-4 remove-field cursor-pointer text-gray-400 hover:text-purple-700">
                        <i class="fas fa-trash-alt"/>
                    </button>
                </div>
            </transition-group>
        </draggable>

        <div class="flex text-lg mt-3 items-end outline-none">
            <div class="inline-block w-full relative bg-transparent mx-5 mb-10 pt-2 pl-5">
                <span class="plus-minus">
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
                            v-model="options.hasTimestamps"
                            class="mr-2 leading-tight" type="checkbox">
                        <span class="text-sm w-2/3">
                            Timestamps
                        </span>
                    </label>
                </div>

                <div class="md:flex md:items-center">
                    <label class="block text-gray-500 font-bold">
                        <input
                            v-model="actions.hasResource"
                            class="mr-2 leading-tight" type="checkbox">
                        <span class="text-sm w-2/3">
                            Resource Controller
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
</template>

<script>
    import Draggable from 'vuedraggable';

    export default {
        name: "create-model",

        components: {
            Draggable,
        },

        data() {
            return {
                fieldsErrors: false,
                actions: {
                    hasFormRequest: false,
                    hasResource: false,
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

        destroyed() {
            EventBus.$off('new-model');
        },

        methods: {
            tab(e) {
                if ($(e.target).is('.remove-field')) {
                    this.addField();
                }
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
                    field => field.name.trim() === ''
                );

                this.fields.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            validName(name) {
                return name.trim().length
                    && !Schematics.models.includes(Schematics.namespace + name)
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
