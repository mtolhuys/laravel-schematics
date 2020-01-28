<template>
    <div class="schema" id="schema">
        <div v-for="model in models">
            <model :model="model"></model>
        </div>
    </div>
</template>

<script>
    module.exports = {
        name: "schema",

        components: {
            'model': httpVueLoader(`${Schematics.components}/Model.vue`),
        },

        data() {
            return {
                models: Schematics.models,
            }
        },

        created() {},

        methods: {
            init() {

            },

            search() {

            }
        },
    }
</script>

<style scoped>

</style>
