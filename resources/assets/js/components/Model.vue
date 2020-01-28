<template>
    <div
        class="model-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg overflow-y-auto">
        <span
            :data-model-class="model"
            class="model absolute text-left flex justify-between
                bg-white hover:bg-gray-100 text-black font-bold
                border-b-4 border-purple-300 hover:border-purple-400 rounded shadow-lg
            ">

            <i class="fas fa-project-diagram icon"></i> {{ model }}

            <div
                @click="hide()"
                class="px-4 model-hide cursor-pointer">
                <i class="far fa-eye-slash text-gray-400 hover:text-purple-700"></i>
            </div>

            <div
                @click="edit()"
                class="cursor-pointer edit">
                <i class="fas fa-search text-gray-400 hover:text-purple-700"></i>
            </div>
        </span>
    </div>
</template>

<script>
    module.exports = {
        name: "model",

        components: {
            'model': httpVueLoader(`${Schematics.components}/Model.vue`),
        },

        props: {
            model: {
                type: String,
                required: true
            }
        },

        created() {},

        methods: {
            hide() {

            },

            edit() {

            }
        },
    }
</script>

<style scoped>

</style>
