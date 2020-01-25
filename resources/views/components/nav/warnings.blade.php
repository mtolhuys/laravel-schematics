<div
    x-data="warnings()"
    x-init="init()"
    class="flex text-purple-300 text-lg"
>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1">
                <i class="fas icon fa-exclamation-triangle"></i>
            </span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu exceptions absolute left-10 pt-5 hidden"></ul>
    </div>
</div>

<script>
    function warnings() {
        return {
            init: function () {
                for (let model in Schematics.exceptions) {
                    $('.exceptions').append(`
                        <li data-model=${model} data-exception="${Schematics.exceptions[model]}"
                            class="exception hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-red-700 hover:text-white">
                            <div class="action inline-block button rounded-full px-4 py-2">
                                <i class="fas fa-exclamation mr-2"></i> ${model}
                            </div>
                        </li>
                    `)
                }

                this.bindClicks();
            },

            bindClicks: function() {
                $('.exception').unbind().click(function () {
                    let model = $(this).data('model'),
                        exception = $(this).data('exception');

                    modal.setTitle(model);

                    modal.setContent(`Exception: <b class="text-red-700">${exception}</b>`);

                    modal.setAction();

                    modal.open();
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
