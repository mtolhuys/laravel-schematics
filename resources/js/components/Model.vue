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

            <span class="model-actions" @mouseover="prevent">
                <button
                    @click="remove(model)"
                    aria-label="Remove Model" data-balloon-pos="down"
                    class="pl-4 pr-2 cursor-pointer text-gray-400 hover:text-purple-700 tooltip">
                    <i class="fas fa-trash-alt"/>
                </button>

                <button
                    @click="hide(model)"
                    aria-label="Hide Model" data-balloon-pos="down"
                    class="pr-2 cursor-pointer text-gray-400 hover:text-purple-700 tooltip">
                    <i class="far fa-eye-slash"/>
                </button>

                <button
                    @click="edit(model, table)"
                    aria-label="Edit Model" data-balloon-pos="down"
                    class="cursor-pointer text-gray-400 hover:text-purple-700 tooltip">
                    <i class="fas fa-pencil-alt"/>
                </button>
            </span>
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
            const $el = $(this.$el),
                hidden = JSON.parse(
                    localStorage.getItem(
                        `schematics-settings-${this.model.toLowerCase()}-hidden-tab-${Schematics.activeTab}`
                    )
                );

            if (hidden) {
                $el.hide();
                $el.addClass('hidden-model');
            }

            Schematics.endpoints = [];
            Schematics.endpointLock = false;

            jsPlumb.makeTarget($el.find('span'), {connectionsDetachable: false});
        },

        methods: {
            prevent(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            },

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

            hide(model) {
                const $model = $(this.$el);

                $model.addClass('hidden-model', true).hide();

                localStorage.setItem(`schematics-settings-${model.toLowerCase()}-hidden-tab-${Schematics.activeTab}`, 'true');

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            },

            remove(model) {
                EventBus.$emit('modal-open', `Deleting model!`, `delete-model`, model);
            },

            edit(model, table) {
                EventBus.$emit('loading', true);

                $.get(`schematics/edit/${table}`, function (fields) {
                    let data = {
                        model: model,
                        fields: fields
                    };

                    EventBus.$emit('modal-open', model, 'model-fields', data);

                    setTimeout(() => {
                        EventBus.$emit('edit-model', data);
                        EventBus.$emit('loading', false);
                    }, 1);
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
    .model-actions {
        min-width: 90px;
    }

    .tooltip {
        --balloon-color: #9F7AEA;
    }

    .selected {
        border: 2px double rgba(255, 71, 58, 0.81);
    }

    .alert {
        z-index: 1200;
    }
</style>
