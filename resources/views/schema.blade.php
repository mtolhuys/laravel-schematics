<div class="schema" id="schema">
    @foreach($models as $model)
        @include('schematics::components.model', [
            'model' => $model,
        ])
    @endforeach
</div>
