<template>
    <div class="container promotions">
        <div class="head">
            <div>
                <span>BetDino</span> Presents the most generous and various Bonuses
            </div>
            <div>
                Play with pleasure and get additional rewards during the game. Choose the most attractive bonuses or use them all at once.
            </div>
        </div>
        <div class="separator">
            <span>BetDino</span> Promotions
        </div>
        <div class="blocks">
            <div class="block block-1" @click="$router.push('/bonus-50')"></div>
            <div class="block block-2" @click="$router.push('/vip-bonus')"></div>
        </div>
        <div class="separator-2"></div>
        <div class="separator">
            <span>Exclusive</span> bonuses
        </div>
        <div class="blocks">
            <div class="block sm block-3" @click="$router.push('/invite')"></div>
            <div class="block sm block-5" @click="openFaucetModal">
                <span>Get up to R$ 777 Free</span>
            </div>
            <div class="block sm block-4" @click="$router.push('/distributor-benefits')"></div>
        </div>
    </div>
</template>

<script>
    import FaucetModal from "../modals/FaucetModal";

    export default {
        methods: {
            openFaucetModal() {
                FaucetModal.methods.open();
            }
        }
    }
</script>

<style lang="scss">
    .promotions {
        .blocks {
            display: flex;

            @media(max-width: 991px) {
                flex-direction: column;

                .block {
                    background-position-x: 30% !important;
                    height: 150px !important;
                }
            }

            .block {
                width: calc(100% - 14px);
                margin: 7px;
                background-size: cover;
                background-position: left center;
                background-repeat: no-repeat;
                border-radius: 10px;
                cursor: pointer;
                transition: all .3s ease;
                height: 200px;
                margin-bottom: 15px;
                position: relative;

                span {
                    position: absolute;
                    top: 25px;
                    left: 50%;
                    transform: translateX(-50%);
                }

                &.sm {
                    height: 150px;
                }

                &:hover {
                    transform: scale(1.02);
                }

                &.block-1 {
                    background-image: url('/img/misc/bonus/come-and-get-50-bonus.png');
                }

                &.block-2 {
                    background-image: url('/img/misc/bonus/exclusive-vip-bonus.png');
                }

                &.block-3 {
                    background-image: url('/img/misc/bonus/invite-friends.png');
                }

                &.block-4 {
                    background-image: url('/img/misc/bonus/millions-of-distr-benefits.png');
                }

                &.block-5 {
                    background-image: url('/img/misc/promotions/spin.png');
                }
            }
        }

        .separator-2 {
            border: 2px solid #3A455A;
            width: 100%;
            height: 0;
            margin-bottom: 25px;
            margin-top: 25px;
        }

        .separator {
            text-align: center;
            font-size: 2em;
            text-transform: uppercase;
            margin-bottom: 25px;
            font-weight: 700;

            span {
                color: #44D242;
            }
        }

        .head {
            background: url('/img/misc/slider/bg-dino.png') no-repeat center;
            background-size: contain;
            padding: 90px 0;

            div {
                font-size: 1.2em;
                font-weight: 100;
                width: 45%;

                &:first-child {
                    font-weight: 600;
                    font-size: 2em;
                    line-height: 1.2em;
                    margin-bottom: 15px;
                }

                span {
                    color: #27FFD8;
                }
            }

            @media(max-width: 991px) {
                padding: 30px 0;
                background-position: 73%;

                div {
                    font-size: 0.8em;
                    margin-top: 5px;

                    &:first-child {
                        font-size: 1.4em;
                        display: flex;
                        flex-direction: column;
                        line-height: 1.2em;
                    }
                }
            }
        }
    }
</style>
