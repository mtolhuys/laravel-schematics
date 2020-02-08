<template>
    <div class="schematics">
        <nav-bar/>
        <loading/>
        <schema/>
        <modal/>
        <alert/>
    </div>
</template>

<script>
    import NavigationBar from './NavigationBar.vue';
    import Loading from './Loading.vue';
    import Schema from './Schema.vue';
    import Modal from './Modal.vue';
    import Alert from './Alert.vue';

    export default {
        name: 'schematics',

        components: {
            'nav-bar': NavigationBar,
            'loading': Loading,
            'schema': Schema,
            'modal': Modal,
            'alert': Alert,
        },

        mounted() {
            $(document).mousedown((e) => {
                let $models = this.$models().all(),
                    notClicked = function ($el) {
                    return !$el.is(e.target) && $el.has(e.target).length === 0
                };

                if (notClicked($models) && notClicked($(".action"))) {
                    $models.removeClass('selected');
                    jsPlumb.clearDragSelection();
                }

                if (notClicked($(".modal-container"))) {
                    EventBus.$emit('modal-close');
                }

                if (notClicked(this.$alert())) {
                    this.$alert().hide();
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
        border-radius: 50%;
    }

</style>
