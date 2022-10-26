<template>
    <div class="container">
        <slider-description class="mobile-only"></slider-description>
        <index-slider></index-slider>
        <div class="mobile-info">
            <div class="block b-1" @click="openFaucetModal"></div>
        </div>
        <div class="slider-2" v-if="!isGuest">
            <div class="glide" id="slider-2">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li onclick="location.href = '/bonus-50'" class="glide__slide" style="background-image: url('/img/misc/slider/d-bonus.png') !important; background-size: cover !important; background-repeat: no-repeat !important; background-position: left center !important;"></li>
                        <li onclick="location.href = '/distributor-benefits'" class="glide__slide" style="background-image: url('/img/misc/slider/benefits.png') !important; background-size: cover !important; background-repeat: no-repeat !important; background-position: left center !important;"></li>
                        <li onclick="location.href = '/invite'" class="glide__slide" style="background-image: url('/img/misc/slider/invite-sm.png') !important; background-size: cover !important; background-repeat: no-repeat !important; background-position: left center !important;"></li>
                    </ul>
                </div>
                <div class="glide__bullets" data-glide-el="controls[nav]">
                    <button class="glide__bullet" data-glide-dir="=0"></button>
                    <button class="glide__bullet" data-glide-dir="=1"></button>
                    <button class="glide__bullet" data-glide-dir="=2"></button>
                </div>
            </div>
        </div>
        <!--
        <div class="mobile-slider-2">
            <div class="mobile-row">
                <div class="block b-1" @click="$router.push('/vip')"></div>
                <div class="block b-2" @click="$router.push('/bonus-50')"></div>
            </div>
            <div class="block b-3" @click="$router.push('/invite')"></div>
        </div>
        -->
        <div class="index-slider-gap" v-if="!isGuest"></div>
        <div class="info" v-if="isGuest" @click="openAuthModal('auth')">
            <div class="left">
                <div class="block b-1"></div>
                <div class="info-row">
                    <div class="block b-2"></div>
                    <div class="block b-3"></div>
                </div>
            </div>
            <div class="middle">
                <div class="block b-4"></div>
            </div>
            <div class="right">
                <div class="info-row">
                    <div class="block b-5"></div>
                    <div class="block b-6"></div>
                </div>
                <div class="block b-7"></div>
            </div>
        </div>
        <img src="/img/misc/footer/payment-index.png" class="index-payment" v-if="isGuest" alt @click="openAuthModal('auth')">
        <div class="search">
            {{ $t('general.enjoy') }}

            <div class="search-input" @click="openSearch">
                <icon icon="fal fa-search"></icon>
                <input :placeholder="$t('general.search')">
            </div>
        </div>
        <game-list :isIndex="true"></game-list>
    </div>
</template>

