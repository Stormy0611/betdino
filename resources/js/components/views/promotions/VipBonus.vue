<template>
    <div class="vipBonus container" v-if="vip">
        <div class="head"></div>
        <div class="description">
            <div class="title" v-html="$t('promotions.vip.title')"></div>
            <div class="text" v-html="$t('promotions.vip.description')"></div>
            <div class="image vip"></div>
        </div>
        <div class="description reverse">
            <div class="title" v-html="$t('promotions.vip.activity')"></div>
            <div class="text">{{ $t('promotions.vip.activity2') }}</div>
            <div class="image star"></div>
        </div>
        <div class="table-view">
            <div class="title">{{ $t('promotions.vip.bonus') }}</div>
            <div class="text">{{ $t('promotions.vip.bonus2') }}</div>
            <div class="t">
                <div class="t-h">
                    <div>{{ $t('promotions.vip.vip') }}</div>
                    <div>R$</div>
                </div>
                <div class="t-r" v-for="level in vip" v-if="level.level" :key="level.level">
                    <div>{{ $t('promotions.vip.vip', { level: level.level }) }}</div>
                    <div>R$ {{ level.oneTimeBonus.toFixed(2) }}</div>
                </div>
            </div>
        </div>
        <div class="reminder">
            <div class="title">{{ $t('promotions.vip.reminder.1') }}</div>
            <div class="text">{{ $t('promotions.vip.reminder.2') }}</div>
        </div>
        <div class="description">
            <div class="title" v-html="$t('promotions.vip.reminder2.1')"></div>
            <div class="text">{{ $t('promotions.vip.reminder2.2') }}</div>
        </div>
        <div class="description center">
            <div class="title" v-html="$t('promotions.vip.reminder3.1')"></div>
            <div class="text">{{ $t('promotions.vip.reminder3.2') }}</div>
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
    .vipBonus {
        span {
            color: #43BB41;
        }

        .reminder {
            margin-top: 25px;
            border: 2px solid #3B5884;
            border-radius: 15px;
            text-align: center;
            padding: 30px 15px;

            .title {
                font-size: 1.3em;
                color: #43BB41;
                margin-bottom: 15px;
            }

            .text {
                font-weight: 600;
            }
        }

        .table-view {
            text-align: center;

            .title {
                font-weight: 700;
                font-size: 1.5em;
            }

            .text {
                font-weight: 600;
                font-size: 1.1em;
                color: #90A3C7;
            }

            .t {
                margin-top: 15px;
                font-weight: 600;

                .t-h, .t-r {
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    div {
                        width: 50%;
                        padding: 15px;
                    }
                }

                .t-h {
                    background: #3B5884;
                    border-top-left-radius: 10px;
                    border-top-right-radius: 10px;
                }

                .t-r {
                    background: #1B2431;
                    margin-bottom: 2px;

                    div:last-child {
                        color: #43BB41;
                    }
                }
            }
        }

        .description {
            position: relative;
            margin-top: 50px;
            margin-bottom: 50px;

            &.center {
                text-align: center;

                .text {
                    width: 100%;
                }
            }

            @media(min-width: 991px) {
                &.reverse {
                    padding-left: 35%;

                    .image {
                        right: unset;
                        left: 10%;
                    }
                }
            }

            .title {
                font-weight: 600;
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            .text {
                color: #90A3C7;
                font-weight: 600;
                width: 50%;
            }

            .image {
                position: absolute;
                background-position: center;
                background-size: contain;
                right: 0;
                top: 50%;
                width: 150px;
                height: 150px;
                transform: translateY(-50%);

                &.star {
                    background-image: url('/img/misc/star.png');
                }

                &.vip {
                    background-image: url('/img/misc/vip.png');
                }
            }

            @media(max-width: 991px) {
                margin-top: 25px;
                margin-bottom: 25px;

                .text {
                    width: 100%;
                }

                .image {
                    position: unset;
                    transform: unset;
                    margin: auto;
                }
            }
        }

        .head {
            height: 200px;
            width: 100%;
            background: url('/img/misc/promotions/headers/vip-bonus.png') no-repeat 10% center;
            background-size: cover;
            margin-top: 15px;
            margin-bottom: 15px;
            border-radius: 10px;

            @media(max-width: 991px) {
                height: 100px;
            }
        }
    }
</style>
