<template>
    <div class="preloader" v-if="show">
        <div class="image"></div>
    </div>
</template>

<script>
    import gsap from 'gsap';

    export default {
        data() {
            return {
                show: true
            }
        },
        created() {
            this.show = document.readyState !== "complete";

            if(this.show) {
                window.addEventListener('load', () => gsap.to('.preloader', {
                    opacity: 0,
                    onComplete: () => this.show = false,
                    ease: 'easeOut',
                    duration: .3
                }));
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";
    @import "resources/sass/themes";

    .preloader {
        z-index: 99999999;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 5em;

        @include themed() {
            background: t('body');
        }

        .image {
            width: 350px;
            height: 50px;
            background: url('/img/misc/preloader_logo.png') no-repeat center;
            background-size: contain;
            -webkit-mask-image: linear-gradient(-75deg, rgba(0,0,0,.8) 30%, #000 50%, rgba(0,0,0,.8) 70%);
            -webkit-mask-size: 200%;
            animation: shine 1s linear infinite;

            @keyframes shine {
                from { -webkit-mask-position: 150%; }
                to { -webkit-mask-position: -50%; }
            }
        }
    }
</style>
