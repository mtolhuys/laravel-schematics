<template>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1">
                <i class="fas icon fa-exclamation-triangle"/>
            </span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu exceptions absolute left-10 pt-5 hidden">
            <li v-for="(exception, title) in exceptions"
                class="exception hover:bg-purple-400 px-4 block whitespace-no-wrap
                    bg-white text-red-700 hover:text-white"
                :data-exception="exception"
                :data-title="title"
            >
                <div class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-bug pr-1"></i> {{ title }}
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "warnings",

        data() {
            return {
                exceptions: Schematics.exceptions
            }
        },

        mounted() {
            $('.exception').unbind().click(function () {
                let title = $(this).data('title'),
                    exception = $(this).data('exception');

                EventBus.$emit(
                    'modal-open',
                    title,
                    'html',
                    `<b class="text-red-700 font-light">${exception}</b>`
                );
            });
        },

        watch: {
            'exceptions': {
                handler(value) {
                    console.info('value', value);
                }
            }
        }
    }
</script>
