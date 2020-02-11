<template>
    <div data-backdrop="static"
         class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center"
        :class="{ 'opacity-0 pointer-events-none' : closed }"
    >
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container flex bg-white w-auto inline-block mx-auto rounded shadow-lg overflow-y-scroll z-50">
            <div class="py-4 text-left px-6 w-full">
                <div class="flex justify-between items-center w-full pb-3">
                    <p class="modal-title text-2xl font-bold" v-html="title"/>

                    <div @click="closed = true"
                        class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex w-full">
                    <span v-if="type === 'html'" v-html="content" />
                    <relation v-if="type === 'relation'" :relation="content" />
                    <create-relation v-if="type === 'new-relation'" :models="content" />
                    <model-fields v-if="type === 'model-fields'" :model="content.model" :fields="content.fields" />
                    <delete-model v-if="type === 'delete-model'" :model="content"/>
                    <create-model v-if="type === 'new-model'"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CreateRelation from './Modal/CreateRelation.vue';
    import CreateModel from './Modal/CreateModel.vue';
    import DeleteModel from './Modal/DeleteModel.vue';
    import ModelFields from './Modal/ModelFields.vue';
    import Relation from './Modal/Relation.vue';

    export default {
        name: "modal",

        components: {
            'create-relation': CreateRelation,
            'model-fields': ModelFields,
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

