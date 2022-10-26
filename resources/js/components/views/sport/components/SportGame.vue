<template>
    <div class="sportGame">
        <router-link tag="div" :to="'/sport/game/' + category.id + '/' + game.id" class="info">
            <div class="header">
                <div v-if="game.live" class="liveIcon"></div>
                <icon :icon="category.icon"></icon>
                {{ game.name }}
            </div>
            <div class="league" v-if="game.league">{{ game.league.name }}</div>
            <div class="competitors">
                <div class="competitor">{{ game.competitors[0].name }}</div>
                <div class="competitor">{{ game.competitors[1].name }}</div>
            </div>
        </router-link>
        <div class="markets" v-if="game.markets[0] && game.markets[0].runners[0]">
            <div class="title">
                {{ $t(game.markets[0].runners[0].translation.market.key, game.markets[0].runners[0].translation.market.data) }}
            </div>
            <div class="runners">
                <bet-slip-button :category="category.id" :market="game.markets[0]" :runner="runner" :game="game"
                                 :key="runner.name"
                                 :class="'market ' + (game.markets[0].open && game.open && runner.open ? '' : 'disabled')"
                                 v-for="runner in game.markets[0].runners">
                    <div class="runner">
                        <div class="name">
                            {{
                                runner.open && game.open && game.markets[0].open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--'
                            }}
                        </div>
                        <div class="price">{{ runner.open && game.open && game.markets[0].open ? runner.price : '--' }}</div>
                    </div>
                </bet-slip-button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['game', 'category']
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sportGame {
        @include themed() {
            background: t('body');
            display: flex;
            padding: 20px 30px;
            margin-left: -15px;
            width: calc(100% + 30px);
            font-family: 'Open Sans', sans-serif;

            &:nth-child(even) {
                background: t('sidebar');

                .market {
                    background: t('body') !important;

                    &:hover {
                        background: lighten(t('body'), 2.5%) !important;
                    }
                }
            }

            .info {
                width: 50%;
                cursor: pointer;

                .header {
                    font-weight: 600;
                    font-size: 1.15em;
                    display: flex;
                    align-items: center;

                    i, svg {
                        margin-right: 10px;
                    }

                    .liveIcon {
                        position: relative;
                        width: 16px;
                        height: 16px;
                        margin-right: 15px;

                        &:before {
                            content: '';
                            position: relative;
                            display: block;
                            width: 300%;
                            height: 300%;
                            box-sizing: border-box;
                            margin-left: -100%;
                            margin-top: -100%;
                            border-radius: 45px;
                            background-color: rgba(black, .5);
                            animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
                        }

                        &:after {
                            content: '';
                            position: absolute;
                            left: 0;
                            top: 0;
                            display: block;
                            width: 100%;
                            height: 100%;
                            background-color: t('secondary');
                            border-radius: 15px;
                            box-shadow: 0 0 8px rgba(0, 0, 0, .3);
                            animation: pulse-dot 1.25s cubic-bezier(0.455, 0.03, 0.515, 0.955) -.4s infinite;
                        }

                        @keyframes pulse-ring {
                            0% {
                                transform: scale(.33);
                            }
                            80%, 100% {
                                opacity: 0;
                            }
                        }

                        @keyframes pulse-dot {
                            0% {
                                transform: scale(.8);
                            }
                            50% {
                                transform: scale(1);
                            }
                            100% {
                                transform: scale(.8);
                            }
                        }
                    }
                }

                .league {
                    font-size: 0.9em;
                    margin-top: 10px;
                    opacity: .6;
                }

                .competitors {
                    margin-top: 15px;

                    .competitor {
                        font-size: .9em;
                    }

                    .competitor:last-child {
                        margin-top: 10px;
                    }
                }
            }

            .markets {
                width: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;

                .title {
                    font-weight: 600;
                    margin-bottom: 15px;

                    @include media-breakpoint-down('md') {
                        margin-top: 15px;
                    }
                }

                .runners {
                    display: flex;

                    .market {
                        min-width: 100px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        background: t('sidebar');
                        padding: 5px 0;
                        transition: background .3s ease, opacity .3s ease, color .3s ease;
                        user-select: none;
                        opacity: 1;
                        cursor: pointer;


                        &.active {
                            background: t('secondary') !important;
                            color: black;
                        }

                        &.disabled {
                            opacity: 0.5;
                            cursor: default;
                        }

                        &:not(.disabled):hover {
                            background: lighten(t('sidebar'), 2.5%);
                        }

                        &:first-child {
                            border-top-left-radius: 3px;
                            border-bottom-left-radius: 3px;
                        }

                        &:last-child {
                            border-top-right-radius: 3px;
                            border-bottom-right-radius: 3px;
                        }

                        .runner {
                            .name {
                                font-weight: 600;
                            }

                            .price {
                                font-size: .9em;
                                margin-top: 2px;
                            }
                        }
                    }
                }
            }
        }
    }

    @include media-breakpoint-down('md') {
        .sportGame {
            flex-direction: column;

            .info, .markets {
                width: 100% !important;
            }

            .info {
                margin-bottom: 10px;
            }
        }
    }
</style>
