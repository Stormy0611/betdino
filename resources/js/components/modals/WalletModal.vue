<script>
    import Bus from '../../bus';
    const qr = require('qrcode');
    import { mapGetters } from 'vuex';

    export default {
        methods: {
            open(tab = null) {
                Bus.$emit('modal:new', {
                    name: 'user_wallet',
                    component: {
                        data() {
                           return {
                             tab: !tab ? 'deposit' : tab,
                             promocode: '',

                             depositWallet: null,
                             depositAmount: 100,
                             withdrawWallet: '',
                             withdrawAmount: 0,

                             disable: false,

                             aggregators: null,
                             aggregator: null,

                             whatsApp: '',
                             pixType: 'CPF',
                             cpf: '',
                             chavePix: ''
                           }
                        },
                        watch: {
                            tab() {
                                this.loadTab();
                            },
                            currency() {
                                this.loadTab();
                            }
                        },
                        created() {
                            this.loadTab();

                            axios.post('/api/aggregator/all').then(({ data }) => {
                                this.aggregators = data;
                                let v = this.aggregators.filter(e => e.supports.includes(this.currency))[0];
                                this.aggregator = v ? v.id : null;
                            });
                        },
                        computed: {
                            ...mapGetters(['user', 'vip', 'currency', 'currencies'])
                        },
                        methods: {
                            loadTab() {
                                if(this.tab === 'deposit') {
                                    if(this.currency.startsWith('local_')) return;

                                    const canvas = document.createElement('canvas');
                                    this.depositWallet = null;
                                    let e = document.querySelector('#qr canvas');
                                    if(e) e.remove();

                                    axios.post('/api/wallet/getDepositWallet', { currency: this.currency }).then(({data}) => {
                                        if (data.currency !== this.currency) return;
                                        this.depositWallet = data.wallet;

                                        setTimeout(() => {
                                            let e = document.querySelector('#qr canvas');
                                            if(e) e.remove();
                                            qr.toCanvas(canvas, data.wallet);
                                            document.querySelector('#qr').append(canvas);
                                        });
                                    });
                                }
                            },
                            enterPromocode() {
                                axios.post('/api/promocode/activate', { code: this.promocode }).then(() => {
                                    this.promocode = '';
                                    this.$toast.success(this.$i18n.t('bonus.promo.success'));
                                }).catch((code) => {
                                    if(code.response.data.code === 1) this.$toast.error(this.$i18n.t('bonus.promo.invalid'));
                                    if(code.response.data.code === 2) this.$toast.error(this.$i18n.t('bonus.promo.expired_time'));
                                    if(code.response.data.code === 3) this.$toast.error(this.$i18n.t('bonus.promo.expired_usages'));
                                    if(code.response.data.code === 4) this.$toast.error(this.$i18n.t('bonus.promo.used'));
                                    if(code.response.data.code === 5) this.$toast.error(this.$i18n.t('general.error.promo_limit'));
                                    if(code.response.data.code === 7) this.$toast.error(this.$i18n.t('general.error.vip_only_promocode'));
                                });
                            },
                            copy(text) {
                                navigator.clipboard.writeText(text);
                                this.$toast.success('Copied!');
                            },
                            accountCenter() {
                                Bus.$emit('modal:close');
                                this.$router.push('/profile/history/history/deposit');
                            },
                            depositLocal() {
                                if(this.user.user.depositLimitBreak && new Date(this.user.user.depositLimitBreak).getTime() > +new Date() && (parseFloat(this.depositAmount) > this.user.user.depositLimit || this.user.user.depositLimitValue >= this.user.user.depositLimit))
                                    return this.$toast.error('Your deposit limit: R$ ' + this.user.user.depositLimit + ', active until ' + new Date(this.user.user.depositLimitBreak).toLocaleString());

                                if(this.disable) return;
                                this.disable = true;

                                axios.post('/api/aggregator/invoice', {
                                    sum: parseFloat(this.depositAmount),
                                    id: this.aggregator,
                                    currency: this.currency
                                }).then(({ data }) => {
                                    window.location.href = data;
                                    this.disable = false;
                                }).catch(() => {
                                    this.disable = false;
                                    this.$toast.error('Failed to create payment. Try again later.');
                                });
                            },
                            promoRedirect() {
                                Bus.$emit('modal:close');
                                this.$router.push('/bonus-50');
                            },
                            isNumber($event) {
                                const allowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                                if(!allowed.includes($event.key)) $event.preventDefault();
                            },
                            withdraw() {
                                if(this.disable) return;
                                if(this.withdrawWallet.length < 4) {
                                    this.$toast.error(this.$i18n.t('general.error.enter_wallet'));
                                    return;
                                }

                                this.disable = true;
                                axios.post('/api/wallet/withdraw', {
                                    sum: parseFloat(this.withdrawAmount),
                                    currency: this.currency,
                                    wallet: this.withdrawWallet,
                                    type: null,
                                    chavePix: this.chavePix,
                                    cpf: this.cpf,
                                    pixType: this.pixType,
                                    whatsApp: this.whatsApp
                                }).then(({ data }) => {
                                    this.disable = false;
                                    Bus.$emit('modal:close');
                                    this.$router.push('/profile/history/history/withdraw');
                                }).catch((error) => {
                                    switch (error.response.data.code) {
                                        case 1: this.$toast.error(this.$i18n.t('general.error.invalid_withdraw')); break;
                                        case 2: this.$toast.error(this.$i18n.t('general.error.invalid_wager')); break;
                                        case 3: this.$toast.error(this.$i18n.t('general.error.only_one_withdraw')); break;
                                    }
                                    this.disable = false;
                                });
                            }
                        },
                        template: `
                          <div class="wallet-modal">
                            <div class="wallet-header">
                                {{ $t('walletModal.title') }}
                            </div>
                            <div class="wallet-content">
                                <div class="wallet-tabs">
                                  <div class="tab" :class="tab === 'deposit' ? 'active' : ''" @click="tab = 'deposit'">{{ $t('walletModal.tabs.deposit') }}</div>
                                  <div class="tab" :class="tab === 'withdraw' ? 'active' : ''" @click="tab = 'withdraw'">{{ $t('walletModal.tabs.withdraw') }}</div>
<!--                                  <div class="tab" :class="tab === 'promocode' ? 'active' : ''" @click="tab = 'promocode'">{{ $t('walletModal.tabs.promocode') }}</div> -->
                                </div>
                                <template v-if="tab === 'deposit'">
                                  <div class="deposit-currencies">
                                    <div v-for="c in currencies" class="currency" :key="c.name" @click="$store.dispatch('setCurrency', c.id); aggregator = aggregators.filter(e => e.supports.includes(currency))[0].id" :class="currency === c.id ? 'active' : ''">
                                      {{ c.name }}
                                    </div>
                                  </div>
                                  <div class="deposit-currencies" v-if="aggregators && currency.startsWith('local_')">
                                    <div v-for="c in aggregators.filter(e => e.supports.includes(currency))" class="currency" :key="c.name" @click="aggregator = c.id" :class="aggregator === c.id ? 'active' : ''">
                                      {{ c.name }}
                                    </div>
                                  </div>
                                  <template v-if="!currency.startsWith('local_')">
                                      <div class="deposit-address">
                                        <div class="text">{{ $t('walletModal.address') }}</div>
                                        <div class="v">
                                            <input placeholder="Address" readonly :value="!depositWallet ? $t('walletModal.loading') : depositWallet">
                                            <button class="btn btn-primary" @click="depositWallet ? copy(depositWallet) : false" :disabled="!depositWallet">{{ $t('walletModal.copy') }}</button>
                                        </div>
                                      </div>
                                      <div class="qr" v-if="depositWallet">
                                        <div id="qr"></div>
                                      </div>
                                  </template>
                                  <template v-else-if="aggregators">
                                      <div class="deposit-address">
                                        <div class="text">{{ $t('walletModal.amount') }}</div>
                                        <div class="v">
                                          <icon :icon="currencies[currency].icon"></icon>
                                          <input class="padding" placeholder="Amount" v-model="depositAmount" @keypress="isNumber">
                                          <button class="btn btn-primary" @click="depositLocal" :disabled="isNaN(parseFloat(depositAmount)) || parseFloat(depositAmount) > 4999 || parseFloat(depositAmount) < 10 || disable">
                                            {{ disable ? $t('walletModal.creating') : $t('walletModal.deposit') }}
                                          </button>
                                        </div>
                                      </div>
                                      <div class="quick">
                                          <div class="option" @click="depositAmount = 20">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            20
                                          </div>
                                          <div class="option" @click="depositAmount = 100">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            100
                                          </div>
                                          <div class="option" @click="depositAmount = 500">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            500
                                          </div>
                                          <div class="option" @click="depositAmount = 1000">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            1000
                                          </div>
                                          <div class="option" @click="depositAmount = 2000">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            2000
                                          </div>
                                          <div class="option" @click="depositAmount = 4999">
                                            <icon :icon="currencies[currency].icon"></icon>
                                            4999
                                          </div>
                                      </div>
                                      <div class="promo" @click="promoRedirect" v-if="!user.user.bonus_50_deposit" v-html="$t('walletModal.promotion')"></div>
                                      <div class="notes">
                                        <div class="title">{{ $t('walletModal.depositNotes.title') }}</div>
                                        <ul>
                                          <li>{{ $t('walletModal.depositNotes.1') }}</li>
                                          <li>{{ $t('walletModal.depositNotes.2') }}</li>
                                          <li>{{ $t('walletModal.depositNotes.3') }}</li>
                                          <li>{{ $t('walletModal.depositNotes.4') }}</li>
                                        </ul>
                                      </div>
                                  </template>
                                  <template v-else>{{ $t('walletModal.loading') }}</template>
                                  <div class="status" v-if="depositWallet"><a href="javascript:void(0)" @click="accountCenter">{{ $t('walletModal.status') }}</a></div>
                                </template>
                                <template v-if="tab === 'withdraw'">
                                  <div class="deposit-currencies">
                                      <div v-for="c in currencies" class="currency" :key="c.name" @click="$store.dispatch('setCurrency', c.id)" :class="currency === c.id ? 'active' : ''">
                                        {{ c.name }}
                                      </div>
                                      <template v-if="currency.startsWith('local_')">
                                        <div class="withdraw-address">
                                          <div class="text">{{ $t('walletModal.withdrawAmount') }}</div>
                                          <div class="v">
                                              <input :placeholder="$t('walletModal.withdrawAmount')" v-model="withdrawAmount" type="number">
                                          </div>
                                        </div>
                                        <div class="withdraw-address">
                                          <div class="text">{{ $t('walletModal.cardholder') }}</div>
                                          <div class="v">
                                            <input :placeholder="$t('walletModal.cardholder')" v-model="withdrawWallet">
                                          </div>
                                        </div>
                                        <div class="info-row">
                                          <div class="withdraw-address">
                                            <div class="text">CPF</div>
                                            <div class="v">
                                              <input placeholder="CPF" v-model="cpf">
                                            </div>
                                          </div>
                                          <div class="withdraw-address">
                                            <div class="text">WhatsApp</div>
                                            <div class="v">
                                              <input placeholder="WhatsApp" v-model="whatsApp">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="info-row">
                                          <div class="withdraw-address">
                                            <div class="text">{{ $t('walletModal.pixType') }}</div>
                                            <div class="v">
                                              <input :placeholder="$t('walletModal.pixType')" disabled v-model="pixType">
                                            </div>
                                          </div>
                                          <div class="withdraw-address">
                                            <div class="text">{{ $t('walletModal.chavePix') }}</div>
                                            <div class="v">
                                              <input :placeholder="$t('walletModal.chavePix')" v-model="chavePix">
                                            </div>
                                          </div>
                                        </div>
                                      </template>
                                      <template v-else>
                                        <div class="withdraw-address">
                                          <div class="text">{{ $t('walletModal.withdrawAddress') }}</div>
                                          <div class="v">
                                              <input :placeholder="$t('walletModal.withdrawAddress')" v-model="withdrawWallet">
                                          </div>
                                        </div>
                                        <div class="withdraw-address">
                                          <div class="text">{{ $t('walletModal.withdrawAmount') }}</div>
                                          <div class="v">
                                              <input :placeholder="$t('walletModal.withdrawAmount')" v-model="withdrawAmount" type="number">
                                          </div>
                                        </div>
                                      </template>
                                      <div class="notes">
                                        <div class="title">{{ $t('walletModal.withdrawNotes.title') }}</div>
                                        <ul>
                                          <li>{{ $t('walletModal.withdrawNotes.1', { amount: vip.filter(e => e.level === user.user.vipLevel)[0].numberOfWithdrawals }) }}</li>
                                          <li>{{ $t('walletModal.withdrawNotes.2', { fee: vip.filter(e => e.level === user.user.vipLevel)[0].withdrawFee.toFixed(2) }) }}</li>
                                          <li>
                                            {{ $t('walletModal.withdrawNotes.3') }} {{ currencies[currency].name }}
                                            {{ currency === 'local_brl' ? vip.filter(e => e.level === user.user.vipLevel)[0].monthlyFreeWithdrawalAmount
                                              : usdToToken(currencies[currency].price, usdToToken(currencies['local_brl'].price, vip.filter(e => e.level === user.user.vipLevel)[0].monthlyFreeWithdrawalAmount)).toFixed(8) }}
                                          </li>
                                          <li>{{ $t('walletModal.withdrawNotes.4', { min: currencies[currency].name + ' ' + currencies[currency].minWithdraw }) }}</li>
                                          <li>{{ $t('walletModal.withdrawNotes.5', { max: currencies[currency].name + ' ' + vip.filter(e => e.level === user.user.vipLevel)[0].maxWithdrawal.toFixed(currency.includes('local') ? 2 : 8) }) }}</li>
                                          <li>{{ $t('walletModal.withdrawNotes.6') }}</li>
                                          <li>{{ $t('walletModal.withdrawNotes.7') }}</li>
                                        </ul>
                                      </div>
                                      <button class="btn btn-primary btn-block" @click="withdraw" :disabled="disable">{{ $t('walletModal.withdraw') }}</button>
                                  </div>
                                </template>
                                <template v-if="tab === 'promocode'">
                                  <input :placeholder="$t('walletModal.tabs.promocode')" v-model="promocode">
                                  <button class="btn btn-primary btn-block" @click="enterPromocode">{{ $t('walletModal.enter') }}</button>
                                </template>
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

    .xmodal.user_wallet {
        max-width: 460px;
        border-radius: 15px !important;

        .info-row {
            display: flex;

            .withdraw-address:first-child {
                margin-right: 10px;
            }

            input[disabled] {
                background: #405272 !important;
            }
        }

        input {
            border: 2px solid #405272 !important;
        }

        .quick {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;

            .option {
                border-radius: 100px;
                border: 2px solid #405272;
                padding: 5px 15px;
                margin: 3px;
                display: flex;
                align-items: center;
                background: transparent;
                cursor: pointer;
                transition: background .3s ease;
                margin-bottom: 10px;

                &:hover {
                    background: #405272;
                }

                i, svg {
                    margin-right: 10px;
                }
            }
        }

        .promo {
            background: linear-gradient(90.6deg, #43BB41 0.07%, rgba(48, 66, 95, 0.87) 99.61%);
            border-radius: 10px;
            padding: 10px 15px;
            font-weight: 600;
            display: flex;
            margin-bottom: 15px;
            margin-top: 10px;
            cursor: pointer;
            align-items: center;

            .sep {
                margin-left: 10px;
                margin-right: 12px;
            }

            img {
                margin-right: 10px;
            }

            span {
                margin-left: 5px;
                color: #00FF0F;
            }
        }

        .notes {
            margin-top: 10px;

            .title {
                font-size: 1.1em;
                font-weight: 600;
                margin-bottom: 10px;
            }

            ul {
                padding-left: 30px;
                color: #90A3C7;
                font-size: 0.8em;
            }
        }

        .withdraw-address {
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 5px;
            width: 100%;

            .v {
                display: flex;
                width: 100%;

                input {
                    width: 100%;
                    margin-top: 10px;
                }
            }
        }

        .deposit-address {
            .text {
                font-weight: 600;
                margin-bottom: 5px;
                margin-top: 15px;
            }

            .v {
                display: flex;
                position: relative;

                i, svg {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    left: 20px;
                    margin-top: -1px;
                }

                input {
                    width: 100%;
                    margin-right: 15px;

                    &.padding {
                        padding-left: 40px;
                    }
                }

                .btn {
                    margin: unset !important;
                    width: 25%;
                    justify-content: center;
                    display: flex;
                }
            }
        }

        .qr {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 25px;
            background: white;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 15px;

            #qr {
                display: flex;
            }
        }

        .status {
            font-size: .9em;
            text-align: center;
        }

        .deposit-currencies {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;

            .currency {
                margin-right: 10px;
                cursor: pointer;
                transition: background .3s ease, color .3s ease;
                background: #324058;
                border-radius: 6px;
                color: #35FA32;
                font-weight: 600;
                padding: 5px 15px;

                &:last-child {
                    margin-right: 0;
                }

                &.active {
                    color: #324058;
                    background: #35FA32;
                }
            }
        }

        .btn {
            margin-top: 15px;
            color: white !important;
            padding: 15px 50px;
            border-radius: 15px !important;
            filter: drop-shadow(0px 2px 8px rgba(255, 255, 255, 0.15));
        }

        .modal_content {
            padding: 0 !important;
            border-radius: 15px !important;

            .wallet-header {
                background: #33435f;
                font-weight: 600;
                font-size: 1.2em;
                padding: 15px 25px;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
            }

            .wallet-content {
                background: #253248;
                padding: 15px;

                .wallet-tabs {
                    background: #324058;
                    display: flex;
                    border-radius: 10px;
                    margin-bottom: 15px;

                    .tab {
                        padding: 15px 20px;
                        background: transparent;
                        transition: background .3s ease, color .3s ease;
                        color: #90A3C7;
                        width: 100%;
                        border-radius: 10px;
                        cursor: pointer;
                        font-size: .9em;
                        text-align: center;

                        &.active {
                            background: #43BB41;
                            color: white;
                        }
                    }
                }
            }
        }
    }
</style>
