<template>
    <nav
        id="header"
        class="fixed w-full z-20 top-0 bg-white border-b border-gray-400"
    >
        <div
            class="w-full flex flex-wrap justify-between mt-0 py-4"
        >
            <div class="pl-4 flex items-center">
                <a class="text-gray-900 text-base no-underline hover:no-underline font-extrabold text-xl">
                    <i class="fas fa-sitemap icon"/> Laravel Schematics
                </a>

                <div class="flex-1 w-full mx-auto max-w-sm content-center py-4 lg:py-0">
                    <div class="relative pull-right pl-4 pr-4 md:pr-0">
                        <input
                            v-on:keydown.enter="search()"
                            v-model="searchFor"
                            type="search"
                            placeholder="(RegEx) Search..."
                            class="w-full bg-gray-100 text-sm text-gray-800 transition border focus:outline-none focus:border-purple-500 rounded py-1 px-2 pl-10 appearance-none leading-normal"
                        >

                        <div class="absolute search-icon" style="top: 0.375rem; left: 1.75rem;">
                            <svg class="fill-current pointer-events-none text-gray-800 w-4 h-4"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <ul class="flex justify-center">
                    <li class="text-center font-bold flex-2">
                        <a class="block py-2 px-4 text-gray-800">
                            <span id="model-count">{{ models }}</span> Model(s)
                        </a>
                    </li>
                </ul>
            </div>

            <dropdowns/>
        </div>
    </nav>
</template>

<script>
    import DropDowns from './NavigationBar/DropDowns.vue';

    export default {
        name: "nav-bar",

        components: {
            'dropdowns': DropDowns,
        },

        data() {
            return {
                searchFor: localStorage.getItem('schematics-settings-search') || '',
                models: Schematics.models.length,
            }
        },

        mounted() {
            this.search();
        },

        methods: {
            search() {
                let $models = this.$models().unhidden();

                EventBus.$emit('loading', true);

                localStorage.setItem('schematics-settings-search', this.searchFor);

                if (this.searchFor.trim().length) {
                    let search = this.searchFor.toLowerCase();

                    $models.addClass('filtered').hide();

                    try {
                        new RegExp(search);
                    } catch (e) {
                        console.error(`Invalid format: ${search}`);
                        $models.show();
                        EventBus.$emit('plumb');
                        return;
                    }

                    const $found = $models.filter(function () {
                        return $(this).data('model')
                            .toLowerCase()
                            .match(new RegExp(search));
                    });

                    $found.removeClass('filtered').show();
                } else {
                    $models.removeClass('filtered').show();
                }

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            }
        },
    }
</script>

<style>
    #header {
        z-index: 1000;
    }
</style>
