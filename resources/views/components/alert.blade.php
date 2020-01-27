<div
    x-data="alert()"
    class="alert hidden fixed bottom-0 right-0 m-8 w-5/6 md:w-full max-w-sm">
    <label
        class="content flex items-start justify-between w-full p-2 h-24 rounded shadow-lg text-white"
        for="toast"
    >

        <span class="px-6 py-3 font-bold alert-text"></span>

        <svg
            @click="close()"
            class="fill-current text-white cursor-pointer "
            xmlns="http://www.w3.org/2000/svg"
            width="18" height="18"
            viewBox="0 0 18 18"
        >
            <path
                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"
            ></path>
        </svg>
    </label>
</div>

<script>
    function alert() {
        return {
            close: function () {
                $(".alert").hide();
            }
        }
    }
</script>

<style>
    /*Toast open/load animation*/
    .alert {
        -webkit-animation: slide-in-right 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
        animation: slide-in-right 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
    }

    /*Toast close animation*/
    .alert input:checked ~ * {
        -webkit-animation: fade-out-right 0.7s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
        animation: fade-out-right 0.7s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
    }

    @-webkit-keyframes slide-in-top {
        0% {
            -webkit-transform: translateY(-1000px);
            transform: translateY(-1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
    }

    @keyframes slide-in-top {
        0% {
            -webkit-transform: translateY(-1000px);
            transform: translateY(-1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
    }

    @-webkit-keyframes slide-out-top {
        0% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateY(-1000px);
            transform: translateY(-1000px);
            opacity: 0
        }
    }

    @keyframes slide-out-top {
        0% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateY(-1000px);
            transform: translateY(-1000px);
            opacity: 0
        }
    }

    @-webkit-keyframes slide-in-bottom {
        0% {
            -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
    }

    @keyframes slide-in-bottom {
        0% {
            -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
    }

    @-webkit-keyframes slide-out-bottom {
        0% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
            opacity: 0
        }
    }

    @keyframes slide-out-bottom {
        0% {
            -webkit-transform: translateY(0);
            transform: translateY(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateY(1000px);
            transform: translateY(1000px);
            opacity: 0
        }
    }

    @-webkit-keyframes slide-in-right {
        0% {
            -webkit-transform: translateX(1000px);
            transform: translateX(1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
            opacity: 1
        }
    }

    @keyframes slide-in-right {
        0% {
            -webkit-transform: translateX(1000px);
            transform: translateX(1000px);
            opacity: 0
        }
        100% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
            opacity: 1
        }
    }

    @-webkit-keyframes fade-out-right {
        0% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateX(50px);
            transform: translateX(50px);
            opacity: 0
        }
    }

    @keyframes fade-out-right {
        0% {
            -webkit-transform: translateX(0);
            transform: translateX(0);
            opacity: 1
        }
        100% {
            -webkit-transform: translateX(50px);
            transform: translateX(50px);
            opacity: 0
        }
    }
</style>
