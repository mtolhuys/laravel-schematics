<template>
   <span class="modal-content w-full">
        <div class="text-gray-900 font-bold text-xl mb-2">Building / Editing Models</div>

       <p class="text-gray-700 text-base">
            Use this form to specify the <b>names</b> and <b>types</b> of your columns.
            <br/>
            The types are specified like how you would write your form request rules.
            <br/>
            <br/>
            Currently supported:
       </p>

       <p class="py-2">Types</p>

       <span class="supported-types items-center ml-5 pb-2 md:flex md:items-center">
           <ul class="inline-block list-disc md:w-2/4">
               <li>
                   increments
               </li>
               <li>
                   integer
               </li>
               <li>
                   boolean
               </li>
               <li>
                   dateTime
               </li>
               <li>
                   date
               </li>
               <li>
                   decimal
               </li>
           </ul>
           <ul class="inline-block list-disc md:w-2/4">
               <li>
                   integer
               </li>
               <li>
                   string

               </li>
               <li>
                   text

               </li>
               <li>
                   timestamp

               </li>
               <li>
                   time
               </li>
           </ul>
       </span>

       <p class="py-2">Rules</p>

       <span class="supported-types items-center ml-5 pb-2 md:flex md:items-center">
            <ul class="inline-block list-disc md:w-2/4">
                <li>
                    max:{int}
                </li>
                <li>
                    unsigned
                </li>
                <li>
                    unique
                </li>
                <li>
                    required
                </li>
            </ul>
       </span>

       <p class="py-2">Examples</p>

       <div class="md:flex md:items-center">
            <div class="md:w-1/3">
                <p class="new-field bg-white appearance-none border-2 border-gray-200
                    rounded w-full py-2 px-4 mr-4 text-gray-700 leading-tight"
                >
                    foo_bar_name
                </p>
            </div>

            <div class="md:w-2/3">
                <p class="new-field bg-gray-200 appearance-none border-2 border-gray-200
                    rounded w-full py-2 px-4 text-gray-700 leading-tight"
                >
                    string|max:20
                </p>
            </div>
       </div>

       <div class="md:flex md:items-center">
            <div class="md:w-1/3">
                <p class="new-field bg-white appearance-none border-2 border-gray-200
                    rounded w-full py-2 px-4 mr-4 text-gray-700 leading-tight"
                >
                    foo_bar_id
                </p>
            </div>

            <div class="md:w-2/3">
                <p class="new-field bg-gray-200 appearance-none border-2 border-gray-200
                    rounded w-full py-2 px-4 text-gray-700 leading-tight"
                >
                    unsigned|integer|required
                </p>
            </div>
       </div>

       <div class="flex justify-end mt-3 ">
            <button
                @click="back()"
                class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
            >
                Back
            </button>
        </div>
    </span>
</template>

<script>
    export default {
        name: "fields-explanation",

        props: {
            title: {
                type: String,
                required: false
            },
        },

        methods: {
            back() {
                EventBus.$emit('fields-explanation-back')
            }
        }
    }
</script>
