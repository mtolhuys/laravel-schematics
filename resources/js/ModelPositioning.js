export default {
    data() {
        return {
            minDistanceX: 250,

            minDistanceY: 200,

            position: {
                top: 100,
                left: 10
            },
        }
    },

    methods: {
        positionModels() {
            const self = this,
                $withoutRelations = this.$withoutRelations();

            this.$withRelations().each(function (i, element) {
                self.setModelPosition(element);
            });

            $withoutRelations.forEach(this.setModelPosition);

            EventBus.$emit('plumb');
        },

        getModelPosition(model) {
            return JSON.parse(localStorage.getItem(`schematics-settings-${model.toLowerCase()}-position`));
        },

        setModelPosition(element) {
            let $model = $(element),
                model = $model.data('model').toLowerCase(),
                position = this.getModelPosition(model);

            if (position) {
                position.top = position.top >= 80 ? position.top : 80;
                position.left = position.left >= 10 ? position.left : 10;

                $model.css(position);
            } else {
                const posX = ($model.width() + this.minDistanceX),
                    posY = ($model.height() + this.minDistanceY);

                $model.css(this.position);

                localStorage.setItem(
                    `schematics-settings-${model}-position`,
                    JSON.stringify(this.position)
                );

                if (this.position.left + (posX * 1.5) >= $(window).width()) {
                    this.position.left = 10;
                    this.position.top += posY;
                } else {
                    this.position.left += posX;
                }
            }
        },

        $withRelations() {
            return $(".model:not(.no-relations)");
        },

        $withoutRelations() {
            let $withoutRelations = [];

            $('.model').each(function (i, el) {
                let $model = $(el),
                    noRelations = true,
                    table = $model.data('table');

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
