<div class="w-full h-full fixed block top-0 left-0 bg-white opacity-75 loading">
  <span class="text-purple-500 opacity-75 top-1/2 my-0 mx-auto block relative w-0 h-0">
    <i class="fas fa-circle-notch fa-spin fa-5x"></i>
  </span>
</div>

<script>
    window.Schematics = {
        models: {!! json_encode($models) !!},

        relations: {!! json_encode($relations) !!},

        zoom: 1.0,

        minDistanceX: 250,

        minDistanceY: 200,

        position: {
            top: 80,
            left: 10
        },

        style: localStorage.getItem('schematics-settings-style') || 'Flowchart',

        getStyleSettings: function (style) {
            return {
                'bezier': {
                    curviness: 100,
                },
                'straight': {
                    stub: 20,
                },
                'flowchart': {
                    alwaysRespectStubs: true,
                    midpoint: 0.6,
                    cornerRadius: 3,
                    stub: 10,
                },
                'statemachine': {
                    margin: -5,
                    curviness: 10,
                    proximityLimit: 100,
                },
            }[style]
        },

        loading: function (loading = false) {
            $('.loading').toggle(loading);
        },

        $models: function () {
            return $(".model");
        },

        $actions: function () {
            return $(".action");
        },

        $withRelations: function () {
            return $(".model:not(.no-relations)");
        },

        $withoutRelations: function() {
            let $withoutRelations = [];

            Schematics.$models().each(function(i, el) {
                let $model = $(el),
                    noRelations = true,
                    table = $model.data('table').toLowerCase();

                for (const relationTable in Schematics.relations) {
                    if (relationTable === table) {
                        noRelations = false;
                    }

                    Schematics.relations[relationTable].forEach(function(relation) {
                        if (relation.table === table) {
                            noRelations = false;
                        }
                    });
                }

                if (noRelations) {
                    $model.addClass('no-relations');
                    $withoutRelations.push($model);
                }
            });

            return $withoutRelations;
        },

        getModelPosition: function (model) {
            return JSON.parse(localStorage.getItem(`schematics-settings-${model}-position`));
        },

        positionModel: function (element) {
            let $model = $(element),
                model = $model.data('model').toLowerCase(),
                position = Schematics.getModelPosition($model);

            if (position) {
                position.top = position.top >= 80 ? position.top : 80;
                position.left = position.left >= 10 ? position.left : 10;

                $model.css(position);
            } else {
                const posX = ($model.width() + Schematics.minDistanceX),
                    posY = ($model.height() + Schematics.minDistanceY);

                $model.css(Schematics.position);

                localStorage.setItem(
                    `schematics-settings-${model}-position`,
                    JSON.stringify(Schematics.position)
                );

                if (Schematics.position.left + (posX * 1.5) >= $(window).width()) {
                    Schematics.position.left = 10;
                    Schematics.position.top += posY;
                } else {
                    Schematics.position.left += posX;
                }
            }
        },

        positionModels: function () {
            Schematics.$withRelations().each(function (i, element) {
                Schematics.positionModel(element);
            });

            Schematics.$withoutRelations().forEach(function (element) {
                Schematics.positionModel(element);
            });
        },

        plumb: function () {
            Schematics.loading(true);

            jsPlumb.deleteEveryEndpoint();

            Object.keys(Schematics.relations).forEach(function (table) {
                Schematics.relations[table].forEach(function (relation, index) {
                    let $source = $(`#${table}:visible`),
                        $target = $(`#${relation.table}:visible`);

                    if ($source.length && $target.length) {
                        jsPlumb.connect({
                            source: $source,
                            target: $target,
                            endpoint: 'Blank',
                            anchors: [
                                ['AutoDefault'],
                                ['AutoDefault']
                            ],
                            connector: [Schematics.style, Schematics.getStyleSettings(Schematics.style.toLowerCase())],
                            overlays: [
                                ['Arrow', {location: 0.2, width: 10, length: 10}],
                                ['Label', {
                                    cssClass: `relation rel-${table}-${index}`,
                                    label: `<i class='fas icon fa-link'></i> <b>${relation.method.name}():</b> ${relation.type}`,
                                    location: 0.4
                                }]
                            ],
                        });
                    }
                });
            });

            Schematics.relationsModals();
            Schematics.loading(false);
        },

        relationsModals: function () {
            $('.relation').unbind().click(function () {
                let relation = $.grep(this.className.split(' '), function (c) {
                    return c.indexOf('rel-') === 0;
                }).join().replace('rel-', '').split('-');

                relation = Schematics.relations[relation[0]][relation[1]];
                relation.model = `${relation.model}`.split('\\').slice(-1)[0];
                relation.type = relation.type.charAt(0).toLowerCase() + relation.type.slice(1);

                modal.setTitle(
                    `${relation.model}.php(<span class="text-purple-400 text-lg">${relation.method.line}</span>)`
                );

                modal.setContent(
                    `-><b class="text-purple-900">${relation.method.name}():</b>
                        <b class="text-black">${relation.type}(</b>
                            <i class="text-gray-800">'${relation.relation}'</i>
                        <b>)</b>;
                    <br>`
                );

                modal.setAction('Open in IDE', function () {
                    window.location.href = `jetbrains://php-storm/navigate/reference?project={{
                        config('schematics.project', basename(base_path()))
                    }}&path=${relation.method.file}:${relation.method.line}`;
                });

                modal.open();
            });
        },

        selector: function() {
            Selection.create({
                class: 'selection',
                selectables: ['.model'],
                boundaries: ['.schema']
            }).on('start', ({inst, selected, oe}) => {
                if (!oe.ctrlKey && !oe.metaKey) {
                    for (const el of selected) {
                        const $el = $(el).not('.hidden-model, .filtered');

                        $el.removeClass('selected');

                        inst.removeFromSelection($el);
                    }

                    jsPlumb.clearDragSelection();
                    inst.clearSelection();
                }
            }).on('move', ({changed: {removed, added}}) => {
                for (const el of added) {
                    const $el = $(el).not('.hidden-model, .filtered');

                    $el.addClass('selected');

                    jsPlumb.addToDragSelection($el);
                }

                for (const el of removed) {
                    $(el).not('.hidden-model, .filtered').removeClass('selected');
                }
            }).on('stop', ({inst}) => {
                inst.keepSelection();
            });
        },

        clearSelection: function() {
            this.$models().removeClass('selected');
            jsPlumb.clearDragSelection();
        }
    };
</script>

<style>
    .selection {
        background: rgba(46, 115, 252, 0.11);
        border-radius: 0.1em;
        border: 2px solid rgba(98, 155, 255, 0.81);
    }

    .selected {
        border: 2px double rgba(255, 71, 58, 0.81);
    }
</style>
