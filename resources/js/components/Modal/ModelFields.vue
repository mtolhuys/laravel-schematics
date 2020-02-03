<template>
    <span class="modal-content w-full">
        <div v-for="field in fields" class="md:flex md:items-center">
            <div class="md:w-1/3">
                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" :for="field.Field">
                    {{ field.Field }}
                </label>
            </div>
            <div class="md:w-2/3">
                <input
                    :value="field.Type"
                    :id="field.Field"
                    class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text"
                    disabled
                >
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "model-fields",

        props: {
            fields: {
                type: Array,
                required: true
            }
        }
    }
</script>
