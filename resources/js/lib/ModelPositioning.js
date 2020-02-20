export default {
    data() {
        return {
            minDistanceX: 250,

            minDistanceY: 200,

            position: {
                top: 100,
                left: 80
            },

            $withRelations: [],

            $withoutRelations: [],
        }
    },

    methods: {
        defineModels() {
            this.$withoutRelations = this.$models().withoutRelations();
            this.$withRelations = this.$models().withRelations();
        },

        getModelPosition(model) {
            return JSON.parse(
                localStorage.getItem(`schematics-settings-${model.toLowerCase()}-position-tab-${Schematics.activeTab}`)
            );
        },

        setModelsPosition() {
            this.defineModels();

            this.$withRelations.forEach(this.setModelPosition);
            this.$withoutRelations.forEach(this.setModelPosition);
        },

        setModelPosition(element) {
            let $model = $(element),
                model = $model.data('model').toLowerCase(),
                position = this.getModelPosition(model);

            if (position) {
                position.top = position.top >= 80 ? position.top : 80;
                position.left = position.left >= 80 ? position.left : 80;

                $model.css(position);
            } else {
                const posX = ($model.width() + this.minDistanceX),
                    posY = ($model.height() + this.minDistanceY);

                $model.css(this.position);

                localStorage.setItem(
                    `schematics-settings-${model}-position-tab-${Schematics.activeTab}`,
                    JSON.stringify(this.position)
                );

                if (this.position.left + (posX * 1.5) >= $(window).width()) {
                    this.position.left = 80;
                    this.position.top += posY;
                } else {
                    this.position.left += posX;
                }
            }
        },
    }
};
