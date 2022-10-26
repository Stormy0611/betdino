<template>
    <div class="affiliates container">
        <div class="head">
            <div class="head-title">{{ $t('affiliate.title') }}</div>
            <div class="head-desc">{{ $t('affiliate.title2') }}</div>
            <div class="head-desc" v-html="$t('affiliate.title3')"></div>
            <button class="btn btn-primary" @click="$router.push('/profile/invite')">{{ $t('affiliate.create') }}</button>
        </div>
        <button class="btn btn-primary mobile-btn" @click="$router.push('/profile/invite')">{{ $t('affiliate.create') }}</button>
        <div class="title">
            {{ $t('affiliate.title4') }}
        </div>
        <div class="blocks">
            <div class="block block-1">
                <div class="block-title" v-html="$t('affiliate.1.title')"></div>
                <div class="block-desc">{{ $t('affiliate.1.description') }}</div>
                <div class="block-desc" v-html="$t('affiliate.1.description2')"></div>
            </div>
            <div class="block block-2">
                <div class="block-title" v-html="$t('affiliate.2.title')"></div>
                <div class="block-desc">{{ $t('affiliate.2.description') }}</div>
                <div class="block-desc" v-html="$t('affiliate.2.description2')"></div>
            </div>
        </div>
        <div class="title" v-html="$t('affiliate.sep')"></div>
        <div class="description">
            {{ $t('affiliate.sepDesc') }}
        </div>
        <div class="blocks">
            <div class="block block-3">
                <div class="block-title">{{ $t('affiliate.3.title') }}</div>
                <div class="block-desc" v-html="$t('affiliate.3.description')"></div>
                <div class="block-content">
                    <div class="t">
                        <div class="t-r t-h">
                            <div>{{ $t('affiliate.3.vip') }}</div>
                            <div>{{ $t('affiliate.3.rewards') }}</div>
                        </div>
                        <div class="t-r" v-for="level in vip" v-if="level.inviteBonus">
                            <div>{{ $t('affiliate.3.vipLevel', { level: level.level }) }}</div>
                            <div><span>R$ {{ level.inviteBonus }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-4">
                <div class="block-title">{{ $t('affiliate.4.title') }}</div>
                <div class="block-desc">{{ $t('affiliate.4.description') }}</div>
                <div class="block-content">
                    <div class="block-content-desc">{{ $t('affiliate.4.content') }}</div>
                    <div class="block-content-affiliates-image"></div>
                </div>
            </div>
        </div>
        <div class="desc">
            <div class="img"></div>
            <div class="text" v-html="$t('affiliate.description')"></div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['vip'])
        }
    }
</script>

<style lang="scss">
    .affiliates {
        .mobile-btn {
            border-radius: 50px !important;
            color: white !important;
            padding: 20px 25px !important;
            font-weight: 600;
            display: none;
            width: 100%;
            margin-bottom: 25px;

            @media(max-width: 991px) {
                display: block;
            }
        }

        .blocks {
            display: flex;
            margin-top: 20px;
            margin-bottom: 20px;

            .block {
                background: #2F3E55;
                margin: 10px;
                width: calc(100% - 20px);
                border-radius: 20px;
                padding: 35px;
                color: #90A3C7;
                position: relative;

                .block-content {
                    background: #1B2638;
                    padding: 20px;
                    margin-top: 15px;
                    border-radius: 15px;

                    .t {
                        span {
                            color: #44D242;
                        }

                        .t-h {
                            font-weight: 600;
                            margin-top: 0 !important;
                        }

                        .t-r {
                            display: flex;
                            margin-top: 10px;

                            div:last-child {
                                margin-left: auto;
                            }
                        }
                    }

                    .block-content-affiliates-image {
                        background: url('/img/misc/affiliates.png') no-repeat center;
                        background-size: contain;
                        width: 100%;
                        height: 270px;
                    }

                    .block-content-desc {
                        margin-bottom: 15px;
                    }
                }

                .block-title {
                    color: #44D242;
                    font-size: 1.4em;
                    margin-bottom: 5px;
                    font-weight: 700;
                }

                .block-desc {
                    margin-top: 15px;
                }

                &.block-1, &.block-2 {
                    .block-title {
                        margin-top: 25px;
                    }
                }

                &.block-3, &.block-4 {
                    .block-desc {
                        width: 70%;
                    }

                    span {
                        color: #FFCA43;
                        font-weight: 700;
                    }
                }

                &:after {
                    content: '';
                    background-size: contain;
                    position: absolute;
                    background-position: center;
                    background-repeat: no-repeat;
                    top: 25px;
                    right: 25px;
                    width: 100px;
                    height: 100px;
                }

                &.block-1:after {
                    background-image: url('/img/misc/chips.png');
                }

                &.block-2:after {
                    background-image: url('/img/misc/card-magic.png');
                }

                &.block-3:after {
                    background-image: url('/img/misc/cash.png');
                }

                &.block-4:after {
                    background-image: url('/img/misc/cash-2.png');
                }
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .block {
                    width: 100%;
                    margin: unset;
                    margin-bottom: 15px;

                    &:after {
                        width: 60px;
                        height: 60px;
                    }

                    .block-title {
                        width: 80%;
                        margin-top: 0 !important;
                    }
                }
            }
        }

        .desc {
            display: flex;
            align-items: center;
            background: #2F3E55;
            padding: 20px;
            border-radius: 20px;
            margin-top: 15px;

            .img {
                background: url('/img/misc/dino-cash.png') no-repeat center;
                background-size: contain;
                margin-right: 15px;
                width: 180px;
                height: 100px;
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .img {
                    margin-right: 0;
                    margin-bottom: 10px;
                }
            }

            .text {
                color: #90A3C7;
                font-weight: 600;
            }
        }

        span {
            color: #44D242;
        }

        .title {
            text-transform: uppercase;
            text-align: center;
            font-size: 1.6em;
            font-weight: 600;

            @media(max-width: 991px) {
                font-size: 1.1em;
            }
        }

        .description {
            text-align: center;
            color: #90A3C7;
            margin-top: 15px;
            font-weight: 600;
        }

        .head {
            background: url('/img/misc/slider/betdino-slide-1.png') no-repeat center right;
            background-size: cover;
            width: 100%;
            height: 250px;
            border-radius: 20px;
            position: relative;
            display: flex;
            padding-left: 50px;
            flex-direction: column;
            justify-content: center;
            margin-bottom: 70px;

            span {
                color: #27FFD8;
            }

            .head-title {
                text-transform: uppercase;
                margin-bottom: 15px;
                font-size: 1.8em;
                font-weight: 700;
            }

            .head-desc {
                text-transform: uppercase;
                font-weight: 600;
            }

            .btn {
                border-radius: 50px !important;
                color: white !important;
                padding: 25px 40px !important;
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateY(50%) translateX(-50%);
                font-weight: 600;
                font-size: 1.2em;
            }
        }

        @media(max-width: 991px) {
            .head {
                height: 150px;
                padding-left: 25px;
                margin-bottom: 15px;

                .head-title {
                    font-size: 1.1em;
                }

                .head-desc {
                    font-size: 0.7em;
                }

                .btn {
                    display: none;
                }
            }
        }
    }
</style>
