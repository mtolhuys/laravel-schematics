<template>
    <div
        :class="{
            'bg-purple-100 border-purple-500' : type === 'info',
            'bg-red-100 border-red-500' : type === 'error',
        }"
        class="alert hidden border-t-4 rounded-b text-teal-900 px-4 py-3 shadow-md w-full"
        role="alert">

        <div class="flex">
            <div class="py-1">
                <svg class="fill-current h-6 w-6 text-white-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold alert-title">{{ type.toUpperCase() }}</p>
                <p class="text-sm alert-message"></p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "alert",

        data() {
            return {
                type: 'info'
            }
        },

        created() {
            EventBus.$on('alert', this.alert);
        },

        destroyed() {
            EventBus.$off('alert');
        },

        methods: {
            close() {
                this.$alert().hide();
            },

            alert(msg, type = 'info', time = 3000) {
                let $alert = this.$alert();

                this.type = type;

                $alert.find('.alert-message').html(msg);
                $alert.show();

                clearTimeout(window.Alert || 0);

                window.Alert = setTimeout(function () {
                    if ($alert.is(":hover")) {
                        return;
                    }

                    $alert.hide();
                }, time)
            },
        }
    }
</script>

<style scoped>
    .alert {
        position: fixed;
        bottom: 0;
        z-index: 2000;
    }
</style>
