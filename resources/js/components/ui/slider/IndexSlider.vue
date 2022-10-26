<template>
    <div class="slider">
        <div class="glide" id="slider" :class="!isGuest ? 'logged' : ''">
            <template v-if="isGuest">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/betdino-slide-1.png') !important; background-size: cover !important; background-position: 80% center !important;">
                            <slider-description class="slider-only"></slider-description>
                        </li>
                    </ul>
                </div>
                <!--<div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
                </div>-->
                <div class="glide__bullets" data-glide-el="controls[nav]">
                    <button class="glide__bullet" data-glide-dir="=0"></button>
                </div>
            </template>
            <template v-else>
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/bg-dino.png') !important; background-size: cover !important; background-position: 80% center !important;">
                            <slider-description class="slider-only"></slider-description>
                        </li>
                    </ul>
                </div>
                <!--<div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
                </div>-->
                <div class="glide__bullets" data-glide-el="controls[nav]">
                    <button class="glide__bullet" data-glide-dir="=0"></button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Glide from '@glidejs/glide';

    export default {
        computed: {
            ...mapGetters(['isGuest'])
        },
        methods: {
            initSlider() {
                setTimeout(() => {
                    new Glide('#slider', {
                        type: 'carousel',
                        perView: 1,
                        peek: 0,
                        focusAt: 'center',
                        gap: 0,
                        autoplay: 10000,
                        keyboard: false
                    }).mount();
                });
            }
        },
        mounted() {
            this.initSlider();
        }
    }
</script>
