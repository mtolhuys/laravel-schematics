<script>
    let withoutRelations = [],
        minDistanceX = 250,
        minDistanceY = 200,
        currentPosition = {
            top: 80,
            left: 10
        },

        getPosition = function (model) {
            return JSON.parse(localStorage.getItem(`schematics-settings-${model}-position`));
        },

        safePosition = function (position) {
            position.top = position.top >= 80 ? position.top : 80;
            position.left = position.left >= 10 ? position.left : 10;

            return position;
        },

        setPosition = function ($model, model) {
            let position = getPosition(model);

            if (position) {
                $model.css(safePosition(position));
            } else {
                const posX = ($model.width() + minDistanceX),
                    posY = ($model.height() + minDistanceY);

                $model.css(currentPosition);

                localStorage.setItem(`schematics-settings-${model}-position`, JSON.stringify(currentPosition));

                if (currentPosition.left + (posX * 1.5) >= $(window).width()) {
                    currentPosition.left = 10;
                    currentPosition.top += posY;
                } else {
                    currentPosition.left += posX;
                }
            }
        };

    $models().each(function (i, model) {
        let $model = $(model);

        setPosition($model, $model.data('model').toLowerCase());
    });
</script>
