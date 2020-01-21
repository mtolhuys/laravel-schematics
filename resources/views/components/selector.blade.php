<script>
    window.selector = function() {
        Selection.create({
            class: 'selection',
            selectables: ['.model'],
            boundaries: ['.schema']
        }).on('start', ({inst, selected, oe}) => {
            if (!oe.ctrlKey && !oe.metaKey) {
                for (const el of selected) {
                    $(el).removeClass('selected');
                    inst.removeFromSelection(el);
                }

                jsPlumb.clearDragSelection();
                inst.clearSelection();
            }
        }).on('move', ({changed: {removed, added}}) => {
            for (const el of added) {
                jsPlumb.addToDragSelection($(el));
                $(el).addClass('selected');
            }

            for (const el of removed) {
                $(el).removeClass('selected');
            }
        }).on('stop', ({inst}) => {
            inst.keepSelection();
        });
    };

    selector();
</script>

<style>
    .selection {
        background: rgba(46, 115, 252, 0.11);
        border-radius: 0.1em;
        border: 2px solid rgba(98, 155, 255, 0.81);
    }

    .selected {
        border: 2px double rgba(255, 71, 58, 0.81);
    }
</style>
