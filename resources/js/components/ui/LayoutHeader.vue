<template>
    <header>
        <div class="fixed">
            <div class="container-fluid">
                <div class="header-container">
                    <icon :icon="sidebar ? 'fal fa-times' : 'fal fa-bars'" class="sidebar-switch" @click.native="$store.dispatch('toggleSidebar')"></icon>
                    <div class="logo-container">
                        <router-link to="/" tag="div" class="logo"></router-link>
                    </div>
                    <div class="logo-mobile" @click="$router.push('/')"></div>
                    <div class="menu">
                        <router-link tag="div" to="/casino" :class="$route.path === '/' || $route.path === '/casino' ? 'active' : ''"><icon icon="fas fa-spade"></icon> {{ $t('general.head.casino') }}</router-link>
                        <router-link tag="div" to="/casino/game/tag/slots" :class="$route.path === '/casino/game/tag/slots' ? 'active' : ''"><icon icon="slots"></icon> {{ $t('general.head.slots') }}</router-link>
                        <router-link tag="div" to="/casino/game/tag/multiplayer" :class="$route.path === '/casino/game/tag/multiplayer' ? 'active' : ''"><icon icon="casino"></icon> {{ $t('general.head.live') }}</router-link>
                        <router-link tag="div" to="/coming-soon" :class="$route.path === '/coming-soon' ? 'active' : ''"><icon icon="sport"></icon> {{ $t('general.head.sport') }}</router-link>
                        <router-link tag="div" to="/promotions" :class="$route.path === '/promotions' ? 'active' : ''"><icon icon="gift"></icon> {{ $t('general.head.promotions') }}</router-link>
                    </div>
                    <content-placeholders v-if="!isGuest && !currencies" class="wallet_loader">
                        <content-placeholders-img/>
                    </content-placeholders>
                    <div class="wallet" v-if="!isGuest && currencies" v-click-outside="() => expand = false">
                        <div :class="`wallet-switcher ${expand ? 'active' : ''}`">
                            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                <div v-for="(currency, i) in currencies" v-if="currency.balance" class="option" :key="i" @click="$store.commit('setCurrency', currency.id); setCookie('currency', currency.id, 365); expand = false">
                                    <div class="wallet-switcher-icon">
                                        <icon :icon="currency.icon" :style="{ color: currency.style }"></icon>
                                    </div>
                                    <div class="wallet-switcher-content">
                                        <div><unit :fiat="fiatView" :to="currency.id" :value="demo ? currency.balance.demo : currency.balance.real"></unit></div>
                                        <span>{{ currency.name }}</span>
                                    </div>
                                </div>
                            </overlay-scrollbars>

                            <div class="bonus" v-if="bonus" :class="expandBonus ? 'expand' : ''" @click="bonus.length > 0 ? expandBonus = !expandBonus : false">
                                <div class="bonus-header">
                                    {{ $t('general.head.bonusBalance.title') }}
                                    <div v-if="bonus.length > 0">{{ $t('general.head.bonusBalance.available', { count: bonus.length }) }}</div>
                                    <div v-else><i class="fal fa-question-circle" @click="showBonusDesc"></i></div>
                                </div>
                                <div class="bonus-content" v-if="expandBonus">
                                    <div class="bonus-real-content">
                                        <div class="promotion" v-for="b in bonus" @click="a = useBonus; $store.commit('setCurrency', currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].id); $store.dispatch('setUseBonus', a === b._id ? null : b._id)">
                                            <div class="name">{{ b.description }}</div>
                                            <div>
                                                <div class="b">
                                                    {{ $t('general.head.bonusBalance.balance') }} <icon :icon="currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].icon" v-if="currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id]" :style="{ color: currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].style }"></icon>
                                                    <unit :fiat="fiatView" :to="currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id" :value="b.value"></unit>
                                                </div>
                                                <div class="wager">
                                                    <icon :icon="currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].icon" v-if="currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id]" :style="{ color: currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].style }"></icon>
                                                    {{ (b.neededToWager - b.wagered).toFixed(currencies[currencies[Object.keys(currencies).filter(e => currencies[e].walletId === b.currency)[0]].id].id.startsWith('local') ? 2 : 8) }}
                                                    {{ $t('general.head.bonusBalance.wager') }}
                                                </div>
                                                <div class="cancel" @click="cancelBonus(b._id)">
                                                    {{ $t('general.head.bonusBalance.cancel') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Demo balance is useless if there is no old (local) games.

                            <div class="option" @click="$store.commit('setDemo', !demo)">
                                <div class="wallet-switcher-icon">
                                    <i :class="demo ? 'fas fa-check-square' : 'far fa-square'"></i>
                                </div>
                                <div class="wallet-switcher-content">
                                    {{ $t('general.head.wallet_demo') }}
                                </div>
                            </div>
                            -->
                            <!--
                            <div class="option mt-2" @click="$store.commit('setFiatView', !fiatView)">
                                <div class="wallet-switcher-icon">
                                    <i :class="fiatView ? 'fas fa-check-square' : 'far fa-square'"></i>
                                </div>
                                <div class="wallet-switcher-content">
                                    {{ $t('general.head.view_in_fiat') }}
                                </div>
                            </div>
                            <div class="option select-option" v-if="!fiatView">
                                <div class="wallet-switcher-icon">
                                    <i class="far fa-sync-alt"></i>
                                </div>
                                <div class="wallet-switcher-content" style="padding-right: 15px; width: 100%">
                                    {{ $t('general.unit') }}:
                                    <select @change="$store.commit('setUnit', selectedUnit)" v-model="selectedUnit">
                                        <option value="btc">BTC</option>
                                        <option value="mbtc">milliBTC</option>
                                        <option value="bit">microBTC</option>
                                        <option value="satoshi">Satoshi</option>
                                    </select>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="btn btn-secondary icon" @click="expand = !expand; expand && useBonus ? expandBonus = true : false">
                            <icon :icon="currencies[currency].icon" v-if="currencies[currency]" :style="{ color: currencies[currency].style }"></icon>
                        </div>
                        <div class="balance" @click="expand = !expand; expand && useBonus ? expandBonus = true : false">
                            <icon icon="gift" v-if="useBonus" class="usingBonus"></icon>

                            <unit v-if="!useBonus" :fiat="fiatView" :to="currency" :value="currencies[currency].balance[demo ? 'demo' : 'real']"></unit>
                            <unit v-else-if="bonus" :fiat="fiatView" :to="currency" :value="bonus.filter(e => e._id === useBonus)[0].value" :class="useBonus ? 'yellow' : ''"></unit>

                            <transition-group mode="out-in" name="balance-a" :style="{ position: 'absolute' }">
                                <span :key="`key-${i}`" v-for="(animate, i) in animated" :class="`animated text-${animate.diff.action === 'subtract' ? 'danger' : 'success'}`">
                                    <unit :fiat="fiatView" :to="currency" :value="animate.diff.value"></unit>
                                </span>
                            </transition-group>
                            <i class="fal fa-angle-down"></i>
                        </div>
                        <div class="wallet-button" @click="demo ? openDemoBalanceModal() : openWalletModal()"><i class="fas fa-wallet"></i> <span>Wallet</span></div>
                    </div>
                    <div v-if="isGuest" :class="`right ${isGuest ? 'ml-auto' : ''}`">
                        <button class="btn btn-primary" @click="openAuthModal('auth')">{{ $t('general.auth.login') }}</button>
                        <button class="btn btn-secondary" @click="openAuthModal('register')">{{ $t('general.auth.register') }}</button>
                    </div>
                    <div v-else :class="`right ${isGuest ? 'ml-auto' : ''}`">
                        <div class="avatar-dropdown" @click="userDropdown = !userDropdown">
                            <img :src="user.user.avatar" alt>
                            <icon :icon="userDropdown ? 'fal fa-chevron-up' : 'fal fa-chevron-down'"></icon>
                        </div>
                        <div class="userDropdown" v-if="userDropdown" @click="userDropdown = false">
                            <div class="userDropdownHeader" @click="$router.push('/profile')">
                                <img :src="user.user.avatar" alt>
                                {{ user.user.name }}
                                <div class="link">
                                    <icon icon="link"></icon>
                                </div>
                            </div>
                            <div class="userDropdownVip">
                                <div class="level">
                                    <div class="icon">
                                        <img alt :src="`/img/misc/vip/${user.user.vipLevel}.png`">
                                    </div>
                                    <div class="text">{{ $t('general.head.vip', { level: user.user.vipLevel }) }}</div>
                                </div>
                                <div class="status">
                                    <div class="v-progress">
                                        <div class="c-1">
                                            <div class="text">
                                                <div class="info-t">{{ $t('general.head.deposit') }}</div>
                                                <div class="info-p" v-if="user.user.vipLevel < 10">
                                                    R${{ vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) }}
                                                    /
                                                    <span>R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) }}</span>
                                                </div>
                                            </div>
                                            <div class="bar">
                                                <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                                            </div>
                                        </div>
                                        <div class="c-2">
                                            <template v-if="user.user.vipLevel < 10">
                                                {{ fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) }}%
                                            </template>
                                            <template v-else>
                                                100%
                                            </template>
                                        </div>
                                    </div>
                                    <div class="v-progress">
                                        <div class="c-1">
                                            <div class="text">
                                                <div class="info-t">{{ $t('general.head.bet') }}</div>
                                                <div class="info-p" v-if="user.user.vipLevel < 10">
                                                    R${{ vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) }}
                                                    /
                                                    <span>R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) }}</span>
                                                </div>
                                            </div>
                                            <div class="bar">
                                                <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                                            </div>
                                        </div>
                                        <div class="c-2">
                                            <template v-if="user.user.vipLevel < 10">
                                                {{ fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) }}%
                                            </template>
                                            <template v-else>
                                                100%
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="userDropdownControls">
                                <div class="control" @click="$router.push('/vip')">
                                    <div class="icon"><img src="/img/misc/user/vip.png" alt></div>
                                    <div class="text">{{ $t('general.head.user.1') }}</div>
                                </div>
                                <div class="control" @click="$router.push('/profile')">
                                    <div class="icon"><img src="/img/misc/user/personal-center.png" alt></div>
                                    <div class="text">{{ $t('general.head.user.2') }}</div>
                                </div>
                                <div class="control" @click="$router.push('/profile/history/history/game')">
                                    <div class="icon"><img src="/img/misc/user/game-history.png" alt></div>
                                    <div class="text">{{ $t('general.head.user.3') }}</div>
                                </div>
                                <div class="control" @click="$store.dispatch('logout'); $router.push('/');">
                                    <div class="icon"><img src="/img/misc/user/log-out.png" alt></div>
                                    <div class="text">{{ $t('general.head.user.4') }}</div>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="action" data-notification-view @click="displayNotifications()">
                            <i class="fas fa-bell"></i>
                        </div>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import DemoBalanceModal from "../modals/DemoBalanceModal";
    import RankingsModal from "../modals/RankingsModal";
    import TermsModal from "../modals/TermsModal";
    import FaucetModal from "../modals/FaucetModal";
    import WalletModal from "../modals/WalletModal";
    import InvalidTokenModal from "../modals/InvalidTokenModal";

    export default {
        computed: {
            ...mapGetters(['user', 'isGuest', 'demo', 'unit', 'currency', 'currencies', 'sidebar', 'fiatView', 'vip', 'bonus', 'useBonus'])
        },
        data() {
            return {
                expand: false,
                selectedUnit: 'btc',
                animated: [],
                userDropdown: false,
                expandBonus: false
            }
        },
        mounted() {
            this.selectedUnit = this.unit;

            Bus.$on('event:bonusBalanceTransferred', (e) => {
                if(this.useBonus === e._id) {
                    this.$store.dispatch('setUseBonus', null);

                    InvalidTokenModal.methods.open();
                    window.location.reload();
                }
            });

            Bus.$on('event:balanceModification', (e) => {
                const animate = () => {
                    this.animated.push(e);
                    setTimeout(() => this.animated = this.animated.filter((a) => a !== e), 1000);
                };

                setTimeout(() => {
                    if(e.bonus) {
                        if(this.useBonus === e.bonus._id) {
                            const bonus = this.bonus;
                            bonus[Object.keys(bonus).filter(b => bonus[b]._id === e.bonus._id)[0]] = e.bonus;
                            this.$store.dispatch('setBonus', bonus);
                            animate();
                        }
                    } else {
                        const currencies = this.currencies;
                        currencies[e.currency].balance = {
                            real: e.balance,
                            demo: e.demo_balance
                        };
                        this.$store.dispatch('setCurrencies', currencies);

                        animate();
                    }
                }, e.delay);
            });
        },
        methods: {
            showBonusDesc() {
                alert(this.$i18n.t('general.head.bonusBalance.empty'));
            },
            cancelBonus(id) {
                if(confirm('Are you sure? You won\'t be able to get this bonus again unless the promotion states otherwise.')) {
                    if(this.useBonus === id) this.$store.dispatch('setUseBonus', null);

                    axios.post('/api/cancelBonus', {id: id}).then(() => {
                        this.$store.dispatch('updateBonus');
                    });
                }
            },
            openWalletModal() {
                WalletModal.methods.open();
            },
            displayNotifications() {
                Bus.$emit('notifications:toggle');
            },
            fixPercent(e) {
                return e >= 100 ? 100 : e.toFixed(2);
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            openDemoBalanceModal() {
                DemoBalanceModal.methods.open();
            },
            openRankingsModal() {
                RankingsModal.methods.open(this.currencies);
            },
            openFaucetModal() {
                if(this.isGuest) return this.openAuthModal('auth');
                FaucetModal.methods.open();
            },
            openTerms(type) {
                TermsModal.methods.open(type);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    header {
        height: $header-height;
        display: initial !important;
        flex-shrink: 0;
        z-index: 38002;

        .bonus {
            max-width: 235px;

            .bonus-header {
                background: linear-gradient(90.6deg, #43BB41 0.07%, rgba(48, 66, 95, 0.87) 99.61%);
                display: flex;
                align-items: center;
                padding: 10px 15px;
                font-weight: 600;
                border-radius: 10px;
                margin-top: 10px;
                //margin-bottom: 15px;

                div:first-child {
                    color: #fdd455;
                }

                div {
                    margin-left: auto;
                }
            }

            .bonus-content {
                margin-bottom: 15px;
                border-top: unset;
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                background: linear-gradient(90.6deg, #43BB41 0.07%, rgba(48, 66, 95, 0.87) 99.61%);
                position: relative;
                padding-bottom: 4px;

                .bonus-real-content {
                    position: relative;
                    left: 4px;
                    width: calc(100% - 8px);
                    height: calc(100% - 2px);
                    background: #19212e;
                    border-bottom-left-radius: 10px;
                    border-bottom-right-radius: 10px;
                    padding: 5px;

                    .promotion {
                        margin-bottom: 5px;
                        border-bottom: 1px solid rgba(255,255,255, .3);
                        background: #19212e;
                        transition: background .3s ease;
                        padding: 5px 10px;
                        border-radius: 5px;

                        &:hover {
                            background: lighten(#19212e, 5%);
                        }

                        .name {
                            color: #fdd455;
                        }

                        .b {
                            font-size: 0.9em;
                            margin-bottom: 3px;
                        }

                        .wager {
                            font-size: 0.7em;
                            font-weight: 600;
                        }

                        .cancel {
                            margin-top: 6px;
                            font-size: 0.9em;
                            opacity: 0.6;
                            transition: opacity .3s ease;

                            &:hover {
                                opacity: 1;
                            }
                        }

                        div {
                            &:first-child {
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                margin-right: 5px;
                            }
                        }

                        &:last-child {
                            margin-bottom: 0;
                            border-bottom: unset;
                            padding-bottom: unset;
                        }
                    }
                }
            }

            &.expand {
                .bonus-header {
                    border-bottom-left-radius: 0;
                    border-bottom-right-radius: 0;
                    margin-bottom: 0;
                }
            }
        }

        button {
            font-size: 0.9em !important;
            padding: 10px 20px !important;
        }

        .fixed {
            height: $header-height;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            z-index: 38999;

            .sidebar-switch {
                opacity: .4;
                cursor: pointer;
                margin-left: 15px;
                z-index: 6;
                position: absolute;
                top: 50%;
            }

            .header-container {
                display: flex;
                align-items: center;
                height: $header-height;

                .menuSwitcher {
                    display: none;
                    margin-left: 15px;
                    opacity: .5;
                    transition: opacity .3s ease;
                    cursor: pointer;

                    &:hover {
                        opacity: 1;
                    }
                }
            }

            @include themed() {
                background: t('header');

                .logo-mobile {
                    display: none;
                    background: url('/img/misc/logo.png') no-repeat center;
                    background-size: contain;
                    width: 35px;
                    height: 35px;
                    margin-left: 10px;
                    cursor: pointer;
                }

                .logo-container {
                    border-radius: 10px;
                    transform: skewY(-3deg) skewX(-17deg);
                    padding: 36px 25px 16px 66px;
                    margin: -30px 0 -20px -65px;
                    background: t('headerLogoArea');
                    z-index: 5;

                    .logo {
                        width: 195px;
                        height: 40px;
                        position: relative;
                        margin-left: 22px;
                        display: flex;
                        cursor: pointer;
                        background: url('/img/misc/logo_betdino.png') no-repeat center;
                        background-size: contain;
                        transform: skewY(3deg) skewX(17deg);
                    }
                }
            }

            .menu {
                position: absolute;
                left: 290px;
                display: flex;
                background: #2A3546;
                border-radius: 50px;

                @media(max-width: 1220px) {
                    display: none;
                }

                @media(max-width: 1335px) {
                    div {
                        padding: 7px 12px !important;
                        font-size: 13px !important;
                    }
                }

                @include themed() {
                    div {
                        transition: color 0.3s ease, background .3s ease;
                        position: relative;
                        cursor: pointer;
                        text-transform: uppercase;
                        font-weight: 600;
                        padding: 10px 20px;
                        border-radius: 50px;
                        color: #90A3C7;

                        &:last-child {
                            margin-right: 0;
                        }

                        svg, i {
                            margin-right: 5px;
                        }

                        &:first-child {
                            margin-left: 0;
                        }

                        &:last-child {
                            margin-right: 0;
                        }
                    }

                    div.active {
                        background: t('secondary');
                        color: white;
                    }
                }
            }

            .right {
                display: flex;
                margin-left: 10px;
                align-items: center;

                .userDropdown {
                    position: absolute;
                    top: 64px;
                    right: 20px;
                    background: linear-gradient(107.38deg, #2D4F89 5.92%, #182337 100%);
                    border-radius: 15px;
                    max-width: 437px;

                    @media(max-width: 500px) {
                        max-width: 340px;
                        font-size: 0.7em;
                    }

                    .userDropdownVip {
                        display: flex;
                        padding: 25px 0;

                        @media(max-width: 500px) {
                            padding: 10px 0;
                        }

                        .level {
                            width: 35%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-direction: column;

                            @media(max-width: 500px) {
                                width: 40%;
                            }

                            .icon {
                                img {
                                    width: 56px;
                                    height: 62px;

                                    @media(max-width: 500px) {
                                        width: 30px;
                                        height: 30px;
                                    }
                                }
                            }

                            .text {
                                background: linear-gradient(98.84deg, #FF9F47 0%, #F4E557 112.15%);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                                text-fill-color: transparent;
                                font-weight: 600;
                                font-size: 1.3em;
                                margin-top: 10px;
                            }
                        }

                        .status {
                            width: 100%;

                            .v-progress {
                                display: flex;
                                width: 100%;
                                margin-bottom: 15px;

                                &:last-child {
                                    margin-bottom: 0;
                                }

                                .c-1 {
                                    width: 100%;

                                    .text {
                                        display: flex;

                                        .info-t {
                                            font-weight: 600;
                                        }

                                        .info-p {
                                            margin-left: auto;
                                            font-weight: 600;

                                            span {
                                                color: #FFCD4D;
                                            }
                                        }
                                    }

                                    .bar {
                                        width: 100%;
                                        height: 10px;
                                        border-radius: 10px;
                                        background: #425A84;
                                        margin-top: 5px;

                                        div {
                                            height: 100%;
                                            @include themed() {
                                                background: t('secondary');
                                            }
                                        }
                                    }
                                }

                                .c-2 {
                                    margin-top: auto;
                                    margin-left: 15px;
                                    margin-right: 25px;
                                }
                            }
                        }
                    }

                    .userDropdownHeader {
                        display: flex;
                        align-items: center;
                        padding: 15px 25px;
                        font-weight: 600;
                        background: #2B446F;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                        cursor: pointer;

                        @media(max-width: 500px) {
                            padding: 10px 15px;
                        }

                        img {
                            width: 50px;
                            height: 50px;
                            border-radius: 50%;
                            margin-right: 15px;

                            @media(max-width: 500px) {
                                width: 25px;
                                height: 25px;
                                margin-right: 10px;
                            }
                        }

                        .link {
                            margin-left: auto;
                            background: #23334E;
                            border-radius: 6px;
                            padding: 10px;
                            color: #90A3C7;
                            font-size: 1.4em;
                            transition: all .3s ease;
                            cursor: pointer;

                            @media(max-width: 500px) {
                                padding: 5px;
                                font-size: 1.2em;
                            }

                            &:hover {
                                transform: scale(1.05);
                            }
                        }
                    }

                    .userDropdownControls {
                        background: #162642;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                        padding: 15px;
                        display: flex;
                        flex-wrap: wrap;

                        .control {
                            width: calc(50% - 10px);
                            margin: 5px;
                            background: #304A76;
                            display: flex;
                            padding: 10px 15px;
                            border-radius: 10px;
                            font-weight: 600;
                            font-size: 1.1em;
                            cursor: pointer;
                            transition: background .3s ease;

                            &:hover {
                                background: lighten(#304A76, 5%);
                            }

                            .icon {
                                margin-right: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .text {
                                display: flex;
                                align-items: center;
                                line-height: 20px;
                            }
                        }
                    }
                }

                .avatar-dropdown {
                    display: flex;
                    background: #3D495F;
                    margin-left: 10px;
                    border-radius: 100px;
                    padding: 5px;
                    cursor: pointer;

                    &:hover {
                        svg, i {
                            transform: scale(1.05);
                        }
                    }

                    svg, i {
                        margin: auto 15px;
                        transition: all .3s ease;
                    }

                    img {
                        width: 35px;
                        height: 35px;
                        border-radius: 50%;
                    }
                }

                .action {
                    display: flex;
                    align-content: center;
                    position: relative;
                    margin-left: 10px;

                    .notification {
                        position: absolute !important;
                        top: 7px !important;
                        left: 19px !important;
                        width: 8px !important;
                        height: 8px !important;
                    }

                    i {
                        font-size: 1.25em;
                        margin: 10px;
                        opacity: 0.35;
                        transition: opacity 0.3s ease;

                        &:hover {
                            opacity: 1;
                            cursor: pointer;
                        }
                    }
                }

                .btn {
                    padding: 10px 15px !important;
                    margin-right: 15px;
                    font-weight: 600;

                    &:first-child {
                        background: #3D495F;
                        color: white !important;
                    }

                    &:last-child {
                        margin-right: 0;
                    }
                }
            }
        }
    }

    @include only_safari('header', (
        display: contents !important
    ));

    @media(max-width: 991px) {
        header .sidebar-switch, header .logo-container {
            display: none;
        }

        header .logo-mobile {
            display: block !important;
        }
    }

    .balance-a-enter-active, .balance-a-leave-active {
        transition: all 1s ease;
    }

    .balance-a-enter {
        margin-top: 25px;
        opacity: 1 !important;
    }

    .balance-a-enter-to {
        margin-top: 0;
        opacity: 0 !important;
    }

    .balance-a-leave, .balance-a-leave-to {
        opacity: 0 !important;
    }
</style>
