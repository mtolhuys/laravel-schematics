<template>
    <span class="modal-content w-full">
        -><b class="text-purple-900">{{ relation.method.name }}():</b>
            <b class="text-black">{{ relation.type }}(</b>
                <i class="text-gray-800">'{{ relation.relation.model }}'</i>
            <b>)
        </b>;

        <div class="flex justify-end pt-2">
            <button
                @click="remove(relation.method)"
                class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-red-200 hover:text-white mr-2"
            >
                Remove
            </button>
            <button
                @click="copy(`${relation.method.file}:${relation.method.line}`)"
                class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-200 hover:text-indigo-400 mr-2"
            >
                Copy Link
            </button>
        </div>
    </span>
</template>

<script>
    export default {
        name: "model-fields",

        props: {
            relation: {
                type: Object,
                required: true
            }
        },

        methods: {
            copy(link) {
                if (! window.isSecureContext) {
                    EventBus.$emit('alert', 'Can\'t copy due to insecure host...<br/>Try localhost or HTTPS.', 'error');

                    return;
                }

                navigator.clipboard.writeText(link);

                EventBus.$emit('alert', 'Link copied!', 'info');
            },

            remove(method) {
                EventBus.$emit('modal-close');

                $.post('schematics/remove-relation', method, () => {
                    Schematics.relations[this.relation.table]
                        .splice(
                            Schematics.relations[this.relation.table]
                                .findIndex(r => r.method.name === this.relation.method.name)
                            , 1);

                    setTimeout(Schematics.refresh, 1);
                    EventBus.$emit('loading', false);
                    EventBus.$emit('plumb');
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        }
    }
</script>
