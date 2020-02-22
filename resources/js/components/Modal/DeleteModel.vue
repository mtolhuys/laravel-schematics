<template>
    <span>
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
        <span
            class="modal-content new-model w-full">
            <p>Are you sure you want to delete <span class="text-purple-900">'{{ model }}'</span>?</p>

            <div class="flex justify-end pt-2">
                <button
                    @click="close()"
                    class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-red-200 hover:text-white mr-2"
                >
                    No
                </button>
                <button
                    @click="confirm()"
                    class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
                >
                    Yes
                </button>
            </div>
        </span>
    </span>
</template>

<script>
    export default {
        name: "delete-model",

        props: {
            title: {
                type: String,
                required: true
            },
            model: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                options: {}
            }
        },

        methods: {
            close() {
                EventBus.$emit('modal-close');
            },

            confirm() {
                const model = this.model;

                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/delete', {
                    name: model,
                    options: this.options,
                }, function () {
                    EventBus.$emit('delayed-alert', `${model} deleted`, 'info', 7000);

                    location.reload();
                }).fail(function (e) {
                    console.error(e);
                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>
