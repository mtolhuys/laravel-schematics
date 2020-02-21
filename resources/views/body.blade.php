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
            activeTab: parseInt(localStorage.getItem('active-tab') || 1),
            namespace: {!! json_encode(config('schematics.model-namespace', 'App\\')) !!},
            models: Object.values({!! json_encode($models) !!}),
            migrations: {!! json_encode($migrations) !!},
            relations: {!! json_encode($relations) !!},
            exceptions: {!! json_encode($exceptions) !!},
            tables: {!! json_encode($tables) !!},
            refresh: function() {
                $('body').css('cursor', 'progress');

                $.get('schematics/refresh', function(response) {
                    Schematics.models = response.models;

                    Schematics.relations = response.relations;
                    Schematics.migrations = response.migrations;

                    EventBus.$emit('refresh-navbar', response);

                    if (response.exception) {
                        EventBus.$emit(
                            'alert',
                            `${response.exception.title}: ${response.exception.message}`,
                            'error',
                            10000
                        );
                    }

                    $('body').css('cursor', 'default');
                })
            },
        };
    </script>

    <div id="app">
        <schematics/>
    </div>

    <script type="module" src="{{ asset('vendor/schematics/app.js') }}"></script>
</body>
