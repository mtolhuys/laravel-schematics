<body class="bg-gray-200">

@php
    /**
    * @var array $models
    */
    $tables = [];
    $exceptions = [];

    foreach ($models as $model) {
        try {
            $tables[$model] = app($model)->getTable();
        } catch (\Throwable $e) {
            $exceptions[$model] = $e->getMessage();
        }
    }
@endphp

<script>
    window.Schematics = {
        models: {!! json_encode($models) !!},
        relations: {!! json_encode($relations) !!},
        tables: {!! json_encode($tables) !!},
        exceptions: {!! json_encode($exceptions) !!},
        exceptions: {!! json_encode($exceptions) !!},
    };
</script>

<div id="app">
    <schematics/>
</div>

<script type="module" src="{{ asset('vendor/schematics/app.js') }}"></script>

</body>
