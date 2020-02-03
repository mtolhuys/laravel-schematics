<template>
    <span class="modal-content w-full">
        -><b class="text-purple-900">{{ relation.method.name }}():</b>
            <b class="text-black">{{ relation.type }}(</b>
                <i class="text-gray-800">'{{ relation.relation.model }}'</i>
            <b>)
        </b>;

        <div class="flex justify-end pt-2">
            <button
                @click="copy(`${relation.method.file}:${relation.method.line}`)"
                class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
            >
                Copy Link
            </button>
        </div>
    </span>
</template>

<script>
    export default {
        name: "ModelFields",

        props: {
            relation: {
                type: Object,
                required: true
            }
        },

        methods: {
            copy(link) {
                navigator.clipboard.writeText(link);

                EventBus.$emit('alert', 'Link copied!', 'info');
            }
        }
    }
</script>
