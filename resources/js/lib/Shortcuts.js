export default {
    mounted() {
        document.addEventListener('keydown', this.listener.bind(this));
    },

    beforeDestroy() {
        document.removeEventListener('keydown', this.listener);
    },

    methods: {
        listener(e) {
            if (this.isEscape(e)) {
                e.preventDefault();

                EventBus.$emit('modal-close');
            }

            if (this.isSelectAllShortcut(e)) {
                e.preventDefault();

                this.selectAll();
            }
        },

        selectAll() {
            this.$models().all().each(function (i, el) {
                const $model = $(el).not('.hidden-model, .filtered');

                jsPlumb.addToDragSelection($model);

                $model.addClass('selected');
            });
        },

        isSelectAllShortcut(e) {
            return $(e.target).is('body')
                && (e.metaKey || e.ctrlKey)
                && (String.fromCharCode(e.which).toLowerCase() === 'a')
        },

        isEscape(e) {
            return $(e.target).is('body') && e.key === 'Escape';
        }
    }
};
