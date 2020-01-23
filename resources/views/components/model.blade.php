@php
    /**
    * @var string $model
    */
    $table = (new $model)->getTable();
    // $exists = \Schema::hasTable($table);
    // $columns = $exists ? \DB::select(\DB::raw("SHOW COLUMNS FROM {$table}")) : [];
@endphp

<div class="model-container bg-white w-full md:max-w-md mx-auto rounded shadow-lg overflow-y-auto">
<span
    id="{{ $table }}"
    x-data="model()"
    data-table="{{ $table }}"
    {{--    data-columns="{{ json_encode($columns) }}"--}}
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
</span>
</div>

<script>
    function model() {
        return {
            hide: function() {
                $(this.$el).parent().addClass('hidden-model').hide();
                $('#model-count').text($('.model:visible').length);
                Schematics.plumb();
            }
        }
    }
</script>

