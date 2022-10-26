<template>
    <div class="container">
        <div class="referral">
            <div class="center-header" @click="mobileDropdown = !mobileDropdown" :class="mobileDropdown ? 'mobileDropdown' : ''">
                <div class="tab" :class="tab === 'invite' ? 'active' : ''" @click="tab = 'invite'">
                    <icon icon="fal fa-user"></icon>
                    {{ $t('referral.tabs.invite') }}
                </div>
                <div class="tab" :class="tab === 'forms' ? 'active' : ''" @click="tab = 'forms'">
                    <icon icon="fal fa-clipboard-list"></icon>
                    {{ $t('referral.tabs.forms') }}
                </div>
                <div class="tab" :class="tab === 'history' ? 'active' : ''" @click="tab = 'history'">
                    <icon icon="fal fa-history"></icon>
                    Statistics
                    {{ $t('referral.tabs.stats') }}
                </div>
            </div>
            <div v-if="tab === 'invite'" class="forms f-invite">
                <div class="stat">
                    <div class="invite">
                        <div class="i-title">{{ $t('referral.invite.title') }}</div>
                        <div class="input">{{ $t('referral.invite.url') }}</div>
                        <div class="input-g">
                            <input readonly :value="'https://betdino.io/?c=' + this.user.user.inviteCode">
                            <button class="btn btn-primary" @click="copy('url')"><icon icon="link"></icon></button>
                        </div>
                        <div class="input">{{ $t('referral.invite.code') }}</div>
                        <div class="input-g">
                            <input readonly :value="this.user.user.inviteCode">
                            <button class="btn btn-primary" @click="copy('code')"><icon icon="link"></icon></button>
                        </div>
                    </div>
                    <div class="stats">
                        <div class="stat-row">
                            <div class="stat-cell">
                                <div>{{ $t('referral.invite.deposited') }}</div>
                                <div>{{ affiliateStats ? affiliateStats.activeReferrals : '...' }}</div>
                            </div>
                            <div class="stat-cell">
                                <div>{{ $t('referral.invite.bonus') }}</div>
                                <div>R$ {{ affiliateStats ? affiliateStats.invitation_bonus_today + affiliateStats.betting_commission_today : '...' }}</div>
                            </div>
                            <div class="stat-cell">
                                <div>{{ $t('referral.invite.yesterday') }}</div>
                                <div>R$ {{ affiliateStats ? affiliateStats.invitation_bonus_yesterday + affiliateStats.betting_commission_yesterday : '...' }}</div>
                            </div>
                        </div>
                        <div class="goal" @click="$router.push('/distributor-benefits')">
                            <!--<div class="g-title">Millions of Distributor Benefits</div>
                            <div class="description">-1 more people to invite before the goal is reached</div>
                            <div class="amount">R$ 0</div>
                            <div class="img"></div>-->
                        </div>
                    </div>
                </div>
                <div class="separator">{{ $t('referral.invite.issued') }}</div>
                <div class="issued">
                    <div class="block">
                        <div class="b-title">{{ $t('referral.invite.bonus2') }}</div>
                        <div class="amount">R$ {{ affiliateStats ? affiliateStats.totalTodayInviteBonus : '...' }}</div>
                        <div class="img"></div>
                    </div>
                    <div class="block">
                        <div class="b-title">{{ $t('referral.invite.bc') }}</div>
                        <div class="amount">R$ {{ affiliateStats ? affiliateStats.totalTodayBettingCommission : '...' }}</div>
                        <div class="img"></div>
                    </div>
                </div>
                <div class="separator">{{ $t('referral.invite.how') }}</div>
                <div class="r-info">
                    <div class="r-title">{{ $t('referral.invite.how2') }}</div>
                    <div class="description" v-html="$t('referral.invite.how3')"></div>
                    <div class="i-table">
                        <div class="i-table-row">
                            <div>{{ $t('referral.invite.deposited2') }}</div>
                            <div>0-999</div>
                            <div>1000-2999</div>
                            <div>3000-4999</div>
                            <div>&gt; 5000</div>
                        </div>
                        <div class="i-table-row">
                            <div>{{ $t('referral.invite.bonus3') }}</div>
                            <div>R$ 8</div>
                            <div>R$ 10</div>
                            <div>R$ 12</div>
                            <div>R$ 15</div>
                        </div>
                    </div>
                </div>
                <div class="separator">{{ $t('referral.invite.commission.title') }}</div>
                <div class="separator sm">{{ $t('referral.invite.commission.description2') }}</div>
                <div class="big-info">
                    <div class="block" v-html="$t('referral.invite.commission.description')"></div>
                    <div class="block">
                        <div class="img"></div>
                    </div>
                    <div class="block">
                        <div class="img"></div>
                    </div>
                    <div class="block">
                        <div class="b-title">{{ $t('referral.invite.calculator.title') }}</div>
                        <div class="amount">R$ {{ calc * 50 }}</div>
                        <div class="description">{{ $t('referral.invite.calculator.description') }}</div>

                        <div class="calc-wrapper">
                            <div class="calc-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="last">
                    <div class="leaderboard">
                        <div class="l-title">{{ $t('referral.invite.leaderboard.title') }}</div>
                        <div class="block">
                            <template v-if="leaderboard">
                                <div class="user">
                                    <template v-if="leaderboard[1]">
                                        <div class="user-title">{{ $t('referral.invite.leaderboard.place', { place: 2 }) }}</div>
                                        <div class="user-avatar" :style="{ backgroundImage: `url(${leaderboard[1].user.avatar})` }"></div>
                                        <div class="user-name">{{ leaderboard[1].user.name }}</div>
                                        <div class="user-amount">R$ {{ leaderboard[1].sum }}</div>
                                    </template>
                                </div>
                                <div class="user">
                                    <template v-if="leaderboard[0]">
                                        <div class="user-title">{{ $t('referral.invite.leaderboard.place', { place: 1 }) }}</div>
                                        <div class="user-avatar" :style="{ backgroundImage: `url(${leaderboard[0].user.avatar})` }"></div>
                                        <div class="user-name">{{ leaderboard[0].user.name }}</div>
                                        <div class="user-amount">R$ {{ leaderboard[0].sum }}</div>
                                    </template>
                                </div>
                                <div class="user">
                                    <template v-if="leaderboard[1]">
                                        <div class="user-title">{{ $t('referral.invite.leaderboard.place', { place: 3 }) }}</div>
                                        <div class="user-avatar" :style="{ backgroundImage: `url(${leaderboard[2].user.avatar})` }"></div>
                                        <div class="user-name">{{ leaderboard[2].user.name }}</div>
                                        <div class="user-amount">R$ {{ leaderboard[2].sum }}</div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="who-won">
                        <div class="w-title">{{ $t('referral.invite.whoWon.title') }}</div>
                        <template v-if="affiliateStats">
                            <div class="user" v-for="log in affiliateStats.recentLog">
                                <div class="name">{{ log.user.name }}</div>
                                <div class="desc">
                                    {{ $t('profile.invite.bonus.' + log.data.type) }}
                                </div>
                                <div class="amount">R$ {{ log.data.amount }}</div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div v-else-if="tab === 'forms'" class="forms">
                <div class="title">{{ $t('referral.forms.title') }}</div>
                <div class="form">
                    <div class="left">
                        <div class="chart-profit-today"></div>
                        <div class="total">R$ {{ !affiliateStats ? '...' : (affiliateStats.betting_commission_today + affiliateStats.invitation_bonus_today).toFixed(2) }}</div>
                        <div class="desc">{{ $t('referral.forms.title') }}</div>
                    </div>
                    <div class="content">
                        <div class="stats">
                            <div class="stat">
                                <div>R$ {{ !affiliateStats ? '...' : affiliateStats.betting_commission_today.toFixed(2) }}</div>
                                <div>{{ $t('referral.forms.bc') }}</div>
                            </div>
                            <div class="stat">
                                <div>R$ {{ !affiliateStats ? '...' : affiliateStats.invitation_bonus_today.toFixed(2) }}</div>
                                <div>{{ $t('referral.forms.ib') }}</div>
                            </div>
                        </div>
                        <div class="desc" v-html="$t('referral.forms.description')"></div>
                    </div>
                </div>
                <div class="title">{{ $t('referral.forms.total') }}</div>
                <div class="form">
                    <div class="left">
                        <img alt src="/img/misc/chest.png">
                        <div class="total">R$ {{ !affiliateStats ? '...' : (affiliateStats.invitation_bonus_total + affiliateStats.betting_commission_total + affiliateStats.deposit_bonus).toFixed(2) }}</div>
                        <div class="desc">{{ $t('referral.forms.total') }}</div>
                    </div>
                    <div class="content">
                        <div class="stats">
                            <div class="stat">
                                <div>R$ {{ !affiliateStats ? '...' : affiliateStats.betting_commission_total.toFixed(2) }}</div>
                                <div>{{ $t('referral.forms.bc') }}</div>
                            </div>
                            <div class="stat">
                                <div>R$ {{ !affiliateStats ? '...' : affiliateStats.invitation_bonus_total.toFixed(2) }}</div>
                                <div>{{ $t('referral.forms.ib') }}</div>
                            </div>
                            <div class="stat">
                                <div>R$ {{ !affiliateStats ? '...' : affiliateStats.deposit_bonus.toFixed(2) }}</div>
                                <div>{{ $t('referral.forms.deposited') }}</div>
                            </div>
                        </div>
                        <div class="desc">{{ $t('referral.forms.desc') }}</div>
                    </div>
                </div>
            </div>
            <div class="tab-content" v-else-if="tab === 'history'">
                <div class="history">
                    <table class="live-table" v-if="bonusTransactions.length > 0">
                        <thead>
                            <tr>
                                <th>{{ $t('referral.history.user') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('referral.history.type') }}</th>
                                <th>{{ $t('referral.history.amount') }}</th>
                                <th>{{ $t('referral.history.date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="live_games">
                            <tr v-for="tx in bonusTransactions">
                                <th style="cursor: pointer" @click="openUserModal(tx.user._id)">
                                    <img :src="tx.user.avatar" width="32" height="32" style="margin-right: 5px"> {{ tx.user.name }}
                                </th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t('profile.invite.bonus.' + tx.type) }}
                                </th>
                                <th>
                                    <div>{{ tx.currency }} {{ tx.amount }}</div>
                                </th>
                                <th>
                                    <div>{{ new Date(tx.date).toLocaleString() }}</div>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else style="text-align: center">{{ $t('referral.history.empty') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    require('jquery-ui/ui/widgets/slider');
    import { mapGetters } from 'vuex';
    import UserModal from "../modals/UserModal";

    export default {
        data() {
            return {
                tab: 'invite',
                mobileDropdown: false,

                bonusTransactions: [],
                affiliateStats: null,
                leaderboard: null,

                calc: 0
            }
        },
        computed: {
            ...mapGetters(['user'])
        },
        watch: {
            tab() {
                this.loadTabContent(true);
            }
        },
        created() {
            axios.post('/api/affiliateStats').then(({ data }) => this.affiliateStats = data);
        },
        mounted() {
            $('.calc-slider').slider({
                range: 'min',
                min: 0,
                max: 100000,
                value: 0,
                slide: (event, ui) => {
                    this.calc = ui.value;
                }
            });

            axios.post('/api/affiliateLeaderboard').then(({ data }) => this.leaderboard = data);
        },
        methods: {
            openUserModal(id) {
                UserModal.methods.open(id);
            },
            copy(type) {
                try {
                    navigator.clipboard.writeText(type === 'url' ? 'https://betdino.io/?c=' + this.user.user.inviteCode : this.user.user.inviteCode);
                    this.$toast.success('Copied!');
                } catch (e) {
                    this.$toast.error('Failed to copy, your invite code: ' + this.user.user.inviteCode);
                }
            },
            loadTabContent(reset = false) {
                if(reset) {
                    this.bonusTransactions = [];
                }

                if(this.tab === 'history') {
                    axios.post('/api/user/bonusTransactions').then(({ data }) => this.bonusTransactions = data);
                }

                if(this.tab === 'forms') {
                    setTimeout(() => {
                        new ApexCharts(document.querySelector(".chart-profit-today"), {
                            series: this.affiliateStats.betting_commission_today + this.affiliateStats.invitation_bonus_today === 0 ? [ 1 ] : [ this.affiliateStats.betting_commission_today, this.affiliateStats.invitation_bonus_today ],
                            labels: this.affiliateStats.betting_commission_today + this.affiliateStats.invitation_bonus_today === 0 ? [ 'You haven\'t earned anything today.' ] : [ 'Betting Commission', 'Invitation Bonus' ],
                            borderWidth: 0,
                            stroke: {
                                show: false,
                            },
                            dataLabels: {
                                enabled: false
                            },
                            chart: {
                                type: 'donut',
                                toolbar: {
                                    show: false
                                }
                            },
                            legend: {
                                show: false
                            },
                            tooltip: {
                                show: false
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        }).render();
                    });
                }
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/themes";

    .referral {
        margin-bottom: 35px;

        .forms {
            &.f-invite {
                .last {
                    margin-top: 15px;
                    display: flex;

                    @media(max-width: 991px) {
                        flex-direction: column;

                        .leaderboard {
                            width: 100% !important;
                            margin-bottom: 15px;
                        }

                        .who-won {
                            width: 100% !important;
                        }
                    }

                    .leaderboard {
                        background: #273A5B;
                        border-radius: 10px;
                        padding: 25px 20px;
                        margin-right: 10px;
                        width: 50%;

                        .block {
                            background: linear-gradient(158.63deg, #162D53 9.3%, #3471D9 93.33%);
                            border-radius: 10px;
                            display: flex;
                            padding: 25px;

                            .user {
                                margin: auto;
                                width: 100%;
                                text-align: center;

                                &:nth-child(1), &:nth-child(3) {
                                    margin-top: 40px;
                                }

                                .user-avatar {
                                    background-size: cover;
                                    background-position: center;
                                    background-repeat: no-repeat;
                                    width: 50px;
                                    height: 50px;
                                    margin: auto;
                                    margin-bottom: 5px;
                                    border-radius: 50%;
                                }

                                .user-title {
                                    margin-bottom: 10px;
                                    font-size: 1.1em;
                                    font-weight: 600;
                                }

                                &:nth-child(1) {
                                    .user-title {
                                        color: #31C5E5;
                                    }
                                }

                                &:nth-child(2) {
                                    .user-title {
                                        color: #FFCC00;
                                    }
                                }

                                &:nth-child(3) {
                                    .user-title {
                                        color: #E5A831;
                                    }
                                }

                                .user-name {
                                    font-weight: 600;
                                }
                            }
                        }

                        .l-title {
                            text-align: center;
                            font-weight: 600;
                            font-size: 18px;
                            margin-bottom: 15px;
                        }
                    }

                    .who-won {
                        background: #273A5B;
                        border-radius: 10px;
                        padding: 25px 20px;
                        width: 50%;

                        .user {
                            display: flex;
                            width: 100%;
                            margin-bottom: 15px;
                            padding: 15px;
                            background: #122342;
                            border-radius: 10px;

                            .desc {
                                margin: auto;
                            }

                            .amount {
                                color: #FFCC00;
                                font-weight: 600;
                            }
                        }

                        .w-title {
                            text-align: center;
                            font-weight: 600;
                            font-size: 18px;
                            margin-bottom: 15px;
                        }
                    }
                }

                .big-info {
                    padding: 20px;
                    background: linear-gradient(158.63deg, #162D53 9.3%, #3471D9 93.33%);
                    border-radius: 10px;
                    display: flex;
                    flex-wrap: wrap;

                    @media(max-width: 991px) {
                        flex-direction: column;

                        .block {
                            width: 100% !important;

                            &:nth-child(2) {
                                display: none !important;
                            }

                            &:nth-child(3) {
                                margin-bottom: 15px;
                            }
                        }
                    }

                    .block {
                        padding: 20px;
                        width: calc(50% - 10px);
                        flex-shrink: 0;

                        &:nth-child(4) {
                            background: #122342;
                            border-radius: 10px;
                            padding: 20px;
                            display: flex;
                            flex-direction: column;

                            .b-title {
                                font-weight: 600;
                                text-align: center;
                                font-size: 1.2em;
                            }

                            .calc-wrapper {
                                width: 100%;
                                margin-top: auto;

                                .calc-slider {
                                    background: #5882CA;
                                    height: 4px;
                                    border-radius: 10px;

                                    div {
                                        background: darken(#43BB41, 1%);
                                        border-radius: 10px;
                                    }

                                    span {
                                        background: #43BB41;
                                        border-radius: 100px;
                                        margin-top: -3px;
                                        cursor: grab;
                                    }

                                    &:active {
                                        span {
                                            cursor: grabbing;
                                        }
                                    }
                                }
                            }

                            .amount {
                                color: #FFCC00;
                                font-weight: 600;
                                font-size: 2.5em;
                                text-align: center;
                                margin-top: 15px;
                                margin-bottom: 15px;
                            }

                            .description {
                                width: 80%;
                                margin-left: auto;
                                margin-right: auto;
                                text-align: center;
                            }
                        }

                        &:nth-child(3) {
                            background: #122342;
                            border-radius: 10px;
                            padding: 20px;
                            margin-right: 10px;

                            .img {
                                background: url('/img/misc/referral-tree.png') no-repeat center;
                                background-size: contain;
                                height: 280px;
                                width: 100%;
                                margin: auto;
                            }
                        }

                        &:nth-child(2) {
                            display: flex;
                            flex-direction: column;

                            .img {
                                background: url('/img/misc/referral-img.png') no-repeat center;
                                background-size: contain;
                                height: 280px;
                                width: 100%;
                                margin: auto;
                            }
                        }

                        &:first-child {
                            color: #C7D6F1;
                            font-weight: 600;
                            margin-right: 10px;

                            .more {
                                margin-top: 15px;
                                padding: 20px;
                                color: white;
                                background: rgba(12, 27, 55, 0.45);
                                border-radius: 10px;
                                font-weight: 400;
                                font-size: .9em;
                            }

                            b {
                                color: #FFCC00;
                            }
                        }
                    }
                }

                .r-info {
                    background: linear-gradient(158.63deg, #162D53 9.3%, #3471D9 93.33%);
                    border-radius: 10px;
                    padding: 25px;
                    position: relative;

                    @media(max-width: 991px) {
                        &:before, &:after {
                            display: none;
                        }
                    }

                    .i-table {
                        background: rgba(255, 255, 255, 0.11);
                        border-radius: 10px;
                        padding: 20px;
                        margin-top: 20px;

                        .i-table-row {
                            display: flex;
                            margin-bottom: 15px;

                            &:last-child {
                                margin-bottom: 0;
                            }

                            div {
                                text-align: center;
                                padding: 10px;
                                width: 100%;
                                background: #1E3A6C;
                                border-radius: 10px;
                                margin-right: 15px;

                                &:last-child {
                                    margin-right: 0;
                                }

                                &:first-child {
                                    color: #FFD338;
                                    font-weight: 600;
                                }
                            }
                        }

                        @media(max-width: 991px) {
                            display: flex;

                            .i-table-row {
                                flex-direction: column;
                                width: 100%;

                                &:first-child {
                                    margin-right: 15px;
                                }

                                div {
                                    margin-bottom: 15px;

                                    &:last-child {
                                        margin-bottom: 0;
                                    }
                                }
                            }
                        }
                    }

                    &:after, &:before {
                        content: '';
                        position: absolute;
                        background: url('/img/misc/coin.png') no-repeat center;
                        background-size: cover;
                        width: 60px;
                        height: 60px;
                    }

                    &:after {
                        filter: blur(4px);
                        transform: rotate(-210deg);
                        top: 65px;
                        left: 75px;
                    }

                    &:before {
                        right: 50px;
                        top: 45px;
                        width: 80px;
                        height: 80px;
                    }

                    &:before {

                    }

                    .description {
                        color: #C7D6F1;
                        text-align: center;
                        width: 65%;
                        margin: auto;

                        @media(max-width: 991px) {
                            width: 90%;
                        }

                        b {
                            color: white;
                        }
                    }

                    .r-title {
                        text-align: center;
                        font-weight: 600;
                        font-size: 1.4em;
                        margin-bottom: 15px;
                    }
                }

                .issued {
                    display: flex;

                    @media(max-width: 991px) {
                        flex-direction: column;

                        .block {
                            margin: unset;
                        }

                        .block:first-child {
                            margin-bottom: 15px;
                        }
                    }

                    .block {
                        margin: 10px;
                        padding: 20px 25px;
                        background: #293853;
                        border-radius: 10px;
                        position: relative;
                        width: 100%;

                        .b-title {
                            font-weight: 600;
                            font-size: 1.5em;
                        }

                        .amount {
                            color: #FFD338;
                            margin-top: 10px;
                            font-weight: 600;
                            font-size: 1.3em;
                        }

                        .img {
                            position: absolute;
                            top: 50%;
                            right: 35px;
                            transform: translateY(-50%);
                            width: 100px;
                            height: 100px;
                        }

                        &:first-child {
                            .img {
                                background: url('/img/misc/goal.png') no-repeat center;
                                background-size: cover;
                            }
                        }

                        &:last-child {
                            .img {
                                background: url('/img/misc/bet-c.png') no-repeat center;
                                background-size: cover;
                            }
                        }
                    }
                }

                .separator {
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 1.6em;
                    text-align: center;
                    margin-top: 35px;
                    margin-bottom: 15px;

                    &.sm {
                        font-size: 16px;
                        color: #90A3C7;
                        font-weight: 600;
                        margin-top: -10px;
                        margin-bottom: 35px;
                    }

                    @media(max-width: 991px) {
                        font-size: 1.3em;

                        &.sm {
                            font-size: 10px;
                        }
                    }
                }

                .stat {
                    background: #293853;
                    border-radius: 10px;
                    padding: 20px;
                    display: flex;
                    margin-top: 20px;

                    .stats {
                        margin-left: 20px;
                        width: 100%;

                        .goal {
                            background: url('/img/misc/promotions/headers/d-b.png') no-repeat 15% center;
                            background-size: cover;
                            cursor: pointer;
                            //background: linear-gradient(158.63deg, #111721 9.3%, #3471D9 93.33%);
                            border-radius: 10px;
                            display: flex;
                            flex-direction: column;
                            height: 150px;
                            padding: 0 25px;
                            position: relative;

                            .g-title {
                                margin-top: auto;
                                font-size: 20px;
                                line-height: 24px;
                                margin-bottom: 15px;
                                font-style: italic;
                                font-weight: 800;
                            }

                            .description {
                                margin-bottom: 10px;
                            }

                            .amount {
                                margin-bottom: auto;
                                font-weight: 800;
                                font-size: 20px;
                                line-height: 24px;
                                color: #FFD338;
                            }

                            .img {
                                background: url('/img/misc/goal.png') no-repeat center;
                                background-size: cover;
                                width: 110px;
                                height: 110px;
                                position: absolute;
                                right: 35px;
                                top: 50%;
                                transform: translateY(-50%);
                            }
                        }

                        .stat-row {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            background: #364B70;
                            border-radius: 10px;
                            width: 100%;
                            padding: 15px 0;
                            margin-bottom: 15px;

                            .stat-cell {
                                text-align: center;
                                margin: auto;

                                div:first-child {
                                    color: #90A3C7;
                                }
                            }
                        }
                    }

                    .invite {
                        width: 240px;
                        display: flex;
                        flex-direction: column;
                        background: linear-gradient(158.63deg, #111721 9.3%, #3471D9 93.33%);
                        border-radius: 10px;
                        padding: 15px;
                        flex-shrink: 0;

                        .input {
                            font-weight: 600;
                            margin-bottom: 5px;
                        }

                        .input-g {
                            display: flex;

                            input {
                                font-size: 0.7em;
                            }

                            .btn {
                                color: white !important;
                            }
                        }

                        .i-title {
                            text-align: center;
                            margin: 15px;
                            color: #FFD338;
                            font-weight: 600;
                            font-size: 1.1em;
                        }
                    }

                    @media(max-width: 991px) {
                        flex-direction: column;

                        .invite {
                            width: 100%;
                            margin-bottom: 15px;
                        }

                        .stats {
                            margin-left: unset;
                        }
                    }
                }
            }

            ul li {
                margin-top: 15px;
            }

            .form {
                display: flex;
                background: linear-gradient(158.63deg, #162D53 9.3%, #3471D9 93.33%);
                border-radius: 15px;
                margin-top: 15px;

                .left {
                    background: linear-gradient(158.63deg, #162D53 9.3%, #3471D9 93.33%);
                    width: 200px;
                    border-radius: 15px;
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;

                    img {
                        margin-bottom: 15px;
                    }

                    .total {
                        font-size: 1.3em;
                        color: #FFCC00;
                        font-weight: 600;
                    }
                }

                @media(max-width: 991px) {
                    flex-direction: column;

                    .left {
                        width: 100% !important;
                    }
                }

                .content {
                    margin: auto;
                    padding: 20px;

                    .stats {
                        display: flex;
                        border-radius: 10px;
                        background: rgba(12, 27, 55, 0.45);
                        padding: 15px;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 10px;

                        .stat {
                            display: flex;
                            flex-direction: column;
                            text-align: center;
                            margin-right: 20px;

                            &:last-child {
                                margin-right: 0;
                            }

                            div:first-child {
                                font-weight: 600;
                                color: #FFD338;
                                font-size: 1.1em;
                            }

                            div:last-child {
                                font-weight: 600;
                            }
                        }
                    }
                }
            }

            .title {
                font-weight: 600;
                margin-top: 25px;
                font-size: 1.6em;
                text-transform: uppercase;
            }
        }

        .live-table th {
            color: unset !important;
        }

        .live-table thead th {
            color: #909DB7 !important;
        }

        .tab-content {
            margin-top: 15px;
            background: #202D42;
            box-shadow: 0px 2px 20px rgba(98, 157, 255, 0.36);
            border-radius: 10px;
            padding: 25px;

            .history {
                padding: 25px;
                background: #384965;
                border-radius: 10px;
            }

            .subTabs {
                display: flex;
                background: #263347;
                border-radius: 10px;
                margin-bottom: 15px;

                .tab {
                    display: flex;
                    background: transparent;
                    border-radius: 10px;
                    transition: background .3s ease, color .3s ease;
                    align-content: center;
                    justify-content: center;
                    margin-right: 15px;
                    width: 100%;
                    text-align: center;
                    padding: 15px 10px;
                    cursor: pointer;
                    color: #90A3C7;

                    &:last-child {
                        margin-right: 0;
                    }

                    &.active {
                        color: white;
                        @include themed() {
                            background: t('secondary');
                        }
                    }
                }
            }
        }

        .center-header {
            border-radius: 10px;
            display: flex;
            background: #2B3951;

            @media(max-width: 1285px) {
                flex-direction: column;

                &.mobileDropdown {
                    .tab {
                        display: flex !important;
                    }
                }
            }

            .tab {
                background: transparent;
                border-radius: 10px;
                transition: background .3s ease, color .3s ease;
                display: flex;
                flex-direction: column;
                align-content: center;
                justify-content: center;
                margin-right: 15px;
                width: 100%;
                text-align: center;
                padding: 15px 10px;
                cursor: pointer;
                color: #90A3C7;

                @media(max-width: 1285px) {
                    margin-right: 0;

                    &:not(.active) {
                        display: none;
                    }
                }

                &:last-child {
                    margin-right: 0;
                }

                i, svg {
                    font-size: 1.5em;
                    margin: auto;
                    margin-bottom: 5px;
                }

                &.active {
                    color: white;
                    @include themed() {
                        background: t('secondary');
                    }
                }
            }
        }
    }
</style>
