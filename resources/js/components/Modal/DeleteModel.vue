<template>
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
</template>

<script>
    export default {
        name: "delete-model",

        props: {
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
                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/models/delete', {
                    name: this.model,
                    options: this.options,
                }, function () {
                    EventBus.$emit('delayed-alert', `${model} deleted`, 'info', 7000);

                    setTimeout(Schematics.refresh, 1);
                }).fail(function (e) {
                    console.error(e);
                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>
