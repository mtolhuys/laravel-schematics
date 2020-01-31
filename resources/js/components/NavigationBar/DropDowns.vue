<template>
    <div class="flex justify-end mr-10 text-purple-300 text-lg">
        <warnings/>
        <chart-style/>
        <settings/>
    </div>
</template>

<script>
    import Warnings from './Warnings.vue';
    import ChartStyle from './ChartStyle.vue';
    import Settings from './Settings.vue';

    export default {
        name: "dropdowns",

        components: {
            'warnings': Warnings,
            'chart-style': ChartStyle,
            'settings': Settings,
        },
    }
</script>

<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu {
        right: -60px;
    }
</style>
