<nav
    id="header"
    x-data="navbar()"
    x-init="init()"
    class="fixed w-full z-20 top-0 bg-white border-b border-gray-400"
>
    <div
        class="w-full flex flex-wrap justify-between mt-0 py-4"
    >
        <div class="pl-4 flex items-center">
            <a class="text-gray-900 text-base no-underline hover:no-underline font-extrabold text-xl" href="#">
                <i class="fas fa-sitemap icon"></i> Laravel Schematics
            </a>

            <div class="flex-1 w-full mx-auto max-w-sm content-center py-4 lg:py-0">
                <div class="relative pull-right pl-4 pr-4 md:pr-0">
                    <input
                        x-on:keydown.enter="search()"
                        x-model="value"
                        type="search"
                        placeholder="(RegEx) Search..."
                        class="w-full bg-gray-100 text-sm text-gray-800 transition border focus:outline-none focus:border-purple-500 rounded py-1 px-2 pl-10 appearance-none leading-normal"
                    >

                    <div class="absolute search-icon" style="top: 0.375rem; left: 1.75rem;">
                        <svg class="fill-current pointer-events-none text-gray-800 w-4 h-4"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <ul class="flex">
                <li class="text-center font-bold flex-1">
                    <a class="block py-2 px-4 text-gray-800">
                        <span id="model-count">{{ count($models) }}</span> Model(s)
                    </a>
                </li>
            </ul>
        </div>

        <div class="flex justify-end pr-5 text-purple-300 text-lg">
            <div
                @click="hideModels()"
                class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
                <i class="fas fa-eye-slash"></i>
            </div>

            <div
                @click="showModels()"
                class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
                <i class="fas fa-eye"></i>
            </div>

            <div
                @click="zoomOut()"
                class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
                <i class="fas fa-search-minus"></i>
            </div>

            <div
                @click="zoomReset()"
                class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
                <i class="fas fa-search"></i>
            </div>


            <div
                @click="zoomIn()"
                class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
                <i class="fas fa-search-plus"></i>
            </div>
        </div>
    </div>
</nav>

<script>
    function navbar() {
        return {
            value: localStorage.getItem('schematics-search') || '',

            currentZoom: 1.0,

            init: function() {
                this.setZoom();
                this.search();
            },

            search: function () {
                let $models = $('.model');

                loading(true);

                localStorage.setItem('schematics-search', this.value);

                if (this.value.trim().length) {
                    let search = this.value.toLowerCase();

                    $models.hide();

                    try {
                        new RegExp(search);
                    } catch(e) {
                        console.error(`Invalid RegEx format: ${search}`);
                        $models.show();
                        _.defer(plumb);
                        return;
                    }

                    const $found = $models.filter(function() {
                            return $(this).data('model').match(new RegExp(search));
                    });

                    $found.show();
                } else {
                    $models.show();
                }

                $('#model-count').text($('.model:visible').length);

                _.defer(plumb);
            },

            setZoom: function () {
                this.currentZoom = parseFloat(localStorage.getItem('schematics-zoom')) || 1.0;

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomIn: function () {
                this.currentZoom += .1;

                localStorage.setItem('schematics-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomOut: function () {
                this.currentZoom -= .1;

                localStorage.setItem('schematics-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomReset: function () {
                this.currentZoom = 1;

                localStorage.setItem('schematics-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            selectedModels: function() {
                return !! $('.selected').length;
            },

            showModels: function() {
                $('.hidden-model').show();
                $('#model-count').text($('.model:visible').length);
                plumb();
            },

            hideModels: function() {
                $('.selected').addClass('hidden-model').hide();
                $('#model-count').text($('.model:visible').length);
                plumb();
            }
        }
    }
</script>

<style>
    #header {
        z-index: 900;
    }

    .button {
        cursor: pointer;
    }
</style>
