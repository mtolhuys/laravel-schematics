<div
    x-data="actions()"
    x-init="init()"
    class="flex justify-end pr-5 text-purple-300 text-lg"
>
    <div class="dropdown inline-block relative bg-transparent pt-2">
        <button class="text-black inline-flex items-center">
            <span class="mr-1">Chart Style</span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/> </svg>
        </button>

        <ul class="dropdown-menu absolute hidden text-gray-700 pt-1">
            <li class=""><a class="rounded-t hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap" href="#">One</a></li>
            <li class=""><a class="hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap" href="#">Two</a></li>
            <li class=""><a class="rounded-b hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap" href="#">Three is the magic number</a></li>
        </ul>
    </div>

    <div
        @click="exportSettings()"
        class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
        <i class="fas fa-file-import"></i>
    </div>

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
        @click="zoomIn()"
        class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
        <i class="fas fa-search-plus"></i>
    </div>

    <div
        @click="reset()"
        class="action inline-block button rounded-full px-4 py-2 hover:text-purple-500">
        <i class="fas fa-recycle"></i>
    </div>
</div>

<script>
    function actions() {
        return {
            currentZoom: 1.0,

            init: function () {
                this.setZoom();
            },

            exportSettings: function () {
                let download = document.createElement('a'),
                    content = '';

                Object.keys(localStorage).filter(function(key) {
                    return key.indexOf('schematics-settings') === 0;
                }).forEach(function (key) {
                    content += `localStorage.setItem(${JSON.stringify(key)}, '${localStorage.getItem(key)}');\n`
                });

                download.setAttribute("href", "data:text/javascript;charset=utf-8," + content);
                download.setAttribute("download", "schematics-settings.js");

                document.body.appendChild(download);

                download.click();
                download.remove();
            },

            search: function () {
                let $models = $('.model');

                Schematics.loading(true);

                localStorage.setItem('schematics-settings-search', this.value);

                if (this.value.trim().length) {
                    let search = this.value.toLowerCase();

                    $models.hide();

                    try {
                        new RegExp(search);
                    } catch (e) {
                        console.error(`Invalid RegEx format: ${search}`);
                        $models.show();
                        setTimeout(Schematics.plumb, 1);
                        return;
                    }

                    const $found = $models.filter(function () {
                        return $(this).data('model').match(new RegExp(search));
                    });

                    $found.show();
                } else {
                    $models.show();
                }

                $('#model-count').text($('.model:visible').length);

                setTimeout(Schematics.plumb, 1);
            },

            setZoom: function () {
                this.currentZoom = parseFloat(localStorage.getItem('schematics-zoom')) || 1.0;

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomIn: function () {
                this.currentZoom += .1;

                localStorage.setItem('schematics-settings-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomOut: function () {
                this.currentZoom -= .1;

                localStorage.setItem('schematics-settings-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            zoomReset: function () {
                this.currentZoom = 1;

                localStorage.setItem('schematics-settings-zoom', '' + this.currentZoom);

                $('#schema').animate({'zoom': this.currentZoom}, 'slow');
            },

            showModels: function () {
                $('.hidden-model').show();
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
                $('.hidden-model').show();
                $('#model-count').text($('.model:visible').length);
                localStorage.clear();
                location.reload();
            }
        }
    }
</script>

<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>
