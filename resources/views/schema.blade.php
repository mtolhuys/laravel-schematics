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

<style>
    html, body, .schema {
        position: relative;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        min-width: 3840px;
        min-height: 2160px;
        overflow: auto;
    }

    body {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .loading span {
        top: 50%;
    }

    .schema {
        zoom: 75%;
    }

    .model {
        font-size: 0.9em;
        padding: 15px;
        margin: 5px;
        z-index: 100;
    }

    .model-name {
        cursor: grab;
    }

    .flex-wrap {
        flex-wrap: wrap;
        margin-right: -120px;
    }

    .action:hover {
        cursor: pointer;
    }

    .icon {
        color: #9F7AEA
    }

    .loading {
        z-index: 1000;
    }

    .jtk-overlay {
        padding: 3px;
        font-size: 0.75em;
        z-index: 99;
        background-color: #FFF;
        color: #4A5568;
        border: 1px solid #E2E8F0;
        border-radius: 5px;
    }

    .relation:hover {
        background-color: #f3f3f3;
    }

    .relation {
        cursor: pointer;
    }
</style>
