<div
    x-data="charStyle()"
    x-init="init()"
    class="flex text-purple-300 text-lg"
>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1"><i class="fas icon fa-palette"></i></span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </button>

        <ul class="dropdown-menu absolute left-10 pt-5 hidden">
            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="setStyle('Bezier')"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-bezier-curve mr-2"></i> Bezier
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="setStyle('Straight')"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-draw-polygon mr-2"></i> Straight
                </div>
            </li>

            <li class="hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="setStyle('Flowchart')"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-sitemap mr-2"></i> Flowchart
                </div>
            </li>

            <li class="rounded-b hover:bg-purple-400 px-4 block whitespace-no-wrap bg-white text-gray-700 hover:text-white">
                <div
                    @click="setStyle('StateMachine')"
                    class="action inline-block button rounded-full px-4 py-2">
                    <i class="fas fa-project-diagram mr-2"></i> State Machine
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    function charStyle() {
        return {
            init: function () {

            },

            setStyle: function(style = 'Flowchart') {
                Schematics.style = style;

                localStorage.setItem('schematics-settings-style', Schematics.style);

                Schematics.plumb();
            }
        }
    }
</script>

<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu {
        right: -60px;
    }
</style>
