<script>
    let models = $(".model"),
        currentPosition = {
            top: 80,
            left: 20
        },

        getPosition = function (model) {
            return JSON.parse(localStorage.getItem(`${model}-position`));
        },

        setPosition = function (el, model) {
            let $el = $(el),
                position = getPosition(model);

            if (position) {
                position.top = position.top >= 80 ? position.top : 80;
                position.left = position.left >= 20 ? position.left : 20;

                $el.css(position);
            } else {
                $el.css(currentPosition);

                localStorage.setItem(`${model}-position`, JSON.stringify(currentPosition));

                if ((currentPosition.left + ($el.width() * 6)) >= $(window).width()) {
                    currentPosition.left = 20;
                    currentPosition.top += ($el.height() * 10);
                } else {
                    currentPosition.left += ($el.width() * 4);
                }
            }
        };

    models.each(function (i, el) {
        setPosition($(el), $(el).data('model').toLowerCase());
    });
</script>
