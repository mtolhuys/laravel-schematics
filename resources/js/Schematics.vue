<template>
    <div class="schematics">
        <nav-bar/>
        <loading/>
        <schema/>
        <modal/>
    </div>
</template>

<script>
    import NavigationBar from './components/NavigationBar.vue';
    import Loading from './components/Loading.vue';
    import Schema from './components/Schema.vue';
    import Modal from './components/Modal.vue';

    export default {
        name: 'schematics',

        components: {
            'modal': Modal,
            'nav-bar': NavigationBar,
            'loading': Loading,
            'schema': Schema,
        },

        mounted() {
            $(document).on('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && (String.fromCharCode(e.which).toLowerCase() === 'a')) {
                    $(".model").each(function (i, el) {
                        const $el = $(el).not('.hidden-model, .filtered');

                        jsPlumb.addToDragSelection($el);

                        $el.addClass('selected');
                    });
                }
            });

            $(document).mousedown(function (e) {
                let $model = $(".model"),
                    notClicked = function ($el) {
                    return !$el.is(e.target) && $el.has(e.target).length === 0
                };

                if (notClicked($model) && notClicked($(".action"))) {
                    $model.removeClass('selected');
                    jsPlumb.clearDragSelection();
                }

                if (notClicked($(".modal-container"))) {
                    EventBus.$emit('modal-close');
                }
            });
        }
    }
</script>

<style>
    html, body, .schema {
        position: relative;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        min-width: 100vw;
        min-height: 100vh;
        overflow: auto;
    }

    body {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .loading span {
        top: 50%;
    }

    .model {
        font-size: 0.9em;
        padding: 15px;
        margin: 5px;
        z-index: 100;
    }

    .model-name {
        cursor: grab;
    }

    .flex-wrap {
        flex-wrap: wrap;
        margin-right: -120px;
    }

    .action:hover {
        cursor: pointer;
    }

    .icon {
        color: #9F7AEA
    }

    .loading {
        z-index: 1000;
    }

    .jtk-overlay {
        padding: 3px;
        font-size: 0.75em;
        z-index: 99;
        background-color: #FFF;
        color: #4A5568;
        border: 1px solid #E2E8F0;
        border-radius: 5px;
    }

    .relation:hover {
        background-color: #f3f3f3;
    }

    .relation {
        cursor: pointer;
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #9F7AEA;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #9F7AEA;
    }

</style>
