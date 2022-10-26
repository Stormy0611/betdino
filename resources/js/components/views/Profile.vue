<template>
    <div class="container" v-infinite-scroll="loadTabContent">
        <div class="accountCenter">
            <div class="center-header" @click="mobileDropdown = !mobileDropdown" :class="mobileDropdown ? 'mobileDropdown' : ''">
                <div class="tab" :class="tab === 'balance' ? 'active' : ''" @click="tab = 'balance'">
                    <icon icon="fal fa-wallet"></icon>
                    {{ $t('profile.tabs.balance') }}
                </div>
                <div class="tab" :class="tab === 'history' ? 'active' : ''" @click="tab = 'history'">
                    <icon icon="fal fa-history"></icon>
                    {{ $t('profile.tabs.history') }}
                </div>
                <div class="tab" :class="tab === 'invite' ? 'active' : ''" @click="tab = 'invite'">
                    <icon icon="fal fa-user"></icon>
                    {{ $t('profile.tabs.invite') }}
                </div>
                <div class="tab" :class="tab === 'responsibleGaming' ? 'active' : ''" @click="tab = 'responsibleGaming'">
                    <icon icon="fal fa-shield-check"></icon>
                    {{ $t('profile.tabs.responsible') }}
                </div>
            </div>
            <div class="tab-content">
                <div class="balance" v-if="tab === 'balance'">
                    <div class="block">
                        <div class="block-header">
                            {{ $t('profile.balance.balance') }}
                        </div>
                        <div class="balance-show">
                            R$ {{ totalBalance() }}
                        </div>
                    </div>
                    <div class="block">
                        <div class="block-header">
                            {{ $t('profile.balance.myLevel') }}
                        </div>
                        <div class="vip-info">
                            <div class="level">
                                <img :src="`/img/misc/vip/${user.user.vipLevel}.png`" alt>
                            </div>
                            <div class="level-info">
                                <div class="level-s">
                                    <div class="title">{{ $t('profile.balance.deposit') }}</div>
                                    <div class="v-progress">
                                        <div class="v-title">
                                            <template v-if="user.user.vipLevel < 10">
                                                R${{ vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) }}
                                                /
                                                R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) }}
                                            </template>
                                            <span>
                                                <template v-if="user.user.vipLevel < 10">
                                                    {{ fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) }}%
                                                </template>
                                                <template v-else>
                                                    100%
                                                </template>
                                            </span>
                                        </div>
                                        <div class="v-bar">
                                            <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="level-s">
                                    <div class="title">{{ $t('profile.balance.bet') }}</div>
                                    <div class="v-progress">
                                        <div class="v-title">
                                            <template v-if="user.user.vipLevel < 10">
                                                R${{ vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) }}
                                                /
                                                R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) }}
                                            </template>
                                            <span>
                                                <template v-if="user.user.vipLevel < 10">
                                                    {{ fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) }}%
                                                </template>
                                                <template v-else>
                                                    100%
                                                </template>
                                            </span>
                                        </div>
                                        <div class="v-bar">
                                            <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                                        </div>
                                    </div>
                                </div>
                                <router-link to="/vip">{{ $t('profile.balance.allLevels') }}</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="block">
                        <div class="block-header">
                            {{ $t('profile.balance.withdraw') }}
                        </div>
                        <template v-if="bonus">
                            <div class="w-progress">
                                <div class="w-title">
                                    <div>{{ totalBalance() }} / {{ totalBalanceBonus() }} R$</div>
                                    <div>{{ fixPercent(parseFloat(totalBalance()) / (parseFloat(totalBalance()) + parseFloat(totalBalanceBonus())) * 100) }} %</div>
                                </div>
                                <div class="w-bar">
                                    <div :style="{ width: fixPercent(parseFloat(totalBalance()) / (parseFloat(totalBalance()) + parseFloat(totalBalanceBonus())) * 100) + '%' }"></div>
                                </div>
                            </div>
                            <div class="w-stats">
                                <div class="w-block">
                                    <div class="w-amount">R$ {{ totalBalance() }}</div>
                                    <div class="w-desc">{{ $t('profile.balance.available') }}</div>
                                </div>
                                <div class="w-block">
                                    <div class="w-amount">R$ {{ totalBalanceBonus() }}</div>
                                    <div class="w-desc">{{ $t('profile.balance.bonus') }}</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-else-if="tab === 'history'" class="history">
                    <div class="subTabs">
                        <div class="tab" :class="historyTab === 'deposit' ? 'active' : ''" @click="historyTab = 'deposit'">
                            {{ $t('profile.history.tabs.deposit') }}
                        </div>
                        <div class="tab" :class="historyTab === 'withdraw' ? 'active' : ''" @click="historyTab = 'withdraw'">
                            {{ $t('profile.history.tabs.withdraw') }}
                        </div>
                        <div class="tab" :class="historyTab === 'game' ? 'active' : ''" @click="historyTab = 'game'">
                            {{ $t('profile.history.tabs.game') }}
                        </div>
                    </div>
                    <template v-if="historyTab === 'deposit'">
                        <table class="live-table" v-if="deposits.length > 0">
                            <thead>
                            <tr>
                                <th>{{ $t('profile.history.deposit') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('profile.history.sum') }}</th>
                                <th>{{ $t('profile.history.date') }}</th>
                                <th>{{ $t('profile.history.status') }}</th>
                            </tr>
                            </thead>
                            <tbody class="live_games">
                                <tr v-for="deposit in deposits">
                                    <th>
                                        <div>
                                            <div>
                                                <icon :icon="currencies[deposit.currency].icon" :style="{ color: currencies[deposit.currency].style }"></icon>
                                                {{ currencies[deposit.currency].name }}
                                            </div>
                                        </div>
                                    </th>
                                    <th class="d-none d-md-table-cell">
                                        {{ deposit.sum.$numberDecimal ? rawBitcoin(deposit.currency, parseFloat(deposit.sum.$numberDecimal)) : deposit.sum.toFixed(2) }} <icon :icon="currencies[deposit.currency].icon" :style="{ color: currencies[deposit.currency].style }"></icon>
                                    </th>
                                    <th>
                                        <div>{{ new Date(deposit.created_at).toLocaleString() }}</div>
                                    </th>
                                    <th>
                                        <div v-if="!deposit.aggregator">{{ deposit.confirmations }}/{{ currencies[deposit.currency].requiredConfirmations }} {{ $t('wallet.history.confirmations') }}</div>
                                        <div v-else>
                                            <template v-if="deposit.status === 0">{{ $t('profile.history.pending') }}</template>
                                            <template v-else-if="deposit.status === 1">{{ $t('profile.history.paid') }}</template>
                                            <template v-else-if="deposit.status === 2">{{ $t('profile.history.cancelled') }}</template>
                                        </div>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else style="text-align: center">{{ $t('profile.history.noDeposits') }}</div>
                    </template>
                    <template v-else-if="historyTab === 'withdraw'">
                        <table class="live-table" v-if="withdraws.length > 0">
                            <thead>
                                <tr>
                                    <th>
                                        {{ $t('profile.history.name') }}
                                    </th>
                                    <th class="d-none d-md-table-cell">
                                        {{ $t('profile.history.sum') }}
                                    </th>
                                    <th>
                                        {{ $t('profile.history.date') }}
                                    </th>
                                    <th>
                                        {{ $t('profile.history.status') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="live_games">
                                <tr v-for="withdraw in withdraws">
                                    <th>
                                        <div>
                                            <div><icon :icon="currencies[withdraw.currency].icon" :style="{ color: currencies[withdraw.currency].style }"></icon> {{ currencies[withdraw.currency].name }}</div>
                                            <div data-highlight>{{ withdraw.address }}</div>
                                        </div>
                                    </th>
                                    <th class="d-none d-md-table-cell">
                                        <div>
                                            {{ withdraw.sum }} <icon :icon="currencies[withdraw.currency].icon" :style="{ color: currencies[withdraw.currency].style }"></icon>
                                        </div>
                                    </th>
                                    <th>
                                        <div>
                                            {{ new Date(withdraw.created_at).toLocaleString() }}
                                        </div>
                                    </th>
                                    <th>
                                        <span v-if="withdraw.status === 0 || withdraw.status === 3">
                                            {{ $t('profile.history.withdraw.moderation') }}
                                            <div v-if="withdraw.status === 0 && !withdraw.auto" data-highlight class="clickable" @click="cancelWithdraw(withdraw._id)">{{ $t('profile.history.withdraw.cancel') }}</div>
                                        </span>
                                        <span v-else-if="withdraw.status === 1">
                                            <div class="text-success">{{ $t('profile.history.paid') }}</div>
                                        </span>
                                        <span v-else-if="withdraw.status === 2">
                                            <div class="text-danger">{{ $t('profile.history.withdraw.declined') }}</div>
                                            <div data-highlight>{{ $t('profile.history.withdraw.reason') }} - {{ withdraw.decline_reason }}</div>
                                        </span>
                                        <span v-else-if="withdraw.status === 4">{{ $t('profile.history.withdraw.cancelled') }}</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else style="text-align: center">{{ $t('profile.history.noWithdraws') }}</div>
                    </template>
                    <template v-else-if="historyTab === 'game'">
                        <table class="live-table">
                            <thead>
                            <tr>
                                <th>{{ $t('general.bets.game') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('general.bets.time') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('general.bets.bet') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('general.bets.mul') }}</th>
                                <th>{{ $t('general.bets.win') }}</th>
                            </tr>
                            </thead>
                            <tbody class="live_games">
                            <tr v-for="game in userGames">
                                <th>
                                    <div class="gameIcon">
                                        <router-link :to="'/casino/game/'+game.metadata.id" tag="div" class="icon d-none d-md-inline-block">
                                            <icon :icon="game.metadata.icon"></icon>
                                        </router-link>
                                        <div class="name">
                                            <div><router-link :to="'/casino/game/'+game.metadata.id">{{ game.metadata.name }}</router-link></div>
                                            <a href="javascript:void(0)" @click="openOverviewModal(game.game._id, game.game.game)">{{ $t('general.overview') }}</a>
                                        </div>
                                    </div>
                                </th>
                                <th class="d-none d-md-table-cell">
                                    <div>
                                        {{ new Date(game.game.created_at).toLocaleString() }}
                                    </div>
                                </th>
                                <th data-highlight class="d-none d-md-table-cell">
                                    <div>
                                        {{ rawBitcoin(game.game.currency, game.game.wager) }}
                                        <icon :icon="currencies[game.game.currency].icon" :style="{ color: currencies[game.game.currency].style }"></icon>
                                    </div>
                                </th>
                                <th data-highlight class="d-none d-md-table-cell">
                                    <div>
                                        {{ game.game.multiplier.toFixed(2) }}x
                                    </div>
                                </th>
                                <th>
                                    <div :class="game.game.status === 'win' ? 'live-win' : ''">
                                        {{ rawBitcoin(game.game.currency, game.game.profit) }}
                                        <icon :icon="currencies[game.game.currency].icon" :style="{ color: currencies[game.game.currency].style }"></icon>
                                    </div>
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </template>
                </div>
                <div v-else-if="tab === 'invite'" class="invite">
                    <div class="subTabs">
                        <div class="tab" :class="inviteTab === 'invite' ? 'active' : ''" @click="inviteTab = 'invite'">
                            {{ $t('profile.invite.tabs.invite') }}
                        </div>
                        <div class="tab" :class="inviteTab === 'invited' ? 'active' : ''" @click="inviteTab = 'invited'">
                            {{ $t('profile.invite.tabs.invited') }}
                        </div>
                        <div class="tab" :class="inviteTab === 'bonus' ? 'active' : ''" @click="inviteTab = 'bonus'">
                            {{ $t('profile.invite.tabs.bonus') }}
                        </div>
                    </div>
                    <template v-if="inviteTab === 'invite'">
                        <div class="title">{{ $t('profile.invite.title') }}</div>
                        <div class="description">{{ $t('profile.invite.description') }}</div>
                        <div class="info-i">
                            <div class="stat">
                                <div>{{ $t('promotions.invite.invited') }}</div>
                                <div>{{ affiliates.affiliates ? affiliates.affiliates.length : '...' }}</div>
                            </div>
                            <div class="stat">
                                <div>{{ $t('promotions.invite.totalBet') }}</div>
                                <div>{{ affiliates.affiliates ? 'R$' + tokenToUsd(currencies['local_brl'].price, affiliates.wageredTotal).toFixed(2) : '...' }}</div>
                            </div>
                            <div class="stat">
                                <div>{{ $t('promotions.invite.totalDeposit') }}</div>
                                <div>{{ affiliates.affiliates ? 'R$' + tokenToUsd(currencies['local_brl'].price, affiliates.depositedTotal).toFixed(2) : '...' }}</div>
                            </div>
                        </div>
                        <div class="controls">
                            <button class="btn btn-primary" @click="copy('url')">{{ $t('profile.invite.copyInvite') }}</button>
                            <div class="div"></div>
                            <button class="btn btn-secondary" @click="copy('code')">{{ $t('profile.invite.copyInviteCode') }}</button>
                        </div>
                    </template>
                    <template v-else-if="inviteTab === 'invited'">
                        <table class="table" v-if="affiliates.affiliates && affiliates.affiliates.length > 0">
                            <thead>
                                <tr>
                                    <th>{{ $t('profile.invite.name') }}</th>
                                    <th>{{ $t('profile.invite.activity') }}</th>
                                    <th>{{ $t('profile.invite.wagered') }}</th>
                                    <!--<th>{{ $t('profile.invite.deposited') }}</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr @click="openUserModal(affiliate.user._id)" v-for="affiliate in affiliates.affiliates" :key="affiliate.user._id" :style="{ cursor: 'pointer' }">
                                    <td><img alt :src="affiliate.user.avatar" style="width: 32px; height: 32px; margin-right: 5px;"> {{ affiliate.user.name }}</td>
                                    <td>{{ affiliate.done ? $t('general.yes') : $t('general.no') }}</td>
                                    <td>R${{ tokenToUsd(currencies['local_brl'].price, affiliate.wagered).toFixed(2) }}</td>
                                    <!--<td>R${{ tokenToUsd(currencies['local_brl'].price, affiliate.deposited).toFixed(2) }}</td>-->
                                </tr>
                            </tbody>
                        </table>
                        <div v-else style="text-align: center">{{ $t('profile.invite.empty') }}</div>
                    </template>
                    <template v-else-if="inviteTab === 'bonus'">
                        <table class="live-table" v-if="bonusTransactions.length > 0">
                            <thead>
                                <tr>
                                    <th>{{ $t('profile.invite.bonus.user') }}</th>
                                    <th class="d-none d-md-table-cell">{{ $t('profile.invite.bonus.type') }}</th>
                                    <th>{{ $t('profile.invite.bonus.amount') }}</th>
                                    <th>{{ $t('profile.invite.bonus.date') }}</th>
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
                        <div v-else style="text-align: center">{{ $t('profile.invite.bonus.empty') }}</div>
                    </template>
                </div>
                <div v-else-if="tab === 'responsibleGaming'" class="responsibleGaming">
                    <div class="subTabs">
                        <div class="tab" :class="rgTab === 'bettingTime' ? 'active' : ''" @click="rgTab = 'bettingTime'">
                            {{ $t('profile.responsible.tabs.bets') }}
                        </div>
                        <div class="tab" :class="rgTab === 'depositLimit' ? 'active' : ''" @click="rgTab = 'depositLimit'">
                            {{ $t('profile.responsible.tabs.deposit') }}
                        </div>
                    </div>
                    <div class="rgContent">
                        <template v-if="rgTab === 'bettingTime'">
                            <template v-if="user.user.break && +new Date() < new Date(user.user.break).getTime()">
                                <div class="description">
                                    {{ $t('profile.responsible.betBan', { time: new Date(user.user.break).toLocaleString() }) }}
                                </div>
                            </template>
                            <template v-else>
                                <div class="description">{{ $t('profile.responsible.description') }}</div>
                                <div class="break">
                                    <select v-model="breakTime">
                                        <option :value="24">24hr</option>
                                        <option :value="24 * 7">7d</option>
                                        <option :value="24 * 14">14d</option>
                                        <option :value="24 * 30">30d</option>
                                    </select>
                                    <button class="btn btn-primary" @click="sendBreak">{{ $t('profile.responsible.send') }}</button>
                                </div>
                            </template>
                        </template>
                        <template v-else-if="rgTab === 'depositLimit'">
                            <template v-if="user.user.depositLimitBreak && +new Date() < new Date(user.user.depositLimitBreak).getTime()">
                                <div class="description">
                                    {{ $t('profile.responsible.depositBan', { amount: user.user.depositLimit, time: new Date(user.user.depositLimitBreak).toLocaleString() }) }}
                                </div>
                            </template>
                            <template v-else>
                                <div class="description">{{ $t('profile.responsible.depositDescription') }}</div>
                                <div class="break">
                                    <div class="breakControls">
                                        <div class="breakControl">
                                            <div>{{ $t('profile.responsible.depositSet') }}</div>
                                            <input v-model="depositLimit" type="number" min="0">
                                        </div>
                                        <div class="breakControl">
                                            <div>{{ $t('profile.responsible.time') }}</div>
                                            <select v-model="breakTimeDeposit">
                                                <option :value="24">24hr</option>
                                                <option :value="24 * 7">7d</option>
                                                <option :value="24 * 14">14d</option>
                                                <option :value="24 * 30">30d</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary breakBtn" @click="sendBreakDeposit">{{ $t('profile.responsible.send') }}</button>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import OverviewModal from "../modals/OverviewModal";
    import UserModal from "../modals/UserModal";

    export default {
        data() {
            return {
                tab: 'balance',
                historyTab: 'deposit',
                rgTab: 'bettingTime',
                inviteTab: 'invite',
                mobileDropdown: false,

                userGames: [],
                gamesPage: 0,
                deposits: [],
                withdraws: [],
                affiliates: [],
                bonusTransactions: [],

                breakTime: 24,
                breakTimeDeposit: 24,
                depositLimit: 0
            }
        },
        computed: {
            ...mapGetters(['vip', 'user', 'currencies', 'bonus'])
        },
        watch: {
            tab() {
                this.loadTabContent(true);
            },
            historyTab() {
                this.loadTabContent(true);
            },
            rgTab() {
                this.loadTabContent(true);
            },
            inviteTab() {
                this.loadTabContent(true);
            }
        },
        created() {
            if(this.$route.params.tab) this.tab = this.$route.params.tab;
            if(this.$route.params.subTab && this.$route.params.subTabValue) {
                switch (this.$route.params.subTab) {
                    case 'history':
                        this.historyTab = this.$route.params.subTabValue;
                        break;
                    case 'responsibleGaming':
                        this.rgTab = this.$route.params.subTabValue;
                        break;
                    case 'invite':
                        this.inviteTab = this.$route.params.subTabValue;
                        break;
                }
            }
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
            sendBreakDeposit() {
                if(this.depositLimit < 0) return this.$toast.error('Enter valid value');
                axios.post('/api/breakDeposit', { hours: this.breakTimeDeposit, value: this.depositLimit }).then(({ data }) => {
                    this.$store.dispatch('update');
                }).catch(() => this.$toast.error('Error'));
            },
            sendBreak() {
                axios.post('/api/break', { hours: this.breakTime }).then(() => {
                    this.$store.dispatch('update');
                }).catch(() => this.$toast.error('Error'));
            },
            fixPercent(e) {
                return e >= 100 ? 100 : e.toFixed(2);
            },
            openOverviewModal(game_id, api_id) {
                OverviewModal.methods.open(game_id, api_id);
            },
            totalBalanceBonus() {
                let total = 0;
                this.bonus.forEach(bonus => {
                    const cr = this.currencies[Object.keys(this.currencies).filter(e => this.currencies[e].walletId === bonus.currency)];
                    total += cr.id === 'local_brl' ? bonus.value : this.tokenToUsd(this.currencies['local_brl'].price, this.tokenToUsd(cr.price, bonus.value));
                });
                return total.toFixed(2);
            },
            totalBalance() {
                let total = 0;
                Object.keys(this.currencies).forEach(key => {
                    const currency = this.currencies[key];
                    total += currency.id === 'local_brl' ? currency.balance.real : this.tokenToUsd(this.currencies['local_brl'].price, this.tokenToUsd(currency.price, currency.balance.real));
                });
                return total.toFixed(2);
            },
            cancelWithdraw(id) {
                axios.post('/api/wallet/cancel_withdraw', { id: id }).then(() => {
                    this.loadTabContent(true);
                });
            },
            loadTabContent(reset = false) {
                if(reset) {
                    this.userGames = [];
                    this.gamesPage = 0;

                    this.deposits = [];
                    this.withdraws = [];

                    this.affiliates = [];
                    this.bonusTransactions = [];
                }

                if(this.tab === 'history') {
                    if (this.historyTab === 'game') axios.get(`/api/user/games/${this.user.user._id}/${this.gamesPage}`).then(({data}) => {
                        this.gamesPage += 1;
                        this.userGames = this.userGames.concat(data);
                    });

                    if(this.deposits.length === 0) {
                        if (this.historyTab === 'deposit') axios.post('/api/wallet/history/deposits').then(({data}) => {
                            this.deposits = data;
                            this.historyLoading = false;
                        });
                    }

                    if(this.withdraws.length === 0) {
                        if (this.historyTab === 'withdraw') axios.post('/api/wallet/history/withdraws').then(({ data }) => {
                            this.withdraws = data;
                            this.historyLoading = false;
                        });
                    }
                } else if(this.tab === 'invite') {
                    if(this.inviteTab === 'invite' || this.inviteTab === 'invited') {
                        axios.post('/api/user/affiliates').then(({ data }) => this.affiliates = data);
                    } else {
                        axios.post('/api/user/bonusTransactions').then(({ data }) => this.bonusTransactions = data);
                    }
                }
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/themes";

    .accountCenter {
        margin-bottom: 35px;

        .breakBtn {
            margin-top: 21px;
        }

        .breakControls {
            display: flex;

            input {
                background: #263347;
                border-radius: 10px;
                padding: 10px 15px;
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .breakControl:first-child {
                    margin-bottom: 15px;
                    margin-right: 0 !important;
                }
            }

            .breakControl {
                div {
                    font-weight: 700;
                    margin-bottom: 5px;
                }

                &:first-child {
                    margin-right: 15px;
                }
            }
        }

        .invite {
            .table {
                color: white;

                th, td {
                    border: unset !important;
                }
            }

            .title {
                text-align: center;
                font-size: 1.2em;
                font-weight: 600;
                margin-top: 35px;
                margin-bottom: 25px;
            }

            .description {
                text-align: center;
                font-size: 1em;
                width: 90%;
                margin: auto;
                margin-bottom: 35px;
            }

            .info-i {
                display: flex;
                justify-content: center;

                @media(max-width: 991px) {
                    flex-direction: column;
                    text-align: center;
                }

                .stat {
                    display: flex;
                    margin-right: 15px;
                    border-right: 1px solid white;
                    padding-right: 15px;
                    align-items: center;

                    @media(max-width: 991px) {
                        border-right: unset;
                        margin-right: unset;
                    }

                    &:last-child {
                        border-right: 0;
                        margin-right: 0;
                    }

                    div:first-child {
                        color: #A4B7DB;
                        margin-right: 5px;
                    }
                }
            }

            .controls {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 25px;

                @media(max-width: 991px) {
                    flex-direction: column;

                    .div {
                        height: 2px !important;
                        margin: unset;
                        margin-top: 15px;
                        background: unset;
                    }
                }

                .div {
                    width: 1px;
                    height: 31px;
                    background: white;
                    margin-left: 15px;
                    margin-right: 15px;
                    opacity: .5;
                }

                .btn {
                    color: white !important;
                    padding: 15px 20px;
                    border-radius: 10px;

                    &:last-child {
                        background: #1C283C !important;
                    }
                }
            }
        }

        .rgContent {
            .description {
                text-align: center;
                margin-top: 35px;
                margin-bottom: 35px;
                font-weight: 600;
            }

            .break {
                display: flex;
                align-items: center;
                justify-content: center;

                @media(max-width: 991px) {
                    flex-direction: column;
                }

                select {
                    margin-right: 15px;
                    background: #263347;
                    width: 220px;
                    padding: 10px 15px;
                    border-radius: 10px;
                }

                .btn {
                    color: white !important;
                    padding: 10px 20px;
                    border-radius: 8px;

                    @media(max-width: 991px) {
                        margin-top: 15px;
                    }
                }
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

            .history, .responsibleGaming, .invite {
                padding: 25px;
                background: #384965;
                border-radius: 10px;
            }

            .subTabs {
                display: flex;
                background: #263347;
                border-radius: 10px;
                margin-bottom: 15px;

                @media(max-width: 991px) {
                    flex-direction: column;
                }

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

            .balance {
                display: flex;

                @media(max-width: 1285px) {
                    flex-direction: column;

                    .block {
                        margin-right: 0;
                        margin-bottom: 25px;

                        &:last-child {
                            margin-bottom: 0;
                        }
                    }
                }

                .vip-info {
                    display: flex;

                    .level {
                        margin-right: 20px;
                    }

                    .level-info {
                        width: 100%;

                        a {
                            text-align: center;
                            font-weight: 500;
                            @include themed() {
                                color: t('secondary');
                            }
                        }

                        .level-s {
                            background: #263347;
                            border-radius: 10px;
                            margin-bottom: 15px;
                            width: 100%;
                            padding: 10px;

                            .title {
                                font-size: 1.3em;
                                font-weight: 600;
                                text-align: center;
                                margin-bottom: 10px;
                            }

                            .v-progress {
                                .v-title {
                                    display: flex;
                                    color: #90A3C7;
                                    align-items: center;
                                    margin-bottom: 5px;

                                    span {
                                        margin-left: auto;
                                    }
                                }

                                .v-bar {
                                    height: 10px;
                                    width: 100%;
                                    background: #687C9C;
                                    border-radius: 10px;

                                    div {
                                        height: 100%;
                                        border-radius: inherit;

                                        @include themed() {
                                            background: t('secondary');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                .block {
                    width: 100%;
                    margin-right: 25px;
                    background: #384965;
                    border-radius: 10px;
                    padding: 25px;

                    .w-stats {
                        display: flex;
                        margin-top: 25px;
                        width: 100%;

                        @media(max-width: 991px) {
                            flex-direction: column;

                            .w-block {
                                margin-right: 0 !important;
                                margin-bottom: 15px;

                                &:last-child {
                                    margin-bottom: 0;
                                }
                            }
                        }

                        .w-block {
                            width: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            flex-direction: column;
                            height: 120px;
                            border-radius: 10px;
                            background: #263347;

                            &:first-child {
                                margin-right: 15px;

                                .w-amount {
                                    color: #2FF42C;
                                }
                            }

                            .w-desc {
                                font-size: 1.1em;
                            }

                            &:last-child {
                                .w-amount {
                                    color: #A2B5D3;
                                }
                            }

                            .w-amount {
                                font-weight: 600;
                                font-size: 1.3em;
                            }
                        }
                    }

                    .w-progress {
                        .w-title {
                            display: flex;
                            color: #90A3C7;
                            align-items: center;
                            margin-bottom: 5px;
                            font-weight: 600;

                            div:last-child {
                                margin-left: auto;
                                color: white;
                            }
                        }

                        .w-bar {
                            height: 10px;
                            width: 100%;
                            background: #687C9C;
                            border-radius: 10px;

                            div {
                                height: 100%;
                                border-radius: inherit;

                                @include themed() {
                                    background: t('secondary');
                                }
                            }
                        }
                    }

                    .block-header {
                        background: #263347;
                        border-radius: 10px;
                        font-size: 1.2em;
                        text-align: center;
                        padding: 15px;
                        font-weight: 600;
                        margin-bottom: 25px;
                    }

                    .balance-show {
                        background: #263347;
                        border-radius: 10px;
                        padding: 20px 25px;
                        text-align: center;
                        font-size: 1.3em;
                        font-weight: 600;
                    }

                    &:last-child {
                        margin-right: 0;
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
