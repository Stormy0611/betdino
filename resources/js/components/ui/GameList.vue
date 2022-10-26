<template>
    <div class="game-list" v-if="games">
        <template v-for="provider in sort && sort.type === 'provider' ? providers.filter(e => e === sort.by) : providers"
                  v-if="findPage(provider) && sortGames(provider).length > 0">
            <div class="category">
                <div class="icon">
                    <icon :icon="icons[provider] ? icons[provider] : icons['default']"></icon>
                </div>
                <div class="name">
                    {{ provider }}
                </div>
                <div class="viewAll" @click="$router.push('/casino/game/provider/' + provider)" v-if="!sort">
                    {{ $t('general.viewAll') }}
                </div>
                <div class="arrows" v-if="!sort">
                    <div class="arrow" @click="findPage(provider).current > 0 ? updatePage(Object.assign(findPage(provider), { current: findPage(provider).current - 1 })) : null"
                         :class="findPage(provider).current <= 0 ? 'disabled' : ''"><icon icon="fal fa-chevron-left"></icon></div>
                    <div class="arrow" @click="findPage(provider).current < findPage(provider).max ? updatePage(Object.assign(findPage(provider), { current: findPage(provider).current + 1 })) : null"
                         :class="findPage(provider).current >= findPage(provider).max ? 'disabled' : ''"><icon icon="fal fa-chevron-right"></icon></div>
                </div>
            </div>
            <div class="category-games" :class="(provider.replaceAll(' ', '_').replaceAll('(', '').replaceAll(')', '') + ' ') + (!sort ? '' : 'sorted')">
                <div class="category-game" :class="provider === 'Originals' && i === 0 && findPage(provider).current === 0 && isIndex ? 'extend' : ''"
                     v-for="(game, i) in isIndex ? sortGames(provider).slice(0, gamesPerView - (width <= 991 || width >= 1500 && findPage(provider).current === 0 && provider === 'Originals' && isIndex ? 1 : 0)) : sortGames(provider)" :key="game.id"
                     :style="{ backgroundImage: `url(${game.image})` }" @click="game.isDisabled ? false : $router.push('/casino/game/' + game.id)">
                    <div class="hover">
                        <div class="playButton"></div>

                        <div class="bottomInfo">
                            <div class="gameName">{{ game.name }}</div>
                            <div class="hover-category">{{ game.type }}</div>
                        </div>
                    </div>
                    <div class="unavailable" v-if="game.isDisabled">
                        <div class="slanting">
                            <div class="content">
                                {{ $t(game.isPlaceholder ? 'general.coming_soon' : 'general.not_available   ') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                icons: {
                    default: 'originals',
                    'Slots (Originals)': 'slots'
                },
                gamesPerView: 0,
                page: [],
                width: 0
            }
        },
        props: {
            sort: {
                default: null,
                type: Object
            },
            isIndex: {
                type: Boolean,
                default: false
            }
        },
        watch: {
            games() {
                this.updateGames();
            }
        },
        computed: {
            ...mapGetters(['games']),
            providers() {
                return [ ...new Set(this.games.map(e => e.type)) ].sort(e => e !== 'Originals' ? 0 : -1)
            }
        },
        mounted() {
            this.updateGamesPerView();
            window.addEventListener('resize', this.updateGamesPerView);
        },
        methods: {
            sortGames(provider) {
                let games = null;

                if(!this.sort) games = this.games.filter(e => e.type === provider).slice(this.findPage(provider).current * this.gamesPerView, (this.findPage(provider).current + 1) * this.gamesPerView);
                else if(this.sort.type === 'tag') games = this.games.filter(e => e.type === provider && e.category.includes(this.sort.by));
                else games = this.games.filter(e => e.type === provider);

                return games;
            },
            updateGamesPerView() {
                this.width = window.innerWidth;

                let prev = this.gamesPerView;
                if(window.innerWidth <= 991) this.gamesPerView = 6;
                else if(window.innerWidth < 1500) this.gamesPerView = 7;
                else this.gamesPerView = 10;

                if(prev !== this.gamesPerView) this.updateGames();
            },
            findPage(type) {
                return this.page.filter((e) => e.id === type)[0];
            },
            updatePage(object) {
                this.page = this.page.filter((e) => e.id !== object.id);
                this.page.push(object);
            },
            updateGames() {
                this.providers.forEach(providerName => {
                    this.updatePage({
                        id: providerName,
                        current: 0,
                        max: Math.floor((this.games.filter(e => e.type === providerName).length - 1) / this.gamesPerView)
                    });
                });
            }
        }
    }
