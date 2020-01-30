<div class="schema" id="schema">
    @foreach($models as $model)
        @include('schematics::components.model', [
            'model' => $model,
        ])
    @endforeach
</div>

<script>
    jsPlumb.addGroup({
        el: Schematics.$models(),
        Container: Schematics.$models(),
        dragOptions: {
            start: function (event) {
                $(event.el).addClass('selected').css({'z-index': 101});
            },

            stop: function (event) {
                $('.model:visible').each(function (i, el) {
                    let $el = $(el);

                    localStorage.setItem(
                        `schematics-settings-${$el.data('model').toLowerCase()}-position`,
                        JSON.stringify($el.position())
                    );
                });

                $(event.el).css({'z-index': 100});
            }
        }
    });

    jsPlumb.ready(function () {
        Schematics.positionModels();
        Schematics.toggleModels();
        Schematics.selector();
        Schematics.plumb();

        $(document).on('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && (String.fromCharCode(e.which).toLowerCase() === 'a')) {
                Schematics.$models().each(function (i, el) {
                    const $el = $(el).not('.hidden-model, .filtered');

                    jsPlumb.addToDragSelection($el);

                    $el.addClass('selected');
                });
            }
        });

        $(document).mousedown(function (e) {
            let notClicked = function ($el) {
                return !$el.is(e.target) && $el.has(e.target).length === 0
            };

            if (notClicked(Schematics.$models()) && notClicked(Schematics.$actions())) {
                Schematics.clearSelection();
            }

            if (notClicked($(".modal-container"))) {
                modal.close();
            }
        });
    });
</script>
