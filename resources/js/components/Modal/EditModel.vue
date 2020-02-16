<template>
    <span class="modal-content w-full">
        <span v-if="! fields.length">
            You have <b class="text-red-500">{{ migrations }}</b>
            migration{{ migrations === 1 ? '' : 's'}} to run.
        </span>

        <div v-else v-for="field in fields" :key="field.id" class="md:flex md:items-center">
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

       <draggable class="mt-5" v-model="created">
            <transition-group>
                 <div v-for="field in created" :key="field.id" class="md:flex md:items-center">
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

                    <i style="cursor:move"
                       class="fas fa-arrows-alt-v text-gray-200 mx-1"/>

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

        <div v-if="fields.length" class="inline-block w-full relative bg-transparent pb-5 pl-5">
            <span class="plus-minus">
                <button @click="addField()"
                        class="tooltip text-black inline-flex items-center
                     focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>

                <button
                    @click="removeField()"
                    class="tooltip text-black inline-flex items-center
                         focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-minus"/></span>
                </button>
            </span>
        </div>

        <div v-if="fields.length" class="flex justify-end pt-2">
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
                migrations: Schematics.migrations.created - Schematics.migrations.run,
                original: [],
                created: [],
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
                const fields = data.fields.map(field => {
                    return {
                        id: this.uuid(),
                        exists: true,
                        error: false,
                        changed: false,
                        name: field.Field,
                        type: null,
                        columnType: field.Type,
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
                this.created.push({
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
                let fields = this.created.concat(this.fields);

                if (!field) {
                    field = fields[fields.length - 1];
                }

                if (field.exists) {
                    this.deleted.push(field);
                    this.fields.splice(this.fields.indexOf(field), 1);
                } else {
                    this.created.splice(this.created.indexOf(field), 1);
                }
            },

            validFields() {
                let fields = this.changed.concat(this.created),
                    fieldErrors = fields.filter(
                    field => field.name.trim() === ''
                );

                fields.forEach(field => field.error = false);
                fieldErrors.forEach(field => field.error = true);

                return !fieldErrors.length;
            },

            setChangedFields() {
                let original = JSON.parse(this.original);

                this.changed = this.arrayDiffByKey('name', original, this.fields)
                    .concat(this.arrayDiffByKey('type', original, this.fields))
                    .filter(field => !this.deleted.map(field => field.id).includes(field.id))
                    .filter(field => this.fields.map(field => field.name).includes(field.name))
                    .filter(field => this.fields.map(field => field.type).includes(field.type))
                    .map(field => {
                        field.changed = true;
                        field.from = original.filter(f => f.id === field.id)[0].name;
                        field.to = field.name;

                        return field;
                    });
            },

            save() {
                this.setChangedFields();

                if (!this.validFields()) return;

                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/edit', {
                    'model': this.model,
                    'fields': this.fields.concat(this.created),
                    'changed': this.changed,
                    'created': this.created,
                    'deleted': this.deleted,
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

<style scoped>
    .plus-minus {
        position: absolute;
        right: 40px;
    }
</style>
