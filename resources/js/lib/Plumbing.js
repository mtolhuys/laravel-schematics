export default {
    data() {
        return {
            style: localStorage.getItem('schematics-settings-style') || 'Bezier',
        }
    },

    created() {
        EventBus.$on('chart-style', style => {
            this.style = Schematics.style = style;
            jsPlumb.deleteEveryEndpoint();
            this.plumb();
        });

        EventBus.$on('plumb', this.plumb);
    },

    destroyed() {
        EventBus.$off('chart-style');
        EventBus.$off('plumb');
    },

    mounted() {
        jsPlumb.ready(() => {
            Schematics.style = this.style;

            this.group();

            jsPlumb.bind("beforeDrop", function (info) {
                if (info.sourceId === info.targetId) return;

                const $source = $(`#${info.sourceId}`),
                    source = $source.data('model'),
                    target = $(`#${info.targetId}`).data('model'),
                    data = {
                        table: $source.data('table'),
                        source: source,
                        target: target,
                    };

                EventBus.$emit('modal-open', source, 'new-relation', data);
                EventBus.$emit('new-relation', data);
            });
        });
    },

    methods: {
        group() {
            jsPlumb.addGroup({
                el: this.$models().all(),
                dragOptions: {
                    start(event) {
                        $(event.el).addClass('selected').css({'z-index': 101});
                    },

                    stop() {
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
                try {
                    jsPlumb.deleteEveryEndpoint();
                } catch (e) {
                    console.error(e);
                }

                Object.keys(Schematics.relations).forEach((table) => {
                    Schematics.relations[table].forEach((relation, index) => {
                        let $source = $(`#${table}:visible`),
                            $target = $(`#${relation.relation.table}:visible`);

                        if ($source.length && $target.length) {
                            jsPlumb.connect({
                                source: $source,
                                target: $target,
                                newConnection: false,
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

                this.bindRelationClicks();

                this.$nextTick(() => {
                    EventBus.$emit('loading', false);

                    setTimeout(jsPlumb.repaintEverything, 1);
                })
            }, 1);
        },

        bindRelationClicks() {
            $('.relation').unbind().click(function () {
                let relation = $.grep(this.className.split(' '), c => {
                    return c.indexOf('rel-') === 0
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
        },

        getStyleSettings(style) {
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
