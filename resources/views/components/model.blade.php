@php
    /**
    * @var string $model
    * @var string $exceptions
    */

    try {
        $table = app($model)->getTable();
    } catch (\Throwable $e) {
        $exception = $e->getMessage();
    }
@endphp

@if (isset($table))
    <div
        x-data="model()"
        class="model-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg overflow-y-auto">
        <span
            id="{{ $table }}"
            data-table="{{ $table }}"
            data-model-class="{{ $model }}"
            data-model="{{ strtolower($model) }}"
            class="model absolute text-left flex justify-between
                bg-white hover:bg-gray-100 text-black font-bold
                border-b-4 border-purple-300 hover:border-purple-400 rounded shadow-lg
            ">
            <i class="fas fa-project-diagram icon"></i> {{ $model }}

            <div
                @click="hide()"
                class="px-4 model-hide cursor-pointer">
                <i class="far fa-eye-slash text-gray-400 hover:text-purple-700"></i>
            </div>

            <div
                @click="edit({{ $table }})"
                class="cursor-pointer edit">
                <i class="fas fa-search text-gray-400 hover:text-purple-700"></i>
            </div>
        </span>
    </div>
@endif

<script>
    @if (isset($table))
    function model() {
        return {
            hide: function () {
                Schematics.toggleModel($(this.$el), true);
            },

            edit: function (el) {
                Schematics.loading(true);

                modal.setTitle($(el).data('model-class'));
                modal.setAction(null);

                $.get(`schematics/details/${$(el).data('table')}`, function (fields) {
                    let content = '';

                    fields.forEach(function (field) {
                        content += `
                            <div class="md:flex md:items-center">
                                <div class="md:w-1/3">
                                    <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="${field.Field}">
                                        ${field.Field}
                                    </label>
                                </div>
                                <div class="md:w-2/3">
                                    <input
                                        value="${field.Type}"
                                        id="${field.Field}"
                                        class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                                        type="text"
                                        disabled
                                    >
                                </div>
                              </div>
                            `;
                    });

                    modal.setContent(content);
                    modal.open();
                    Schematics.loading(false);
                }).fail(function (e) {
                    console.error(e);
                    Schematics.alert(e.statusText, 'error');
                    Schematics.loading(false);
                });
            }
        }
    }

    @else
        Schematics.exceptions['{!! json_encode($model) !!}'] = {!! json_encode($exception) !!};
    @endif
</script>

