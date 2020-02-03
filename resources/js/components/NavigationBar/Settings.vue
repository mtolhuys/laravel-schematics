<template>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1"><i class="fas icon fa-cog"/></span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu absolute left-10 pt-5 hidden">
            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="exportSettings()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-file-export mr-2"/> Export Settings
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <input
                    type="file"
                    id="import-settings"
                    class="hidden"
                    name="settings"
                />
                <div
                    @click="importSettings()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-file-import mr-2"/> Import Settings
                </div>
            </li>

            <li class="action hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="hideModels()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-eye-slash mr-2"/> Hide Selected Models
                </div>
            </li>

            <li class="action hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="showModels()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-eye mr-2"/> Show Hidden Models
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="clearCache()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-broom mr-2"/> Clear Cache
                </div>
            </li>

            <li class="rounded-b hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="reset()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-redo-alt mr-2"/> Reset Diagram
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "settings",

        methods: {
            importSettings() {
                let $models = this.$models().all();

                $('#import-settings').click();

                $("#import-settings:file").change(function() {
                    const fileReader = new FileReader();

                    fileReader.readAsText($(this).prop('files')[0]);

                    fileReader.onload = () => {
                        let value = fileReader.result;

                        try {
                            EventBus.$emit('loading', true);

                            localStorage.clear();

                            value.split('\n').forEach(eval);

                            EventBus.$emit('alert', 'Settings successfully imported!<br>Loading...', 'info');

                            jsPlumb.deleteEveryConnection();

                            $models.hide();

                            location.reload();
                        } catch (e) {
                            console.error(e.message);

                            EventBus.$emit('loading', false);
                            EventBus.$emit('alert', 'Invalid import file!', 'error');
                        }
                    };
                });
            },

            exportSettings() {
                let download = document.createElement('a'),
                    content = '';

                EventBus.$emit('loading', true);

                Object.keys(localStorage).filter((key) => {
                    return key.indexOf('schematics-settings') === 0;
                }).forEach(function (key) {
                    content += `localStorage.setItem(${JSON.stringify(key)}, '${localStorage.getItem(key)}');\n`
                });

                download.setAttribute("href", "data:text/javascript;charset=utf-8," + content);
                download.setAttribute("download", "schematics-settings.js");

                document.body.appendChild(download);

                download.click();
                download.remove();

                EventBus.$emit('loading', false);
            },

            clearCache() {
                EventBus.$emit('loading', true);

                $.get('schematics/clear-cache', () => {
                    location.reload();
                });
            },

            showModels() {
                let $hidden = this.$models().hidden();

                $hidden.removeClass('hidden-model').show();
                $hidden.each((i, el) => {
                    localStorage.setItem(`schematics-settings-${$(el).data('model')}-hidden`, 'false');
                });

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            },

            hideModels() {
                let $selected = this.$selected();

                $selected.addClass('hidden-model').hide();
                $selected.each((i, el) => {
                    localStorage.setItem(`schematics-settings-${$(el).data('model')}-hidden`, 'true');
                });

                this.$models().count().text(this.$models().visible().length);

                EventBus.$emit('plumb');
            },

            reset() {
                EventBus.$emit('loading', true);

                this.$models().hidden().removeClass('hidden-model filtered').show();
                this.$models().count().text(this.$models().visible().length);

                localStorage.clear();

                $.get('schematics/clear-cache', () => {
                    location.reload();
                });
            }
        }
    }
</script>
