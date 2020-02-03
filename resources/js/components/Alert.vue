<template>
    <div
        class="alert hidden fixed flex bottom-0 right-0 m-8 w-5/6 shadow-lg md:w-full max-w-sm rounded"
        :class="{
            'bg-white' : type === 'info',
            'bg-red-500' : type === 'error',
        }"
    >
        <label
            class="content flex items-start justify-between w-full p-2 h-24 rounded shadow-lg"
            :class="{
                'text-purple-600' : type === 'info',
                'text-white' : type === 'error',
            }"
        >
            <span
                class="px-6 py-3 flex font-bold alert-text"
                :class="{
                    'text-black' : type === 'info',
                    'text-white' : type === 'error',
                }"
            />

            <svg
                @click="close()"
                class="fill-current cursor-pointer "
                xmlns="http://www.w3.org/2000/svg"
                width="18" height="18"
                viewBox="0 0 18 18"
            >
                <path
                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"
                />
            </svg>
        </label>
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

            alert(msg, type = 'success', time = 3000) {
                let $alert = this.$alert();

                this.type = type;

                $alert.find('.alert-text').html(msg);
                $alert.show();

                clearTimeout(window.Alert || 0);

                window.Alert = setTimeout(function () {
                    $alert.hide();
                }, time)
            },
        }
    }
</script>

<style scoped>
    .alert {
        z-index: 2000;
    }
</style>
