<template>
    <div data-backdrop="static"
         class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center"
        :class="{ 'opacity-0 pointer-events-none' : closed }"
    >
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container flex bg-white w-auto inline-block mx-auto rounded shadow-lg overflow-y-scroll z-50">
            <div class="py-4 text-left px-6 w-full">
                <span v-if="type === 'html'" v-html="content" />
                <relation
                        v-if="type === 'relation'"
                        :relation="content"
                        :title="title"
                />
                <create-relation
                        v-if="type === 'new-relation'"
                        :models="content"
                        :title="title"
                />
                <edit-model
                        v-if="type === 'model-fields'"
                        :model="content.model"
                        :fields="content.fields"
                        :title="title"
                />
                <delete-model
                        v-if="type === 'delete-model'"
                        :model="content"
                        :title="title"
                />
                <create-model v-if="type === 'new-model'" :title="title"/>
            </div>
        </div>
    </div>
</template>

<script>
    import CreateRelation from './Modal/CreateRelation.vue';
    import CreateModel from './Modal/CreateModel.vue';
    import DeleteModel from './Modal/DeleteModel.vue';
    import EditModel from './Modal/EditModel.vue';
    import Relation from './Modal/Relation.vue';

    export default {
        name: "modal",

        components: {
            'create-relation': CreateRelation,
            'edit-model': EditModel,
            'create-model': CreateModel,
            'delete-model': DeleteModel,
            'relation': Relation,
        },

        data() {
            return {
                closed: true,
                title: '',
                type: '',
                content: [],
            }
        },

        created() {
            EventBus.$on('modal-open', (title, type, content) => {
                this.title = title;
                this.type = type;
                this.content = content;
                this.closed = false;

                Schematics.modal = true;
            });

            EventBus.$on('modal-close', this.close);
        },

        destroyed() {
            EventBus.$off('modal-open');
            EventBus.$off('modal-close');
        },

        methods: {
            close() {
                this.closed = true;
                Schematics.modal = false;
            }
        },
    }
</script>

<style scoped>
    .modal {
        z-index: 1100;
    }

    .modal-container {
        min-width: 600px;
        max-height: 90%;
    }
</style>

