export default {
    data() {
        return {
            minDistanceX: 250,

            minDistanceY: 200,

            position: {
                top: 100,
                left: 10
            },

            $withRelations: [],

            $withoutRelations: [],
        }
    },

    mounted() {
        this.$withoutRelations = this.$models().withoutRelations();
        this.$withRelations = this.$models().withRelations();
    },

    methods: {
        getModelPosition(model) {
            return JSON.parse(
                localStorage.getItem(`schematics-settings-${model.toLowerCase()}-position`)
            );
        },

        setModelsPosition() {
            this.$withRelations.forEach(this.setModelPosition);
            this.$withoutRelations.forEach(this.setModelPosition);

            EventBus.$emit('plumb');
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
    }
};
