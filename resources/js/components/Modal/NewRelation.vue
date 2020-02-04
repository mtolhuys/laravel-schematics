<template>
    <span class="modal-content w-full">
        <div class="md:flex md:items-center flex">
            <div class="md:w-1/3">
                <label
                    for="method"
                    class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                    Method Name
                </label>
            </div>
            <input
                id="method"
                value="method"
                placeholder="methodName"
                v-model="relation.method.name"
                @keydown.enter="save()"
                class="bg-gray-100 appearance-none border-2 border-gray-200 rounded w-full
                py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                :class="{
                    'focus:border-purple-500' : ! methodNameError,
                    'focus:border-red-500 border-red-500' : methodNameError,
                }"
                type="text"
            />
        </div>

        <div class="md:flex md:items-center flex">
            <div class="md:w-1/3">
                <label
                    for="keys"
                    class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-5">
                    Keys
                </label>
            </div>
            <span
                id="keys"
                class="appearance-none leading-tight w-full"
            >
                <input
                    value="method"
                    v-model="relation.method.foreignKey"
                    placeholder="foreignKey"
                    @keydown.enter="save()"
                    class="bg-gray-100 appearance-none border-2 border-gray-200 rounded w-full
                    py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text"
                />
                <input
                    value="method"
                    v-model="relation.method.localKey"
                    :placeholder="relation.type === 'BelongsTo' ? 'ownerKey' : 'localKey'"
                    @keydown.enter="save()"
                    class="bg-gray-100 appearance-none border-2 border-gray-200 rounded w-full
                    py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text"
                />
            </span>
        </div>


        <div class="md:flex md:items-center flex">
              <div class="md:w-1/3">
                  <label
                      for="type"
                      class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                    Relation Type
                  </label>
              </div>
              <select
                  id="type"
                  v-model="relation.type"
                  class="bg-gray-100 appearance-none border-2 border-gray-200 rounded w-full
                  py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
              >
              <option>HasMany</option>
              <option>HasOne</option>
              <option>BelongsTo</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path
                    d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
              </div>
        </div>

        <div class="md:flex md:items-center flex">
            <div class="md:w-1/3">
                <label
                    for="target"
                    class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                    Target
                </label>
            </div>
            <input
                id="target"
                v-model="relation.target"
                class="bg-gray-400 appearance-none border-2 border-gray-400 w-full
                py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                type="text"
                disabled
            />
        </div>


        <div class="md:flex md:items-center border border-gray-400 rounded w-full flex py-3 mt-2">
            <span
                id="result"
                class="appearance-none w-full py-2 px-4 text-gray-700 leading-tight">
                -><b class="text-purple-900">{{ relation.method.name }}():</b>
                    <b class="text-black">{{ relation.type }}(</b>
                        <i class="text-gray-800">'{{ relation.target }}'{{ relation.keys }}</i>
                    <b>)
                </b>;
            </span>
        </div>

        <div class="flex justify-end pt-2">
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
    export default {
        name: "new-relation",

        props: {
            models: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                methodNameError: true,
                relation: {
                    type: 'HasMany',
                    source: this.models.source,
                    target: this.models.target,
                    keys: '',
                    method: {
                        name: '',
                        foreignKey: '',
                        localKey: '',
                    }
                }
            }
        },

        created() {
            EventBus.$on('new-relation', (models) => {
                this.relation.source = models.source;
                this.relation.target = models.target;
                this.relation.sourceTable = models.target;
                this.relation.targetTable = models.target;
                this.relation.keys = '';
                this.relation.method = {
                    name: '',
                    foreignKey: '',
                    localKey: '',
                };
            });
        },

        destroyed() {
            EventBus.$off('new-relation');
        },

        methods: {
            addRelation(relation) {
                relation.relation = {};
                relation.model = this.models.source;
                relation.table = this.models.sourceTable;
                relation.relation.model = this.models.target;
                relation.relation.table = this.models.targetTable;

                if (! Schematics.relations[this.models.sourceTable]) {
                    Schematics.relations[this.models.sourceTable] = [];
                }

                Schematics.relations[this.models.sourceTable].push(relation);
            },

            save() {
                if (this.methodNameError) {
                    EventBus.$emit('alert', 'Invalid method name!', 'error');

                    return;
                }

                EventBus.$emit('modal-close');
                EventBus.$emit('loading', true);

                $.post('schematics/new-relation', this.relation, (relation) => {
                    this.addRelation(relation);

                    EventBus.$emit('loading', false);
                    EventBus.$emit('plumb');
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        },

        watch: {
            'relation.method.name': {
                handler(name) {
                    this.methodNameError = !name.trim().length || (
                        Schematics.relations[this.models.sourceTable] &&
                        Schematics.relations[this.models.sourceTable].map(r => {
                            return r.method.name
                        }).includes(name)
                    );
                }
            },

            'relation.method.foreignKey': {
                handler(key) {
                    if (key.trim().length) {
                        this.relation.keys = `, '${key}'`;
                    } else {
                        this.relation.keys = '';
                    }
                }
            },

            'relation.method.localKey': {
                handler(key) {
                    let foreignKey = this.relation.method.foreignKey;

                    if (key.trim().length && foreignKey.trim().length) {
                        this.relation.keys = `, '${foreignKey}', '${key}'`;
                    } else if (foreignKey.trim().length) {
                        this.relation.keys = `, '${foreignKey}'`;
                    } else {
                        this.relation.keys = '';
                    }
                }
            }
        }
    }
</script>

<style>
    #keys {
        margin-left: -3px;
    }
</style>
