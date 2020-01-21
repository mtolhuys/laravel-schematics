<script>
    let $modal = $(".modal-container"),
        $action = $(".action");

    $(document).on('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && (String.fromCharCode(e.which).toLowerCase() === 'a')) {
            $models().each(function (i, el) {
                jsPlumb.addToDragSelection($(el));
                $(el).addClass('selected');
            });
        }
    });

    $(document).mousedown(function (e) {
        let notClicked = function ($el) {
            return !$el.is(e.target) && $el.has(e.target).length === 0
        };

        if (notClicked($models()) && notClicked($action)) {
            jsPlumb.clearDragSelection();
            $(".model").removeClass('selected');
        }

        if (notClicked($modal)) {
            modal.close();
        }
    });
</script>
