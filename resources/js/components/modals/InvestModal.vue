<script>
    import { mapGetters } from 'vuex';
    import Bus from '../../bus';

    export default {
        methods: {
            open() {
                Bus.$emit('modal:new', {
                    name: 'invest',
                    title: 'general.head.invest',
                    component: {
                        data() {
                            return {
                                tab: 'overview',
                                stats: null,
                                history: null,
                                money: {
                                    decimal: '.',
                                    thousands: '',
                                    prefix: '',
                                    suffix: '',
                                    precision: 8,
                                    masked: false
                                },

                                amount: 0
                            }
                        },
                        computed: {
                            ...mapGetters(['currency', 'currencies'])
                        },
                        watch: {
                            tab() {
                                this.load();
                            }
                        },
                        methods: {
                            load() {
                                if(this.tab === 'overview') {
                                    this.stats = null;
                                    axios.post('/api/investment/stats', { currency: this.currencies[this.currency].walletId }).then(({ data }) => this.stats = data);
                                }
                                if(this.tab === 'history') {
                                    this.history = null;
                                    axios.post('/api/investment/history').then(({ data }) => this.history = data);
                                }
                            },
                            invest() {
                                axios.post('/api/invest', { amount: this.amount }).then(() => this.tab = 'history').catch(() => this.$toast.error(this.$i18n.t('general.chat_commands.modal.tip.invalid_amount')));
                            },
                            disinvest(id) {
                                axios.post('/api/disinvest', { id: id }).then(this.load, this.load);
                            }
                        },
                        created() {
                            this.load();
                        },
                        template: `
                                <div>
                                    <div class="modal-tabs">
                                        <div :class="'modal-tab ' + (tab === 'overview' ? 'active' : '')" @click="tab = 'overview'">
                                            {{ $t('invest.tabs.overview') }}
                                        </div>
                                        <div :class="'modal-tab ' + (tab === 'invest' ? 'active' : '')" @click="tab = 'invest'">
                                            {{ $t('invest.tabs.invest') }}
                                        </div>
                                        <div :class="'modal-tab ' + (tab === 'history' ? 'active' : '')" @click="tab = 'history'">
                                            {{ $t('invest.tabs.history') }}
                                        </div>
                                    </div>
                                    <div v-if="tab === 'invest'">
                                        <div class="warn" v-html="$t('invest.invest_fee')"></div>
                                        <div class="warn" v-html="$t('invest.invest_fee_withdraw', { value: currencies[currency].invest_commission })"></div>
                                        <div class="mt-2 investBalance">{{ $t('invest.your_balance', { balance: currencies[currency].balance.real }) }}</div>
                                        <div class="investBalance">{{ $t('invest.min', { min: rawBitcoin(currency, currencies[currency].investMin) }) }}</div>
                                        <input v-money="money" v-model="amount" class="mt-2">
                                        <button class="btn btn-primary btn-block mt-2" @click="invest">{{ $t('invest.invest') }}</button>
                                    </div>
                                    <div v-if="tab === 'history' && history !== null">
                                        <table class="live-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {{ $t('invest.history.amount') }}
                                                    </th>
                                                    <th>
                                                        {{ $t('invest.history.your_share') }}
                                                    </th>
                                                    <th>
                                                        {{ $t('invest.history.profit') }}
                                                    </th>
                                                    <th>
                                                        {{ $t('invest.history.status') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="live_games">
                                                <tr v-for="e in history">
                                                    <th>
                                                        <div>
                                                            <unit :to="e.currency" :value="e.amount"></unit>
                                                            <icon :icon="currencies[e.currency].icon" :style="{ color: currencies[e.currency].style }"></icon>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div>
                                                            {{ e.share.toFixed(2) }}%
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div>
                                                            <span :class="'text-'+(e.profit > e.amount ? 'success' : (e.profit === e.amount ? '' : 'danger'))">
                                                                <unit :to="e.currency" :value="e.profit"></unit>
                                                                <icon :icon="currencies[e.currency].icon" :style="{ color: currencies[e.currency].style }"></icon>
                                                            </span>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div>
                                                            <span v-if="e.status === 1">{{ $t('invest.history.cancelled') }}</span>
                                                            <a href="javascript:void(0)" v-if="e.profit > 0 && e.status !== 1" @click="disinvest(e.id)">{{ $t('invest.history.disinvest') }}</a>
                                                            <span v-else>{{ $t('invest.history.dead') }}</span>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <loader v-if="!stats"></loader>

                                    <div v-if="tab === 'overview' && stats !== null">
                                        <div class="shares">
                                            <div class="your_shares">{{ stats.your_bankroll_share.toFixed(2) }}%</div>
                                            <div class="your_shares_desc">{{ $t('invest.sidebar.your_share') }}</div>
                                        </div>
                                        <div class="divider">
                                            <div class="line"></div>
                                            <i class="fal fa-angle-down"></i>
                                            <div class="line"></div>
                                        </div>
                                        <div class="stats">
                                            <div class="stat">
                                                <div>{{ $t('invest.sidebar.your_bankroll') }}</div>
                                                <div>
                                                  <unit :to="currency" :value="stats.your_bankroll"></unit>
                                                  <icon :icon="currencies[currency].icon" :style="{ color: currencies[currency].style }"></icon>
                                                </div>
                                            </div>
                                            <div class="stat">
                                                <div>{{ $t('invest.sidebar.site_bankroll') }}</div>
                                                <div>
                                                  <unit :to="currency" :value="stats.site_bankroll"></unit>
                                                  <icon :icon="currencies[currency].icon" :style="{ color: currencies[currency].style }"></icon>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stats">
                                            <div class="stat">
                                                <div>{{ $t('invest.sidebar.your_investing_profit') }}</div>
                                                <div>
                                                  <unit :to="currency" :value="stats.investment_profit"></unit>
                                                  <icon :icon="currencies[currency].icon" :style="{ color: currencies[currency].style }"></icon>
                                                </div>
                                            </div>
                                            <div class="stat">
                                                <div>{{ $t('invest.sidebar.site_profit') }}</div>
                                                <div>
                                                    <unit :to="currency" :value="stats.site_profit"></unit>
                                                    <icon :icon="currencies[currency].icon" :style="{ color: currencies[currency].style }"></icon>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.invest {
        max-width: 550px;

        .loaderContainer {
            margin-bottom: 20px;
        }

        .shares {
            text-align: center;
            margin-bottom: 35px;

            .your_shares {
                font-size: 3em;
            }

            .your_shares_desc {
                font-size: 1.35em;
            }
        }

        .divider {
            margin-bottom: 20px;
        }

        @include themed() {
            .warn {
                font-size: 0.9em;

                span {
                    color: t('secondary');
                }
            }

            .investBalance {
                font-size: 0.95em;
            }

            .btn {
                text-transform: uppercase;
                font-weight: 600;
            }

            .stats {
                display: flex;
                flex-direction: row;
                margin-bottom: 20px;

                &:last-child {
                    margin-bottom: 0;
                }

                .stat {
                    display: flex;
                    flex-direction: column;
                    background: lighten(t('input'), 2.5%);
                    width: 50%;
                    padding: 10px 15px;
                    font-size: 0.95em;

                    div {
                        &:first-child {
                            color: t('link');
                        }
                    }

                    &:first-child {
                        border-top-left-radius: 3px;
                        border-bottom-left-radius: 3px;
                    }

                    &:last-child {
                        border-top-right-radius: 3px;
                        border-bottom-right-radius: 3px;
                    }
                }
            }
        }
    }
</style>
