<body class="bg-gray-200">
    @php
        /**
        * @var array $models
        */
        $tables = [];
        $exceptions = [];

        foreach ($models as $index => $model) {
            try {
                $tables[$model] = app($model)->getTable();
            } catch (\Throwable $e) {
                unset($models[$index]);

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
            refresh: function() {
                $.get('schematics/refresh', function(response) {
                    Schematics.models = response.models;
                    Schematics.relations = response.relations;
                })
            },
        };
    </script>

    <div id="app">
        <schematics/>
    </div>

    <script type="module" src="{{ asset('vendor/schematics/app.js') }}"></script>
</body>