</script>

<style lang="scss">
    .game-list {
        .category-games {
            display: flex;
            flex-wrap: wrap;

            &.Originals {
                .category-game {
                    min-height: 220px;
                    max-width: 20%;

                    @media(max-width: 1500px) {
                        min-height: 240px;
                    }

                    @media(max-width: 1355px) {
                        min-height: 200px;
                    }

                    @media(max-width: 1120px) {
                        min-height: 230px;
                        min-width: 140px;
                        max-width: unset;
                    }

                    @media(max-width: 991px) {
                        height: 32vw !important;
                        min-height: 120px !important;
                        max-height: 240px;
                    }
                }
            }

            &.sorted {
                flex-wrap: wrap;
                justify-content: center;

                .category-game {
                    width: 180px;
                }
            }

            .category-game {
                background-color: #192230;
                height: 160px;
                margin: 5px;
                position: relative;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                max-width: 350px;
                border-radius: 8px;
                transition: all .3s ease;
                cursor: pointer;
                flex: 160px 1;

                @media(max-width: 991px) {
                    min-width: unset !important;
                    flex: 1 1 calc(33.3% - 10px);
                }

                &.extend {
                    max-width: unset;
                    flex: 366px 1;

                    @media(max-width: 991px) {
                        flex: 1 1 calc(100vw - 34px - 35vw);
                        max-width: unset;
                    }

                    @media(min-width: 768px) and (max-width: 991px) {
                        flex: 1 1 calc(100vw - 42px - 50vw);
                    }
                }

                @media(max-width: 991px) {
                    height: 120px !important;
                    min-height: 120px !important;
                }

                &:hover {
                    transform: scale(1.05);
                }

                .unavailable {
                    z-index: 4;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(black, 0.4);
                    color: white;
                    border-radius: 8px;

                    .slanting {
                        transform: skewY(-5deg) translateY(-50%);
                        padding: 25px;
                        position: absolute;
                        top: 50%;
                        background: rgba(black, 0.25);
                        backdrop-filter: blur(25px);
                        text-transform: uppercase;
                        font-weight: 600;
                        width: 100%;

                        .content {
                            font-size: 15px;
                            transform: skewY(5deg);
                            text-align: center;
                        }
                    }
                }

                &:hover {
                    .hover {
                        opacity: 1;
                    }
                }

                .hover {
                    position: absolute;
                    z-index: 10;
                    background: rgba(black, .8);
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                    height: 100%;
                    pointer-events: none;
                    opacity: 0;
                    transition: opacity .3s ease;
                    border-radius: 8px;

                    .playButton {
                        background: url('/img/misc/play.png') no-repeat center;
                        width: 65px;
                        height: 65px;
                        background-size: cover;
                        position: absolute;
                        top: calc(50% - 15px);
                        left: 50%;
                        transform: translate(-50%, -50%);
                    }

                    .bottomInfo {
                        margin-top: auto;
                        text-align: center;
                        text-transform: uppercase;
                        margin-bottom: 10px;

                        .gameName {
                            font-weight: 600;
                        }

                        .hover-category {
                            opacity: .7;
                            font-size: .95em;
                        }
                    }
                }
            }
        }

        .category {
            color: #43BB41;
            margin-top: 25px;
            display: flex;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;

            @media(max-width: 991px) {
                font-size: 12px;
            }

            .icon {
                margin-right: 15px;
                display: flex;
                align-items: center;
            }

            .name {
                display: flex;
                align-items: center;
            }

            .viewAll {
                margin-left: auto;
                margin-right: 15px;
                background: #222A38;
                padding: 10px 15px;
                cursor: pointer;
                color: #90A3C7;
                border-radius: 6px;
            }

            .arrows {
                display: flex;

                .arrow {
                    padding: 15px 20px;
                    background: #222A38;
                    color: #90A3C7;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;

                    @media(max-width: 991px) {
                        padding: 5px 10px;
                    }

                    &.disabled {
                        cursor: default;

                        i, svg {
                            opacity: .5;
                            pointer-events: none;
                        }
                    }

                    &:first-child {
                        border-top-left-radius: 10px;
                        border-bottom-left-radius: 10px;
                    }

                    &:last-child {
                        border-top-right-radius: 10px;
                        border-bottom-right-radius: 10px;
                        margin-left: -1px;
                    }

                    i, svg {
                        font-size: .9em;
                        transition: all .3s ease;
                    }

                    &:not(.disabled):hover {
                        i, svg {
                            transform: scale(1.15);
                        }
                    }
                }
            }
        }
    }
</style>
