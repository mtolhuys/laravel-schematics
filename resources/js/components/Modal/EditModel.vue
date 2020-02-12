<template>
    <span class="modal-content w-full">
        <draggable class="mt-5" v-model="fields">
            <transition-group>
                <div v-for="field in fields" :key="field.id" class="md:flex md:items-center">
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
                            :placeholder="field.columnType || 'Default: string|max:255'"
                            class="new-field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
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

        <div class="inline-block w-full relative bg-transparent my-5 pt-2 pl-5">
            <span class="plus-minus">
                <button @click="addField()"
                        class="tooltip text-black inline-flex items-center
                     focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>

                <button
                    v-if="fields.length > 0"
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
        name: "edit-model",

        components: {
            Draggable,
        },

        props: {
            model: {
                type: String,
                required: true
            },
        },

        data() {
            return {
                original: [],
                changed: [],
                fields: [],
                deleted: [],
            }
        },

        created() {
            EventBus.$on('edit-model', this.setFields);
        },

        destroyed() {
            EventBus.$off('edit-model');
        },

        methods: {
            setFields(data) {
                const fields = data.fields.map(f => {
                    return {
                        id: this.uuid(),
                        exists: true,
                        error: false,
                        changed: false,
                        name: f.Field,
                        type: null,
                        columnType: `Existing: ${f.Type}`,
                    }
                });

                this.original = JSON.stringify(fields);
                this.fields = fields;
            },

            tab(e) {
                if ($(e.target).is('.remove-field')) {
                    this.addField();
                }
            },

            addField() {
                this.fields.push({
                    id: this.uuid(),
                    exists: false,
                    error: false,
                    changed: false,
                    name: '',
                    type: '',
                    columnType: null,
                });
            },

            removeField(field = null) {
                if (!field) {
                    field = this.fields[this.fields.length - 1];
                }

                this.fields.splice(this.fields.indexOf(field), 1);

                if (field.exists) {
                    this.deleted.push(field);
                }
            },

            validFields() {
                let fieldErrors = this.changed.filter(
                    field => field.name.trim() === ''
                );

                this.changed.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            setChangedFields() {
                let original = JSON.parse(this.original);

                this.arrayDiffByKey('name', original, this.fields)
                    .concat(this.arrayDiffByKey('type', original, this.fields))
                    .filter(field => !this.deleted.map(field => field.id).includes(field.id))
                    .filter(field => this.fields.map(field => field.name).includes(field.name))
                    .filter(field => this.fields.map(field => field.type).includes(field.type))
                    .map(field => {
                        field.changed = true;

                        return field;
                    });
            },

            save() {
                this.setChangedFields();

                if (!this.validFields()) return;

                // EventBus.$emit('modal-close');
                // EventBus.$emit('loading', true);

                $.post('schematics/models/edit', {
                    'model': this.model,
                    'fields': this.fields,
                    'deleted': this.deleted,
                }, (response) => {
                    console.info('response', response);
                    // location.reload();
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>

<style scoped>
    .plus-minus {
        position: absolute;
        right: 40px;
    }
</style>
