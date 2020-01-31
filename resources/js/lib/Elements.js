export default {
    methods: {
        $schema() {
            return $('.schema');
        },

        $selected() {
            return $('.selected');
        },

        $actions() {
            return $('.action');
        },

        $alert() {
            return $('.alert');
        },

        $models() {
            return {
                all() {
                    return $('.model');
                },

                count() {
                    return $('#model-count');
                },

                visible() {
                    return $('.model:visible');
                },

                hidden() {
                    return $('.hidden-model');
                },

                unhidden() {
                    return $('.model:not(.hidden-model)');
                },

                withRelations() {
                    return $(".model:not(.no-relations)").toArray();
                },

                withoutRelations() {
                    let $withoutRelations = [];

                    this.all().each((i, el) => {
                        let $model = $(el),
                            noRelations = true,
                            table = $model.data('table');

                        for (const relationTable in Schematics.relations) {
                            if (relationTable === table) {
                                noRelations = false;
                            }

                            Schematics.relations[relationTable].forEach((relation) => {
                                if (relation.relation.table === table) {
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
            };
        },
    }
};
