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
        <span class="modal-content w-full">
            -><b class="text-purple-900">{{ relation.method.name }}():</b>
                <b class="text-black">{{ relation.type }}(</b>
                    <i class="text-gray-800">'{{ relation.relation.model }}'</i>
                <b>)
            </b>;

            <div class="flex justify-end pt-2">
                <button
                    @click="remove()"
                    class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-red-200 hover:text-white mr-2"
                >
                    Remove
                </button>
                <button
                    @click="copy()"
                    class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-200 hover:text-indigo-400 mr-2"
                >
                    Copy Link
                </button>
            </div>
        </span>
    </span>
</template>

<script>
    export default {
        name: "model-fields",

        props: {
            title: {
                type: String,
                required: true
            },
            relation: {
                type: Object,
                required: true
            }
        },

        methods: {
            close() {
                EventBus.$emit('modal-close');
            },

            copy() {
                if (! window.isSecureContext) {
                    EventBus.$emit(
                        'alert',
                        'Can\'t copy due to insecure host...<br/>Try localhost or HTTPS.',
                        'error'
                    );

                    return;
                }

                const link = `${this.relation.method.file}:${this.relation.method.line}`;

                navigator.clipboard.writeText(link);

                EventBus.$emit('alert', `Copied: ${link}`, 'info');
            },

            remove() {
                EventBus.$emit('loading', true);
                EventBus.$emit('modal-close');

                $.post('schematics/relations/delete', this.relation, () => {
                    Schematics.relations[this.relation.table]
                        .splice(
                            Schematics.relations[this.relation.table]
                                .findIndex(r => r.method.name === this.relation.method.name)
                            , 1);

                    EventBus.$emit('loading', false);
                    EventBus.$emit('plumb');
                    setTimeout(Schematics.refresh, 1);
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        }
    }
</script>
