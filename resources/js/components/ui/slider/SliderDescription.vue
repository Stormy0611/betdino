<template>
    <div class="sliderDescription" :class="isGuest ? '' : 'logged'">
        <template v-if="isGuest">
            <div class="title" v-html="$t('sliderDescription.title')"></div>
            <div class="description" v-html="$t('sliderDescription.description')"></div>
            <div class="description" v-html="$t('sliderDescription.description2')"></div>
            <div class="description" v-html="$t('sliderDescription.description3')"></div>

            <div class="action">
                <button class="btn btn-primary" @click="openFaucetModal">{{ $t('sliderDescription.signUp') }}</button>
                <div class="text">
                    <div>{{ $t('sliderDescription.spin') }}</div>
                    <div v-html="$t('sliderDescription.win')"></div>
                </div>
                <div class="icon"></div>
            </div>
        </template>
        <template v-else>
            <div class="title" v-html="$t('sliderDescription.logged.title')"></div>
            <div class="description" v-html="$t('sliderDescription.logged.description')"></div>
            <div class="description" v-html="$t('sliderDescription.logged.description2')"></div>
        </template>
    </div>
</template>

<script>
    import FaucetModal from "../../modals/FaucetModal";
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['isGuest'])
        },
        methods: {
            openFaucetModal() {
                FaucetModal.methods.open();
            }
        }
    }
</script>

<style lang="scss">
    .sliderDescription {
        display: flex;
        flex-direction: column;

        &.logged {
            margin: auto;
            margin-left: unset;
            text-shadow: 0 0 15px rgb(0 0 0 / 50%);
        }

        &.mobile-only {
            &.logged {
                display: none;
            }

            margin-top: 5px;
            font-size: 0.9em;

            @media(min-width: 991px) {
                display: none;
            }
        }

        &.slider-only {
            padding: 30px 45px;
        }

        .icon {
            background: url('/img/misc/slider/btc-icon.png') no-repeat center;
            background-size: cover;
            width: 30px;
            height: 30px;
            filter: drop-shadow(0px 4px 9px rgba(252, 255, 86, 0.26));
            margin: auto;
            margin-right: 25px;
        }

        .action {
            display: flex;
            margin-top: 25px;
            background: rgba(black, .5);
            width: fit-content;
            border-radius: 100px;

            .btn {
                color: white !important;
                box-shadow: 2px 6px 9px 3px rgba(67, 187, 65, 0.25);
                border-radius: 10px;
            }

            .text {
                padding: 10px 15px;
            }
        }

        .title {
            font-weight: 600;
            font-size: 2.2em;
            margin-bottom: 5px;

            span {
                color: #27FFD8;
            }
        }

        .description {
            font-size: 1.05em;
        }
    }
</style>
