<template>
    <div class="w-full h-full fixed block top-0 left-0 bg-white opacity-75 loading"
         :class="{ 'hidden': ! loading }">
      <span class="text-purple-800 opacity-75 top-1/2 my-0 mx-auto block relative w-0 h-0">
        <i class="fas fa-circle-notch fa-spin fa-5x"/>
      </span>
    </div>
</template>

<script>
    export default {
        name: "loading",

        data() {
            return {
                loading: false
            }
        },

        created() {
            EventBus.$on('loading', loading => this.loading = loading);
        },

        destroyed() {
            EventBus.$off('loading');
        },
    }
</script>