<script>
    import Glide from '@glidejs/glide';
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import PasswordResetModal from "../modals/PasswordResetModal";
    import Bus from "../../bus";
    import FaucetModal from "../modals/FaucetModal";

    export default {
        computed: {
            ...mapGetters(['games', 'isGuest'])
        },
        watch: {
            isGuest() {
                if(!this.isGuest) this.initSlider2();
            }
        },
        methods: {
            openSearch() {
                Bus.$emit('search:toggle');
            },
            openFaucetModal() {
                FaucetModal.methods.open();
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            initSlider2() {
                if(this.isGuest) return;

                setTimeout(() => {
                    new Glide('#slider-2', {
                        type: 'carousel',
                        perView: 3,
                        peek: 0,
                        focusAt: 'center',
                        gap: 15,
                        autoplay: 10000,
                        keyboard: false,

                        breakpoints: {
                            991: {
                                peek: -150
                            }
                        }
                    }).mount();
                });
            }
        },
        created() {
            if(this.$route.params.user && this.$route.params.token)
                PasswordResetModal.methods.open(this.$route.params.user, this.$route.params.token);
        },
        mounted() {
            this.initSlider2();
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .mobile-info {
        margin-top: 15px;

        @media(min-width: 991px) {
            display: none;
        }

        .block {
            width: 100%;
            min-height: 220px;
            max-height: 500px;
            height: 30vw;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 10px;
            cursor: pointer;

            &.b-1 {
                background-image: url('/img/misc/slider/info/4.png');
                background-position: top center;
            }
        }
    }

    .mobile-slider-2 {
        margin-top: 15px;

        @media(min-width: 991px) {
            display: none;
        }

        .mobile-row {
            display: flex;
        }

        .block {
            width: 100%;
            height: 110px;
            margin: 5px;
            background-size: cover;
            background-position: left center;
            background-repeat: no-repeat;
            border-radius: 10px;
            cursor: pointer;

            &.b-1 {
                background-image: url('/img/misc/slider/cashback.png');
                background-position: top left;
            }

            &.b-2 {
                background-image: url('/img/misc/slider/bonus.png');
            }

            &.b-3 {
                background-image: url('/img/misc/slider/invite.png');
                width: calc(100% - 10px);
            }
        }
    }

    .info {
        background: #222A38;
        border-radius: 15px;
        display: flex;
        padding: 15px;
        margin-top: 15px;

        @media(min-width: 1500px) {
            margin-top: 55px;
        }

        @media(max-width: 991px) {
            display: none;
        }

        .info-row {
            display: flex;
        }

        .middle {
            flex: 1;
            margin-left: 10px;
            margin-right: 20px;
        }

        .middle .block {
            width: 100% !important;
            height: calc(100% - 10px) !important;
            //background-size: contain;
            background-position: left top;
        }

        .block {
            display: flex;
            width: 150px;
            height: 140px;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            margin: 5px;
            border-radius: 10px;
            transition: all .3s ease;
            cursor: pointer;

            @media(max-width: 1400px) {
                width: 90px !important;
                height: 85px !important;
            }

            &:hover {
                transform: scale(1.05);
            }

            &.b-1 {
                background-image: url('/img/misc/slider/info/1.png');
                width: 310px;
                height: 110px;
                background-position: top left;

                @media(max-width: 1400px) {
                    width: 190px !important;
                    height: 79px !important;
                }
            }

            &.b-2 {
                background-image: url('/img/misc/slider/info/2.png');
            }

            &.b-3 {
                background-image: url('/img/misc/slider/info/3.png');
            }

            &.b-4 {
                background-image: url('/img/misc/slider/info/4.png');
            }

            &.b-5 {
                background-image: url('/img/misc/slider/info/5.png');
            }

            &.b-6 {
                background-image: url('/img/misc/slider/info/6.png');
            }

            &.b-7 {
                background-image: url('/img/misc/slider/info/7.png');
                width: 310px;
                height: 110px;
                background-position: top left;

                @media(max-width: 1400px) {
                    width: 190px !important;
                    height: 79px !important;
                }
            }
        }

        @media(max-width: 1580px) {
            .block {
                width: 110px;
                height: 100px;
            }

            .block.b-1, .block.b-7 {
                width: 230px;
                height: 79px;
            }
        }
    }

    .index-slider-gap {
        margin-top: 60px;

        @media(max-width: 991px) {
            display: none;
        }

        @media(max-width: 1500px) {
            margin-top: -30px;
        }
    }

    .search {
        @include themed() {
            display: flex;
            background: t('accentBackground');
            padding: 15px;
            border-radius: 10px;
            color: t('accentText');
            margin-top: 20px;
            align-items: center;
            padding-left: 25px;

            @media (max-width: 991px) {
                background: transparent;
                flex-direction: column;
                align-items: unset;
                padding: unset;

                .search-input {
                    margin-left: unset;
                    margin-top: 10px;

                    input {
                        background: transparent;
                        border: 1px solid #3A455A;
                    }
                }
            }
        }

        .search-input {
            margin-left: auto;
            position: relative;
            cursor: pointer;

            input {
                border-radius: 10px;
                background: #3A455A;
                padding-left: 40px;
                pointer-events: none;

                &::placeholder {
                    color: #90A3C7;
                }
            }

            svg, i {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                left: 15px;
                margin-top: -1px;
            }
        }
    }

    .index-payment {
        width: 870px;
        margin-top: 25px;
        margin-bottom: 5px;
        cursor: pointer;

        @media(max-width: 1436px) {
            display: none;
        }
    }
</style>
