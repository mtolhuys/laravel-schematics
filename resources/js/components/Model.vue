<template>
    <div
        @mouseover="plumbSource"
        @mouseleave="clear"
        class="model-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg overflow-y-auto">
        <span
            :id="table"
            :data-table="table"
            :data-model="model"
            class="model absolute text-left flex justify-between
                bg-white hover:bg-gray-100 text-black font-bold
                border-b-4 border-purple-300 hover:border-purple-400 rounded shadow-lg
            ">

            <i class="fas fa-project-diagram icon"/> {{ model }}

            <button
                @click="remove(model)"
                class="px-4 cursor-pointer text-gray-400 hover:text-purple-700">
                <i class="fas fa-trash-alt"/>
            </button>

            <button
                @click="hide()"
                class="pr-4 cursor-pointer text-gray-400 hover:text-purple-700">
                <i class="far fa-eye-slash"/>
            </button>

            <button
                @click="edit(model, table)"
                class="cursor-pointer text-gray-400 hover:text-purple-700">
                <i class="fas fa-info"/>
            </button>
        </span>
    </div>
</template>

<script>
    export default {
        name: "model",

        props: {
            model: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                table: Schematics.tables[this.model],
                endpoint: null,
            }
        },

        mounted() {
            const $el = $(this.$el).find('span');

            Schematics.endpoints = [];
            Schematics.endpointLock = false;

            jsPlumb.makeTarget($el, {connectionsDetachable: false});
        },

        methods: {
            clear(event = null) {
                if (!event || $(event.relatedTarget).prop('nodeName') !== 'IMG') {
                    Schematics.endpoints.forEach(endpoint => jsPlumb.deleteEndpoint(endpoint));

                    this.$endpoints().detach();

                    Schematics.endpointLock = false;
                    Schematics.endpoints = [];
                }
            },

            getEndpointPosition(e) {
                let rect = e.target.getBoundingClientRect(),
                    x = e.clientX - rect.left,
                    y = e.clientY - rect.top,
                    left = x < (rect.width / 2.7),
                    right = x > (rect.width / 1.3),
                    top = y < (rect.height / 2) && (!left && !right),
                    bottom = !top && (!left && !right);

                return [
                    'Top',
                    'Left',
                    'Right',
                    'Bottom',
                ][[top, left, right, bottom].indexOf(true)];
            },

            getEndpointStyle(location) {
                return {
                    'Top': {'margin-top': '-6px'},
                    'Left': {'margin-left': '-6px'},
                    'Right': {'margin-left': '6px'},
                    'Bottom': {'margin-top': '6px'},
                }[location];
            },

            plumbSource(e) {
                if (Schematics.endpointLock) {
                    return;
                }

                let endpoint = this.endpoint,
                    location = this.getEndpointPosition(e);

                jsPlumb.deleteEndpoint(endpoint);

                this.endpoint = endpoint = jsPlumb.addEndpoint($(e.target), {
                    isSource: true,
                    reattach: true,
                    isTarget: true,
                    connector: Schematics.style,
                    anchor: location,
                    endpoint: ["Image", {
                        src: `vendor/schematics/images/${location.toLowerCase()}-arrow.png`,
                        cssClass: `plumb-arrow plumb-arrow-${location.toLowerCase()}`,
                    }],
                    dragOptions: {
                        drag: () => {
                            Schematics.endpointLock = true;
                        },
                        stop: () => {
                            Schematics.endpointLock = false;

                            this.clear();
                        }
                    }
                });

                Schematics.endpoints.push(endpoint);

                $(`.plumb-arrow-${location.toLowerCase()}`).css(this.getEndpointStyle(location));
            },

            hide() {
                const $model = $(this.$el);

                $model.addClass('hidden-model', true).hide();

                localStorage.setItem(`schematics-settings-${$model.data('model')}-hidden`, 'true');

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            },

            remove(model) {
                EventBus.$emit('modal-open', `Deleting model!`, `delete-model`, model);
            },

            edit(model, table) {
                EventBus.$emit('loading', true);

                $.get(`schematics/details/${table}`, function (fields) {
                    EventBus.$emit('modal-open', model, 'model-fields', {
                        model: model,
                        fields: fields
                    });

                    EventBus.$emit('loading', false);
                }).fail(function (e) {
                    console.error(e);
                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },
    }
</script>

<style>
    .selected {
        border: 2px double rgba(255, 71, 58, 0.81);
    }

    .alert {
        z-index: 1200;
    }
</style>
