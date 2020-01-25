<div
    x-data="actions()"
    x-init="init()"
    class="flex justify-end mr-10 text-purple-300 text-lg"
>
    <div
        @click="zoom(-.1)"
        class="action inline-block button rounded-full px-5 pt-2 hover:text-purple-500">
        <i class="fas fa-search-minus"></i>
    </div>

    <div
        @click="zoom()"
        class="action inline-block button rounded-full px-5 pt-2 hover:text-purple-500">
        <i class="fas fa-search-plus"></i>
    </div>

    @include('schematics::components.nav.warnings')

    @include('schematics::components.nav.style')

    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1"><i class="fas icon fa-cog"></i></span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu absolute left-10 pt-5 hidden">
            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="exportSettings()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-file-import mr-2"></i> Export Settings (localStorage)
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="hideModels()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-eye-slash mr-2"></i> Hide Selected Models
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="showModels()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-eye mr-2"></i> Show Hidden Models
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="clearCache()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-broom mr-2"></i> Clear Cache
                </div>
            </li>

            <li class="rounded-b hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="reset()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-redo-alt mr-2"></i> Reset Diagram
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    function actions() {
        return {
            init: function () {
                this.setZoom();
            },

            exportSettings: function () {
                let download = document.createElement('a'),
                    content = '';

                Schematics.loading(true);

                Object.keys(localStorage).filter(function (key) {
                    return key.indexOf('schematics-settings') === 0;
                }).forEach(function (key) {
                    content += `localStorage.setItem(${JSON.stringify(key)}, '${localStorage.getItem(key)}');\n`
                });

                download.setAttribute("href", "data:text/javascript;charset=utf-8," + content);
                download.setAttribute("download", "schematics-settings.js");

                document.body.appendChild(download);

                download.click();
                download.remove();

                Schematics.loading(false);
            },

            clearCache: function () {
                Schematics.loading(true);

                $.get('schematics/clear-cache', function() {
                    location.reload();
                });
            },

            setZoom: function () {
                Schematics.zoom = parseFloat(localStorage.getItem('schematics-zoom')) || 1.0;

                $('#schema').animate({'zoom': Schematics.zoom}, 'slow');
            },

            zoom: function (zoom = .1) {
                Schematics.zoom += zoom;

                localStorage.setItem('schematics-settings-zoom', '' + Schematics.zoom);

                $('#schema').animate({'zoom': Schematics.zoom}, 'slow');
            },

            zoomReset: function () {
                Schematics.zoom = 1;

                localStorage.setItem('schematics-settings-zoom', '' + Schematics.zoom);

                $('#schema').animate({'zoom': Schematics.zoom}, 'slow');
            },

            showModels: function () {
                $('.hidden-model').removeClass('hidden-model').show();
                $('#model-count').text($('.model:visible').length);
                Schematics.plumb();
            },

            hideModels: function () {
                $('.selected').addClass('hidden-model').hide();
                $('#model-count').text($('.model:visible').length);
                Schematics.plumb();
            },

            reset() {
                Schematics.loading(true);
                $('.hidden-model').removeClass('hidden-model filtered').show();
                $('#model-count').text($('.model:visible').length);
                localStorage.clear();

                $.get('schematics/clear-cache', function() {
                    location.reload();
                });
            }
        }
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
