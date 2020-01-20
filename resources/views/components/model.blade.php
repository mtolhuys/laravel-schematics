@php
    /**
    * @var string $model
    */
    $table = (new $model)->getTable();
    // $exists = \Schema::hasTable($table);
    // $columns = $exists ? \DB::select(\DB::raw("SHOW COLUMNS FROM {$table}")) : [];
@endphp

<button
    id="{{ $table }}"
    data-table="{{ $table }}"
{{--    data-columns="{{ json_encode($columns) }}"--}}
    data-model="{{ strtolower($model) }}"
    class="model absolute
        bg-white hover:bg-gray-100 text-black font-bold py-6 px-4
        border-b-4 border-purple-300 hover:border-purple-400 rounded shadow-lg
    ">
    <i class="fas fa-project-diagram icon"></i> {{ $model }}
</button>

