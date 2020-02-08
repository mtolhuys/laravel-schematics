<template>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1"><i class="fas icon fa-database"/></span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu absolute left-10 pt-5 hidden">
            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="runMigrations()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fab icon fa-laravel mr-2"/> migrate
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="rollbackMigrations()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fab icon fa-laravel mr-2"/> migrate:rollback
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="refreshMigrations()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fab icon fa-laravel mr-2"/> migrate:refresh
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="seed()"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fab icon fa-laravel mr-2"/> db:seed
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "migrations",

        methods: {
            runMigrations() {
                this.migrations('run');
            },

            rollbackMigrations() {
                this.migrations('rollback');
            },

            refreshMigrations() {
                this.migrations('refresh');
            },

            seed() {
                this.migrations('seed');
            },

            migrations(action) {
                EventBus.$emit('loading', true);

                $.post(`schematics/migrations/${action}`, (response) => {
                    EventBus.$emit('alert', response, 'info', 7000);

                    EventBus.$emit('loading', false);
                }).fail((e) => {
                    console.error(e);

                    EventBus.$emit('alert', e.statusText, 'error');
                    EventBus.$emit('loading', false);
                });
            }
        }
    }
</script>
