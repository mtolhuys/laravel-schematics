<template>
    <span class="modal-content w-full">
        <div v-for="field in fields" class="md:flex md:items-center">
            <div class="md:w-1/3">
                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" :for="field.Field">
                    {{ field.Field }}
                </label>
            </div>
            <div class="md:w-2/3">
                <input
                    :value="field.Type"
                    :id="field.Field"
                    class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text"
                    disabled
                >
            </div>
        </div>

        <draggable class="mt-5" v-model="newFields">
            <transition-group>
                <div v-for="field in newFields" :key="field.id" class="md:flex md:items-center">
                    <div class="md:w-1/3">
                        <input
                            @keydown.enter="save()"
                            v-model="field.name"
                            placeholder="Field name"
                            class="new-field bg-white appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 mr-4
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
                            @keydown.enter="save()"
                            placeholder="Default: string|max:255"
                            class="new-field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                            text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                            type="text"
                        >
                    </div>
                </div>
            </transition-group>
        </draggable>

        <div class="inline-block w-full relative bg-transparent mb-10 pt-2 pl-5">
            <span class="plus-minus">
                <button @click="addField()"
                        class="tooltip text-black inline-flex items-center
                     focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>

                <button
                    v-if="newFields.length > 0"
                    @click="removeField()"
                    class="tooltip text-black inline-flex items-center
                         focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-minus"/></span>
                </button>
            </span>
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
    import Draggable from 'vuedraggable';

    export default {
        name: "model-fields",

        components: {
            Draggable,
        },

        props: {
            model: {
                type: String,
                required: true
            },

            fields: {
                type: Array,
                required: true
            }
        },


        data() {
            return {
                newFields: [{
                    id: this.uuid(),
                    name: '',
                    type: '',
                    error: false
                }]
            }
        },

        methods: {
            tab(e) {
                if ($(e.target).is('.remove-field')) {
                    this.addField();
                }
            },

            addField() {
                this.newFields.push({
                    id: this.uuid(),
                    name: '',
                    type: '',
                    error: false
                })
            },

            removeField() {
                this.newFields.splice(-1, 1);
            },

            validFields() {
                let fieldErrors = this.newFields.filter(
                    field => field.name.trim() === ''
                );

                this.newFields.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            save() {
                if (! this.validFields()) return;

                // EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/add-fields', {
                    'model': this.model,
                    'fields': this.newFields,
                }, (response) => {
                    console.info('response', response);
                    // location.reload();
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        }
    }
</script>

<style>
    .plus-minus {
        position: absolute;
        right: 0;
    }
</style>
