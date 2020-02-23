<template>
    <div class="sidebar bg-white h-screen fixed border-r border-gray-400">
        <div v-for="tab in tabs" :key="tab" class="flex tab text-lg">
            <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
                <button @click="setTab(tab)"
                        :class="{ 'text-purple-600' : active === tab }"
                        class="tooltip focus:outline-none text-black inline-flex mr-2
                        items-center text-purple-300 hover:text-purple-500">
                    <i class="fas fa-sitemap mb-2 pr-1"/> {{ tab }}
                </button>
            </div>
        </div>

        <div class="inline-block w-full relative bg-transparent pt-6 pl-4">
            <span class="plus-minus">
                <button
                    :disabled="tabs === 1"
                    @click="removeTab()"
                    class="tooltip text-black inline-flex items-center
                         focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-minus"/></span>
                </button>

                <button
                    :disabled="tabs >= 10"
                    @click="addTab()"
                    class="tooltip text-black inline-flex items-center
                     focus:outline-none text-purple-300 hover:text-purple-500">
                    <span class="mr-1"><i class="fas fa-plus"/></span>
                </button>
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'side-bar',

        data() {
            return {
                active: Schematics.activeTab,
                tabs: parseInt(localStorage.getItem('tabs') || 1),
            }
        },

        methods: {
            setTab(tab) {
                Schematics.activeTab = tab;

                localStorage.setItem('schematics-active-tab', tab);

                EventBus.$emit('loading', true);

                setTimeout(() => {
                    location.reload();
                }, 1);
            },

            addTab() {
                this.tabs++;

                localStorage.setItem('schematics-tabs', this.tabs);

                this.setTab(this.tabs);
            },

            removeTab() {
                Object.keys(localStorage).filter((key) => {
                    return (key.indexOf('schematics-settings') === 0)
                        && (key.includes(`tab-${this.tabs}`));
                }).forEach(function (key) {
                    localStorage.removeItem(key);
                });

                this.tabs--;

                localStorage.setItem('schematics-tabs', this.tabs);

                this.setTab(this.tabs);
            },
        }
    }
</script>

<style>
    .sidebar {
        top: 70px;
        width: 70px;
        z-index: 200;
    }

    .tab {
        padding-top: 30px;
        height: 70px;
    }
</style>
