const ModelPositioning = {
    methods: {
        positionModels: function () {
            let $withoutRelations = this.$withoutRelations();

            this.$withRelations().each(function (i, element) {
                this.setModelPosition(element);
            });

            $withoutRelations.forEach(function (element) {
                this.setModelPosition(element);
            });
        },

        getModelPosition: function (model) {
            return JSON.parse(localStorage.getItem(`schematics-settings-${model.toLowerCase()}-position`));
        },

        setModelPosition: function (element) {
            let $model = $(element),
                model = $model.data('model').toLowerCase(),
                position = this.getModelPosition(model);

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

        $withRelations: function () {
            return $(".model:not(.no-relations)");
        },

        $withoutRelations: function () {
            let $withoutRelations = [];

            $('.model').each(function (i, el) {
                let $model = $(el),
                    noRelations = true,
                    table = $model.data('table').toLowerCase();

                for (const relationTable in Schematics.relations) {
                    if (relationTable === table) {
                        noRelations = false;
                    }

                    Schematics.relations[relationTable].forEach(function (relation) {
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
    }
};
