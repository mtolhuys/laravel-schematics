<template>
    <div class="flex justify-end mr-10">
        <div class="flex text-lg">
            <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
                <button @click="addModel()"
                        aria-label="Add Model" data-balloon-pos="down"
                        class="tooltip focus:outline-none text-black inline-flex items-center text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>
            </div>
        </div>
        <migrations/>
        <warnings v-if="exceptions"/>
        <chart-style/>
        <settings/>
    </div>
</template>

<script>
    import Migrations from './Migrations.vue';
    import Warnings from './Warnings.vue';
    import ChartStyle from './ChartStyle.vue';
    import Settings from './Settings.vue';

    export default {
        name: "buttons",

        components: {
            'migrations': Migrations,
            'warnings': Warnings,
            'chart-style': ChartStyle,
            'settings': Settings,
        },

        data() {
            return {
                exceptions: Object.keys(Schematics.exceptions).length
            }
        },

        methods: {
            addModel() {
                EventBus.$emit(
                    'modal-open',
                    '<span class="focus:outline-none border-b-2 py-2 px-4">' +
                         Schematics.namespace + '<input ' +
                        'class="new-model-name" ' +
                        'placeholder="New model"' +
                    '/></span>',
                    'new-model'
                );
                setTimeout(() => {
                    EventBus.$emit('new-model');
                    $('.new-model-name').val('').focus();
                }, 1);
            }
        }
    }
</script>

<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .tooltip {
        --balloon-color: #9F7AEA;
    }

    .dropdown-menu {
        right: -60px;
    }

    .bottom-full {
        bottom: 100%
    }

    .top-full {
        top: 100%
    }
</style>
