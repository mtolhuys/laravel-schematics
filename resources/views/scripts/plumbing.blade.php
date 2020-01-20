<script>
    window.plumb = function () {
        loading(true);

        jsPlumb.deleteEveryEndpoint();

        Object.keys(relations).forEach(function (table) {
            relations[table].forEach(function (relation, index) {
                let $source = $(`#${table}:visible`),
                    $target = $(`#${relation.table}:visible`);

                if ($source.length && $target.length) {
                    jsPlumb.connect({
                        endpoint: 'Blank',
                        anchors: [
                            ['AutoDefault'],
                            ['AutoDefault']
                        ],
                        connector: ['Straight', {
                            curviness: 0,
                            stub: 20,
                        }],
                        source: $source,
                        target: $target,
                        overlays: [
                            ['Arrow', {location: 0.12, width: 10, length: 10}],
                            ['Label', {
                                cssClass: `relation rel-${table}-${index}`,
                                label: `<i class='fas icon fa-link'></i> <b>${relation.method.name}():</b> ${relation.type}`,
                                location: 0.33
                            }]
                        ],
                    });
                }
            });

            loading(false);
        });

        $('.relation').click(function () {
            let relation = $.grep(this.className.split(' '), function (c) {
                return c.indexOf('rel-') === 0;
            }).join().replace('rel-', '').split('-');

            relation = window.relations[relation[0]][relation[1]];
            relation.model = `${relation.model}`.split('\\').slice(-1)[0];
            relation.type = relation.type.charAt(0).toLowerCase() + relation.type.slice(1);

            modal.setTitle(
                 `${relation.model}.php(<span class="text-purple-400 text-lg">${relation.method.line}</span>)`
            );
            modal.setContent(
                `-><b class="text-purple-900">${relation.method.name}():</b>
                    <b class="text-black">${relation.type}('</b>
                        <i class="text-gray-800">${relation.relation}</i>
                    <b>')</b>;
                <br>`
            );
            modal.setAction('Open in IDE', function () {
                window.location.href = `jetbrains://php-storm/navigate/reference?project={{
                    config('schematics.project', basename(base_path()))
                }}&path=${relation.method.file}:${relation.method.line}`;
            });
            modal.open();
        });
    };

    jsPlumb.addGroup({
        el: models,
        Container: models,
        dragOptions: {
            start: function (event) {
                $(event.el).addClass('selected').css({'z-index': 101});
            },

            stop: function (event) {
                let $models = $('.model:visible');

                $models.removeClass('selected');

                $models.each(function (i, el) {
                    let $el = $(el);

                    localStorage.setItem(
                        `${$el.data('model').toLowerCase()}-position`,
                        JSON.stringify($el.position())
                    );
                });

                $(event.el).css({'z-index': 100});

                jsPlumb.clearDragSelection();
            }
        }
    });

    jsPlumb.ready(function () {
        plumb();
    });
</script>

<style>
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
