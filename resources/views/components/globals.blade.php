<div class="w-full h-full fixed block top-0 left-0 bg-white opacity-75 loading">
  <span class="text-purple-500 opacity-75 top-1/2 my-0 mx-auto block relative w-0 h-0">
    <i class="fas fa-circle-notch fa-spin fa-5x"></i>
  </span>
</div>

<script>
    window.models = {!! json_encode($models) !!};
    window.relations = {!! json_encode($relations) !!};

    window.loading = function (loading = false) {
        $('.loading').toggle(loading);
    };

    window.$models = function() {
        return $(".model");
    };
</script>

<style>
    .loading span {
        top: 50%;
    }
</style>
