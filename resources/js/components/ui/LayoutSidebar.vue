<template>
    <div :class="'sidebar ' + (sidebar ? 'visible' : 'hidden') + ' ' + (mobileToggle ? 'mobileToggle' : '')" @click="mobileToggle = false">
        <div class="fixed">
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" :class="`games ${!isCasino() ? 'sportSidebar' : ''}`">
                <div class="attention">
                    <div class="attentionRow">
                        <div class="entry money" @click="openFaucetModal"></div>
                        <div class="entry promo" @click="isGuest ? openAuthModal('auth') : openWalletModal('promocode')"></div>
                    </div>
                    <div class="attentionRow">
                        <div class="entry vip" @click="$router.push('/vip')"></div>
                        <div class="entry deposit" v-if="!isGuest" @click="openWalletModal()"></div>
                        <div class="entry deposit" v-else @click="openAuthModal('auth')"></div>
                    </div>
                </div>

                <div class="promotions">
                    <div class="promo-header" :class="promo ? 'expand' : ''" @click="promo = !promo">
                        <icon icon="fas fa-bullhorn"></icon>
                        <div>{{ $t('general.sidebar.promotions') }}</div>
                        <i :class="!promo ? 'fal fa-chevron-down' : 'fal fa-chevron-up'"></i>
                    </div>
                    <div class="promo-content" v-if="promo">
                        <router-link to="/bonus-50">{{ $t('general.sidebar.bonus50') }}</router-link>
                        <router-link to="/vip-bonus">{{ $t('general.sidebar.vipBonus') }}</router-link>
                        <router-link to="/distributor-benefits">{{ $t('general.sidebar.benefits') }}</router-link>
                        <router-link to="/invite">{{ $t('general.sidebar.invite') }}</router-link>
                        <router-link to="/promotions">{{ $t('general.sidebar.allPromotions') }}</router-link>
                    </div>
                </div>

                <div class="divider"></div>

                <template v-if="$checkPermission('dashboard')">
                    <router-link tag="div" to="/admin" class="game">
                        <i class="fas fa-cog"></i>
                        <div>{{ $t('general.sidebar.dashboard') }}</div>
                    </router-link>

                    <div class="divider"></div>
                </template>

                <template v-if="isCasino()">
                    <router-link tag="div" to="/" class="game" :class="$route.path === '/' || $route.path === '/casino' || $route.path.startsWith('/casino/game') ? 'active' : ''">
                        <img src="/img/sidebar/all_games.png" alt>
                        <div>{{ $t('general.sidebar.allGames') }}</div>
                    </router-link>

                    <div class="game" @click="$router.push('/vip')" :class="$route.path === '/vip' ? 'active' : ''">
                        <img src="/img/sidebar/vip.png" alt>
                        <div>{{ $t('general.sidebar.vip') }}</div>
                    </div>

                    <router-link tag="div" to="/promotions" class="game" :class="$route.path === '/promotions' ? 'active' : ''">
                        <img src="/img/sidebar/all_bonuses.png" alt>
                        <div>{{ $t('general.sidebar.allBonuses') }}</div>
                    </router-link>

                    <div class="divider"></div>

                    <router-link tag="div" to="/referral" class="game" :class="$route.path === '/referral' ? 'active' : ''">
                        <icon icon="partner"></icon>
                        <div>{{ $t('general.sidebar.referral') }}</div>
                    </router-link>

                    <router-link tag="div" to="/affiliates" :class="$route.path === '/affiliates' ? 'active' : ''" class="game">
                        <icon icon="affiliates"></icon>
                        <div>{{ $t('general.sidebar.affiliates') }}</div>
                    </router-link>

                    <router-link tag="div" to="/fairness" class="game" :class="$route.path === '/fairness' ? 'active' : ''">
                        <icon icon="fairness"></icon>
                        <div>{{ $t('general.sidebar.fairness') }}</div>
                    </router-link>

                    <div class="divider"></div>

                    <div class="sidebar-button">
                        <icon icon="fal fa-download"></icon>
                        <div>{{ $t('general.sidebar.app') }}</div>
                    </div>

                    <div class="sidebar-button">
                        <icon icon="chat"></icon>
                        <div>{{ $t('general.sidebar.support') }}</div>
                    </div>

                    <select class="languageSelector" @change="setLanguage(language)" v-model="language">
                        <option :selected="locale === 'en'" value="en">🇺🇸&emsp;English</option>
                        <option :selected="locale === 'pt-br'" value="pt-br">🇧🇷&emsp;Brasileiro/Português</option>
                        <!--
                        <option :selected="locale === 'ru'" value="ru">🇷🇺&emsp;Русский</option>
                        <option :selected="locale === 'es'" value="es">🇪🇸&emsp;Spanish</option>
                        <option :selected="locale === 'fa'" value="fa">🇮🇷&emsp;Persian</option>
                        <option :selected="locale === 'de'" value="de">🇩🇪&emsp;Deutsch</option>
                        <option :selected="locale === 'id'" value="id">🇮🇩&emsp;Indonesia</option>
                        <option :selected="locale === 'ko'" value="ko">🇰🇷&emsp;한국어</option>
                        <option :selected="locale === 'zh'" value="zh">🇨🇳&emsp;汉语</option>
                        -->
                    </select>

                    <div class="social-links">
                        <i class="fab fa-facebook" onclick="window.open('https://example.com', '_blank')"></i>
                        <i class="fab fa-instagram" onclick="window.open('https://example.com', '_blank')"></i>
                        <i class="fab fa-telegram" onclick="window.open('https://example.com', '_blank')"></i>
                        <i class="fab fa-discord" onclick="window.open('https://example.com', '_blank')"></i>
                        <i class="fab fa-twitter" onclick="window.open('https://example.com', '_blank')"></i>
                    </div>
                </template>
                <template v-else>
                    <content-placeholders class="game" v-for="n in 17" :key="n" v-if="sportGames.length === 0">
                        <content-placeholders-img/>
                    </content-placeholders>
                    <template v-if="sportGames && sportGames.length > 0">
                        <router-link tag="div" v-for="game in sportGames" :key="game.id" :to="'/sport/category/' + game.id" class="game"
                                v-if="game.games.length > 0">
                            <icon :icon="game.icon"></icon>
                            <div>
                                {{ game.id }}
                            </div>
                        </router-link>
                    </template>
                </template>
            </overlay-scrollbars>
            <div :class="`game bet_slip ${betSlip ? 'active' : ''}`" v-if="!isCasino()" @click="$store.dispatch('toggleBetSlip')">
                <icon icon="fas fa-ticket-alt"></icon>
                <div>{{ $t('sport.bet_slip') }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import FaucetModal from "../modals/FaucetModal";
    import Bus from "../../bus";
    import WalletModal from "../modals/WalletModal";

    export default {
        computed: {
            ...mapGetters(['isGuest', 'user', 'theme', 'games', 'currencies', 'sidebar', 'sportGames', 'betSlip', 'locale'])
        },
        data() {
            return {
                promo: false,
                language: null,
                mobileToggle: false
            }
        },
        created() {
            this.language = this.locale;

            Bus.$on('sidebar:toggleMobile', () => this.mobileToggle = !this.mobileToggle);
        },
        methods: {
            openFaucetModal() {
                FaucetModal.methods.open();
            },
            openWalletModal(type) {
                WalletModal.methods.open(type);
            },
            setLanguage(language) {
                this.$store.dispatch('changeLocale', language);
                this.$store.dispatch('setChatChannel', `${this.isCasino() ? 'casino' : 'sport'}_${language}`);
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sidebar.mobileToggle {
        display: block !important;
        width: 232px;
        opacity: 1;

        .fixed {
            padding: 18px 0;
            padding-bottom: 120px;
        }
    }

    .sidebar.visible {
        width: 232px;
        opacity: 1;

        .recentTpGames {
            display: flex !important;
        }

        .fixed {
            width: 232px;

            @include themed() {
                background: t('sidebar');
            }

            .attention {
                margin-left: 20px;
                width: calc(100% - 40px);

                .attentionRow {
                    display: flex;
                    margin-bottom: 10px;

                    .entry {
                        display: flex;
                        flex-direction: column;
                        flex: 1;
                        height: 50px;
                        margin-right: 8px;
                        background-size: cover;
                        background-position: center right;
                        background-repeat: no-repeat;
                        position: relative;
                        border-radius: 6px;
                        justify-content: center;
                        padding-left: 5px;
                        transition: all .3s ease;
                        cursor: pointer;
                        font-size: 0.85em;

                        &:hover {
                            transform: scale(1.1);
                        }

                        &:before {
                            content: '';
                            position: absolute;
                            opacity: .4;
                            height: 100%;
                            z-index: -1;
                            width: calc(100% + -4px);
                            border-radius: 16px;
                        }

                        &.promo {
                            background-image: url('/img/sidebar/enter-promo.png');
                        }

                        &.money {
                            background-image: url('/img/sidebar/free-bonus.png');
                        }

                        &.deposit {
                            background-image: url('/img/sidebar/make-deposit.png');
                        }

                        &.vip {
                            background-image: url('/img/sidebar/vip-bonus.png');
                        }

                        &:last-child {
                            margin-right: 0;
                        }
                    }
                }
            }

            .social-links {
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 10px 20px 15px;
                padding-bottom: 25px;

                i {
                    margin-top: 15px;
                    margin-right: 20px;
                    font-size: 18px;
                    color: #93acd3;
                    cursor: pointer;
                    transition: color .3s ease;

                    &:last-child {
                        margin-right: 0;
                    }

                    &:hover {
                        color: white;
                    }
                }
            }

            select {
                margin-left: 20px;
                width: calc(100% - 40px);
                background: #2A3546;
                border-radius: 10px;
                padding-left: 20px;
                font-weight: 600;
            }

            .game {
                justify-content: unset;
                padding-left: 17px;
                padding-right: 17px;
                position: relative;

                i {
                    width: 25px;
                }

                svg {
                    margin-right: 11px;
                }

                div {
                    display: block;
                    opacity: 1;
                }
            }
        }
    }

    .sidebar.visible ~.pageWrapper {
        padding-left: 232px;
    }

    .sidebar {
        width: 0;
        flex-shrink: 0;
        z-index: 10000;
        transition: width 0.3s ease, opacity .25s ease;
        opacity: 0;

        .fixed {
            position: fixed;
            top: $header-height;
            width: 0;
            background: transparent;
            height: calc(100% - 30px);
            padding: 35px 0;
            font-weight: 600;

            .sidebar-button {
                margin-left: 20px;
                width: calc(100% - 40px);
                background: #2A3546;
                border-radius: 10px;
                padding-left: 20px;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                padding-top: 10px;
                padding-bottom: 10px;
                cursor: pointer;
                transition: all .3s ease;

                &:hover {
                    transform: scale(1.03);
                }

                i, svg {
                    margin-right: 10px;
                }
            }

            .promotions {
                margin-top: 20px;

                .promo-header {
                    display: flex;
                    margin-left: 20px;
                    margin-right: 20px;
                    align-items: center;
                    border-radius: 10px;
                    background: transparent;
                    padding: 10px 20px;
                    cursor: pointer;
                    transition: background .3s ease, color .3s ease;
                    color: #93acd3;

                    &.expand {
                        border-bottom-left-radius: 0;
                        border-bottom-right-radius: 0;
                        background: #2A3546;
                        color: white;
                    }

                    i:first-child {
                        margin-right: 11px;
                        margin-top: 1px;
                    }

                    i:last-child {
                        margin-left: auto;
                    }
                }

                .promo-content {
                    border-bottom-left-radius: 10px;
                    border-bottom-right-radius: 10px;
                    margin-left: 20px;
                    margin-right: 20px;
                    padding: 10px 20px;
                    background: #2A3546;
                    display: flex;
                    flex-direction: column;

                    a {
                        margin-bottom: 10px;

                        &:last-child {
                            margin-bottom: 0;
                        }
                    }
                }
            }

            @include themed() {
                transition: background 0.05s ease, width .3s ease;

                .games {
                    height: 100%;

                    &.sportSidebar {
                        height: calc(100% - 35px);
                    }

                    border-radius: 3px;

                    .divider {
                        margin-top: 15px !important;
                        height: 2px;
                        background: #2a3546;
                        width: calc(100% - 80px);
                        margin-bottom: 10px;
                        margin-left: 40px;
                        margin-right: 40px;
                    }

                    .recentTpGames {
                        display: none;
                        width: 100%;
                        flex-direction: column;
                        margin-top: 10px;
                        border-top: 2px solid t('border');
                        padding-top: 15px;

                        .loaderContainer {
                            width: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transform: scale(0.6);
                        }

                        .btn {
                            text-transform: uppercase;
                            margin-top: 10px;
                        }

                        .recentTpGame {
                            display: flex;
                            flex-direction: column;

                            .info {
                                display: flex;
                                padding-left: 15px;
                                padding-right: 15px;

                                .image {
                                    width: 50px;
                                    height: 50px;
                                    background-position: center;
                                    background-size: cover;
                                    margin-right: 10px;
                                    border-radius: 3px;
                                    cursor: pointer;
                                    display: flex;

                                    svg, i {
                                        margin: auto;
                                        font-size: 1.8em;
                                        color: t('secondary');
                                        opacity: .8;
                                    }
                                }

                                .meta {
                                    width: calc(100% - 50px);
                                    font-size: 0.8em;
                                    font-weight: 600;

                                    .gameName {
                                        text-transform: uppercase;
                                    }
                                }
                            }
                        }
                    }

                    .btn {
                        width: calc(100% - 30px);
                        margin-left: 15px;
                        margin-right: 15px;
                        margin-bottom: 15px;
                        border-radius: 20px;

                        &.btn-primary {
                            border-bottom: 3px solid darken(t('secondary'), 5%);
                        }

                        &.btn-secondary {
                            border-bottom: 3px solid darken($gray-600, 5%);
                        }
                    }
                }
            }

            .game {
                justify-content: unset;
                position: relative;
                font-size: 15px;
                height: 46px;
                margin-left: 20px;
                margin-right: 20px;
                width: calc(100% - 40px);
                padding: 0 20px;
                border-radius: 30px;
                display: flex;
                align-items: center;
                background: transparent;
                cursor: pointer;
                color: #90A3C7;
                transition: color .3s ease;

                &.active {
                    color: #43BB41;
                }

                &.bet_slip {
                    position: relative;
                    top: -20px;
                }

                @include themed() {
                    &:hover {
                        background: lighten(t('sidebar'), 3.5%);
                    }
                }

                div {
                    display: none;
                    opacity: 0;
                    transition: opacity 1s ease;
                    white-space: nowrap;
                }

                .vue-content-placeholders-img {
                    display: block !important;
                    opacity: 1 !important;
                }

                .vue-content-placeholders-img {
                    height: 15px;
                    width: 15px;
                    border-radius: 3px;
                }

                img {
                    width: 18px;
                    height: 18px;
                    margin-right: 11px;
                }

                i {
                    cursor: pointer;
                }

                &:hover {
                    opacity: 1;
                }

                .online {
                    position: absolute !important;
                    top: 4px !important;
                    left: 17px !important;
                    border-radius: 50%;
                    width: 15px;
                    @include themed() {
                        background: t('secondary');
                    }
                    color: white;
                    height: 15px;
                    font-size: 0.5em;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
            }

            .game.router-link-exact-active {
                opacity: 1;
            }
        }
    }

    @include media-breakpoint-down(md) {
        .sidebar {
            display: none;
        }
    }
</style>
