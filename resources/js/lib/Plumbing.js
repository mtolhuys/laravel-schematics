export default {
    data() {
        return {
            style: localStorage.getItem('schematics-settings-style') || 'Bezier',
        }
    },

    created() {
        EventBus.$on('chart-style', style => {
            this.style = Schematics.style = style;
            this.plumb();
        });

        EventBus.$on('plumb', this.plumb);
        EventBus.$on('group', this.group);
    },

    destroyed() {
        EventBus.$off('chart-style');
        EventBus.$off('plumb');
        EventBus.$off('group');
    },

    mounted() {
        jsPlumb.ready(() => {
            Schematics.style = this.style;

            jsPlumb.bind("beforeDrop", function (info) {
                if (info.sourceId === info.targetId) return;

                const $source = $(`#${info.sourceId}`),
                    $target = $(`#${info.targetId}`),
                    source = $source.data('model'),
                    target = $target.data('model'),
                    data = {
                        sourceTable: $source.data('table'),
                        targetTable: $target.data('table'),
                        source: source,
                        target: target,
                    };

                EventBus.$emit('modal-open', source, 'new-relation', data);

                setTimeout(() => {
                    EventBus.$emit('new-relation', data);
                    $('#method').focus();
                }, 1);
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

            if (jsPlumb.getConnections().length) {
                jsPlumb.deleteEveryEndpoint();
            }

            setTimeout(() => {
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

                this.$nextTick(() => {
                    EventBus.$emit('loading', false);

                    setTimeout(() => {
                        jsPlumb.repaintEverything();
                        EventBus.$emit('group');
                        this.bindRelationClicks();
                    }, 1);
                })
            }, 1);
        },

        bindRelationClicks() {
            const self = this;

            $('.relation').unbind().click(function () {
                let relation = $.grep(this.className.split(' '), c => {
                    return c.indexOf('rel-') === 0
                }).join().replace('rel-', '').split('-');

                self.fetch(relation);
            });
        },

        fetch(relation, max = 50, speed = 500) {
            const self = this;

            let ran = 0,
                handler,
                hasRelationData = () => {
                    return Object.keys(Schematics.relations).length
                        && Schematics.relations[relation[0]]
                        && Schematics.relations[relation[0]][relation[1]];
                },
                loop = () => {
                    Schematics.refresh();

                    ran++;

                    if (hasRelationData()) {
                        clearInterval(handler);
                        EventBus.$emit('loading', false);

                        self.openRelationModal(Schematics.relations[relation[0]][relation[1]]);
                    }

                    if (ran === max) {
                        clearInterval(handler);
                        EventBus.$emit('loading', false);

                        console.error('Could not fetch relations data for: ', relation);
                    }
                };

            if (hasRelationData()) {
                this.openRelationModal(Schematics.relations[relation[0]][relation[1]]);
            } else {
                EventBus.$emit('loading', true);

                handler = setInterval(loop, speed);
            }
        },

        openRelationModal(relation) {
            relation.model = `${relation.model}`.split('\\').slice(-1)[0];
            relation.type = relation.type.charAt(0).toLowerCase() + relation.type.slice(1);

            EventBus.$emit(
                'modal-open',
                `${relation.model}.php(<span class="text-purple-400 text-lg">${relation.method.line}</span>)`,
                'relation',
                relation
            );
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
