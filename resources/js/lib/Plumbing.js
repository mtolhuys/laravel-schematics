export default {
    data() {
        return {
            style: localStorage.getItem('schematics-settings-style') || 'Flowchart',
        }
    },

    created() {
        EventBus.$on('chart-style', style => this.style = style);
        EventBus.$on('plumb', this.plumb);
    },

    destroyed() {
        EventBus.$off('chart-style');
        EventBus.$off('plumb');
    },

    mounted() {
        jsPlumb.ready(() => {
            this.group();
        });
    },

    methods: {
        group() {
            jsPlumb.addGroup({
                el: $(".model"),
                dragOptions: {
                    start(event) {
                        $(event.el).addClass('selected').css({'z-index': 101});
                    },

                    stop(event) {
                        $('.model:visible').each(function (i, el) {
                            let $el = $(el);

                            localStorage.setItem(
                                `schematics-settings-${$el.data('model').toLowerCase()}-position`,
                                JSON.stringify($el.position())
                            );
                        });

                        setTimeout(() => {
                            $(".model").removeClass('selected').css({'z-index': 100});
                            jsPlumb.clearDragSelection();
                        }, 1);
                    }
                }
            });
        },

        plumb() {
            EventBus.$emit('loading', true);

            setTimeout(() => {
                jsPlumb.deleteEveryEndpoint();

                Object.keys(Schematics.relations).forEach((table) => {
                    Schematics.relations[table].forEach((relation, index) => {
                        let $source = $(`#${table}:visible`),
                            $target = $(`#${relation.relation.table}:visible`);

                        if ($source.length && $target.length) {
                            jsPlumb.connect({
                                source: $source,
                                target: $target,
                                endpoint: 'Blank',
                                anchors: [
                                    ['AutoDefault'],
                                    ['AutoDefault']
                                ],
                                connector: [this.style, this.getStyleSettings(this.style.toLowerCase())],
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

                $('.relation').unbind().click(function () {
                    let relation = $.grep(this.className.split(' '), (c) => {
                        return c.indexOf('rel-') === 0;
                    }).join().replace('rel-', '').split('-');

                    relation = Schematics.relations[relation[0]][relation[1]];
                    relation.model = `${relation.model}`.split('\\').slice(-1)[0];
                    relation.type = relation.type.charAt(0).toLowerCase() + relation.type.slice(1);

                    EventBus.$emit(
                        'modal-open',
                        `${relation.model}.php(<span class="text-purple-400 text-lg">${relation.method.line}</span>)`,
                        'relation',
                        relation
                    );
                });

                EventBus.$emit('loading', false);
            }, 1);
        },

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
    },
};
