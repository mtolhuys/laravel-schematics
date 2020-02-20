<template>
   <span class="modal-content w-full">
       <div class="md:flex md:items-center">
            <div class="md:w-1/3">
                <p
                    class="new-field bg-white appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 mr-4
                     text-purple-600 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                >
                    Column name
                </p>
            </div>

            <div class="md:w-2/3">
                <p
                    class="new-field bg-white appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                    text-purple-600 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                >
                    Possible types:
                </p>
            </div>
        </div>

        <div class="md:flex md:items-center pt-2">
            <div class="md:w-1/3"></div>

            <div class="md:w-2/3">
                <p
                    class="new-field bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4
                    text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                >
                    increments ()
                </p>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "fields-explanation",
    }
</script>
