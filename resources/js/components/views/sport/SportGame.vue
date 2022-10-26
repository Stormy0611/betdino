<template>
    <div class="sportGamePage">
        <template v-if="!doesntExist">
            <loader v-if="!game"></loader>
            <template v-else>
                <div class="sportGameHeader">
                    <i class="fal fa-chevron-left" @click="$router.push('/sport/category/' + $route.params.category)"></i>
                    {{ game.name }}
                </div>

                <div class="sportGameInfo" :style="categoryBackground()">
                    <div class="competitors">
                        <div class="competitor">
                            <div class="name">{{ game.competitors[0].name }}</div>
                            <img v-if="game.competitors[0].logo" :src="game.competitors[0].logo" alt />
                        </div>
                        <div class="competitor">
                            <div class="name">{{ game.competitors[1].name }}</div>
                            <img v-if="game.competitors[1].logo" :src="game.competitors[1].logo" alt />
                        </div>
                    </div>
                    <div class="sportLiveStatus">
                        <div class="createdAt">{{ new Date(game.liveStatus.createdAt).toLocaleString() }}</div>
                        <div class="score">{{ game.liveStatus.score.split(":")[0] }}<div class="divide"></div>{{ game.liveStatus.score.split(":")[1] }}</div>
                        <div class="stage">{{ game.liveStatus.stage }}</div>
                        <div class="setScore">({{ game.liveStatus.setScores }})</div>
                    </div>
                    <div class="live-markets" v-if="game.markets.length > 0">
                        <bet-slip-button :category="$route.params.category" :market="game.markets[0]" :runner="runner" :game="game" :class="'runner ' + (runner.open && game.open && game.markets[0].open ? '' : 'disabled')" v-for="runner in game.markets[0].runners" :key="game.markets[0].name + runner.name">
                            <div>{{ game.open && runner.open && game.markets[0].open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--' }}</div>
                            <div>{{ game.open && runner.open && game.markets[0].open ? runner.price : '--' }}</div>
                        </bet-slip-button>
                    </div>
                </div>
                <div class="sportClosedBetting" v-if="!game.open">
                    <i class="fal fa-pause-circle"></i> {{ $t('sport.game_is_closed') }}
                </div>
                <div class="market-columns">
                    <div class="markets" v-for="n in 2" :key="n">
                        <div class="market" v-for="market in marketsColumn[n - 1]" :key="market.name">
                            <div class="title">{{ !market.categories[0][0].translation ? market.name : $t(market.categories[0][0].translation.market.key, market.categories[0][0].translation.market.data) }}</div>
                            <div class="category" v-for="category in market.categories">
                                <bet-slip-button :category="$route.params.category" :class="'runner ' + (game.open && market.open && runner.open ? '' : 'disabled') + ' ' + (category.length < 3 ? 'runner-2' : 'runner-3')"
                                                 v-for="runner in category" v-if="runner.supported" :game="game" :market="market" :runner="runner" :key="market.name + runner.name">
<!--                                    <div v-if="!runner.supported" style="margin-right: 15px; color: #d23d30">[Unsupported]</div>-->
                                    <div>{{ game.open && runner.open && market.open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--' }}</div>
                                    <div>{{ game.open && runner.open && market.open ? runner.price : '--' }}</div>
                                </bet-slip-button>
                                <div class="runner runner-2 disabled d-none d-md-flex" v-if="category.length === 1">
                                    <div>--</div>
                                    <div>--</div>
                                </div>
                                <div class="runner runner-3 disabled d-none d-md-flex" v-if="category.length === 5">
                                    <div>--</div>
                                    <div>--</div>
                                </div>
                                <div class="runner runner-3 disabled d-none d-md-flex" v-if="category.length === 4" v-for="n in 2" :key="n">
                                    <div>--</div>
                                    <div>--</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </template>
        <div class="error" v-else>
            <icon icon="time"></icon>
            <div class="title">{{ $t('sport.game_not_found') }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                game: null,
                markets: null,
                marketsColumn: null,

                doesntExist: false,

                updateInterval: null
            }
        },
        watch: {
            game() {
                let markets = {};

                this.game.markets.forEach((market) => {
                    if(markets[market.name]) markets[market.name].categories.push(market.runners);
                    else markets[market.name] = {
                        name: market.name,
                        open: market.open,
                        categories: [
                            market.runners
                        ]
                    };
                });

                this.markets = markets;

                let firstColumn = {}, secondColumn = {};
                let columns = [firstColumn, secondColumn];

                let left = true;
                for(let i = 0; i < Object.keys(markets).length; i++) {
                    const market = markets[Object.keys(markets)[i]];

                    let isEmpty = true;

                    market.categories.forEach((e) => {
                        e.forEach((runner) => {
                            if(runner.supported) isEmpty = false;
                        });
                    });

                    if(isEmpty) continue;

                    (left ? firstColumn : secondColumn)[Object.keys(left ? firstColumn : secondColumn).length] = market;
                    left = !left;
                }

                this.marketsColumn = columns;
            }
        },
        beforeDestroy() {
            clearInterval(this.updateInterval);
        },
        methods: {
            categoryBackground() {
                switch (this.$route.params.category) {
                    case 'Soccer': case 'Australian Rules': return { background: '#2d7837' };
                    case 'Tennis': return { background: '#9d5535' };
                    case 'Table Tennis': return { background: '#1b4d78' };
                    case 'Basketball': return { background: '#7e5f3a' };
                    case 'Ice Hockey': return { background: '#38536c' };
                    default: return { background: '#08090a' };
                }
            },
            update() {
                axios.post('/api/sport/game', { id: this.$route.params.id }).then(({ data }) => {
                    this.game = data;
                    this.doesntExist = false;
                }).catch(() => {
                    this.doesntExist = true;
                });
            }
        },
        created() {
            this.update();

            this.updateInterval = setInterval(() => this.update(), 1500);
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sportGamePage {
        margin-top: -15px;

        .error {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 150px;

            svg, i {
                @include themed() {
                    font-size: 7em;
                    margin-bottom: 25px;
                    color: t('secondary');
                }
            }

            .title {
                font-size: 1.5em;
            }
        }

        .loaderContainer {
            margin-top: 55px;
            margin-bottom: 55px;

            .loader {
                margin: auto;
            }
        }

        @include themed() {
            .market-columns {
                display: flex;
            }

            .markets {
                display: flex;
                width: 50%;
                flex-direction: column;
                padding: 15px;
                margin-top: 5px;

                &:first-child {
                    padding-right: 0;
                }

                .market {
                    border-radius: 3px;
                    background: t('sidebar');
                    margin-bottom: 15px;
                    color: t('text');
                    transition: background .3s ease, color .3s ease;

                    &.active {
                        background: t('secondary') !important;
                        color: black;
                    }

                    &:last-child {
                        margin-bottom: 0;
                    }

                    .title {
                        text-align: center;
                        padding: 10px;
                        background: darken(t('sidebar'), 1.5%);
                    }

                    .category {
                        display: flex;
                        flex-wrap: wrap;

                        .runner {
                            width: 100%;
                            display: flex;
                            user-select: none;
                            padding: 10px 15px;
                            background: lighten(t('sidebar'), 3%);
                            cursor: pointer;
                            transition: background .3s ease, color .3s ease;
                            flex-wrap: wrap;
                            color: t('text');

                            &.active {
                                background: t('secondary') !important;
                                color: black;
                            }

                            &.runner-2 {
                                flex-basis: 50%;
                                max-width: 50%;
                            }

                            &.runner-3 {
                                flex-basis: 33.33%;
                                max-width: 33.33%;
                            }

                            &.disabled {
                                cursor: default;
                                background: lighten(t('sidebar'), 2%) !important;
                            }

                            &:hover {
                                background: lighten(t('sidebar'), 4%);
                            }

                            div:last-child {
                                margin-left: auto;
                            }
                        }
                    }
                }
            }

            .sportGameHeader {
                text-transform: uppercase;
                font-weight: 600;
                font-size: 1.1em;
                padding: 25px;
                width: 100%;
                background: t('sidebar');
                text-align: center;
                position: relative;

                i {
                    position: absolute;
                    left: 25px;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;
                    opacity: .8;
                    transition: opacity 3s ease;

                    &:hover {
                        opacity: 1;
                    }
                }
            }

            .sportClosedBetting {
                padding: 15px;
                width: 100%;
                background: t('sidebar');
                text-align: center;

                i {
                    margin-right: 5px;
                }
            }

            .sportGameInfo {
                padding: 30px;
                color: white;
                position: relative;

                .sportLiveStatus {
                    position: relative;
                    left: 50%;
                    transform: translateX(-50%);
                    text-align: center;

                    .score {
                        font-size: 2.5em;
                        margin-top: 5px;
                        margin-bottom: 15px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        justify-content: center;

                        .divide {
                            border-left: 1px solid rgba(white, .5);
                            height: 30px;
                            margin: 0 10px;
                        }
                    }

                    .stage, .setScore {
                        font-size: .9em;
                    }
                }

                .live-markets {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-top: 15px;

                    .runner {
                        margin-right: 10px;
                        cursor: pointer;
                        user-select: none;
                        background: t('body');
                        display: flex;
                        min-width: 120px;
                        padding: 10px 15px;
                        border-radius: 3px;
                        color: t('text');
                        transition: background .3s ease, color .3s ease;

                        &.active {
                            background: t('secondary') !important;
                            color: black;
                        }

                        div:last-child {
                            margin-left: auto;
                        }

                        &:hover {
                            background: t('sidebar');
                        }

                        &.disabled {
                            cursor: default;
                            background: t('body') !important;
                        }

                        &:last-child {
                            margin-right: 0;
                        }
                    }
                }

                .competitors {
                    display: flex;
                    position: absolute;
                    width: calc(100% - 60px);

                    .competitor {
                        .name {
                            font-weight: 600;
                            margin-bottom: 10px;
                            font-size: 1.1em;
                        }

                        img {
                            width: 72px;
                            height: 72px;
                            border: 3px solid white;
                            background: white;
                            border-radius: 50%;
                        }

                        &:last-child {
                            margin-left: auto;
                            text-align: right;
                        }
                    }
                }
            }
        }
    }

    @media(max-width: 1350px) {
        .sportGamePage {
            .market-columns {
                flex-direction: column;

                .markets {
                    width: 100%;
                    padding-right: 15px !important;
                }
            }
        }
    }

    @media(max-width: 991px) {
        .sportGamePage {
            .markets {
                .runner-3 {
                    flex-basis: 50% !important;
                    max-width: 50% !important;
                }

                .runner {
                    font-size: 0.8em;
                }
            }
        }

        .sportGameHeader {
            font-size: 0.8em !important;
            padding: 15px !important;

            i {
                display: none !important;
            }
        }

        .sportGameInfo {
            padding-bottom: 70px !important;

            .live-markets {
                display: none !important;
            }

            .competitors {
                bottom: 10px;

                .competitor {
                    img {
                        display: none !important;
                    }
                }
            }
        }
    }
</style>
