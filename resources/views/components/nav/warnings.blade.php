<div
    x-data="warnings()"
    x-init="init()"
    class="flex warnings text-purple-300 text-lg"
>
    <div class="dropdown inline-block relative bg-transparent pt-2 pl-5">
        <button class="text-black inline-flex items-center">
            <span class="mr-1">
                <i class="fas icon fa-exclamation-triangle"></i>
            </span>
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
            </button>

        <ul class="dropdown-menu exceptions absolute left-10 pt-5 hidden"></ul>
    </div>
</div>

<script>
    
</script>

<style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu {
        right: -60px;
    }
</style>
