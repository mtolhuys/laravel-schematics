<div
    class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="modal-title text-2xl font-bold"></p>
                <div class="modal-close cursor-pointer z-50">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                         viewBox="0 0 18 18">
                        <path
                            d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>


            <div class="modal-content w-auto">
                <p>...</p>
            </div>

            <div class="flex justify-end pt-2">
                <button
                    class="modal-action px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2"
                ></button>
            </div>

        </div>
    </div>
</div>

<script>
    window.modal = {
        setTitle: function (title) {
            let $title = $('.modal-title');

            $title.html(title);
        },

        setContent: function (content) {
            let $content = $('.modal-content');

            $content.html(content);
        },

        setAction: function (label, action) {
            let $action = $('.modal-action');

            $action.unbind().text(label).click(action);
        },

        open: function () {
            $('.modal').removeClass('opacity-0 pointer-events-none');
        },

        close: function () {
            $('.modal').addClass('opacity-0 pointer-events-none');
        },
    };

    $('.modal-close').click(modal.close);
</script>

<style>
    .modal {
        z-index: 1100;
    }

    .modal-container {
        min-width: 600px;
    }
</style>
