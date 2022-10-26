<template>
    <div class="container-fluid" v-if="info">
        <div class="row page-title">
            <div class="col-md-12">
                <h4 class="mb-1 mt-0">{{ info.user.name }}</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mt-3">
                            <img :src="info.user.avatar" alt="" class="avatar-lg rounded-circle">
                            <h5 class="mt-2 mb-0">{{ info.user.name }}</h5>
                            <template v-if="info.user.name_history.length > 1">
                                <h6 class="font-weight-normal mt-2 mb-0">Also known as:</h6>
                                <h6 class="text-muted font-weight-normal" v-for="history in info.user.name_history">
                                    {{ new Date(history['time']).toLocaleString() }} - {{ history['name'] }}
                                </h6>
                            </template>

                            <button type="button" :class="`btn ${ info.user.ban ? 'btn-primary' : 'btn-danger' } btn-sm mr-1 mt-1`" @click="ban(info.user._id)"
                                   v-if="$checkPermission('users', 'delete')">
                                {{ info.user.ban ? 'Unban' : 'Ban' }}
                            </button>

                            <button type="button" :class="`btn btn-primary btn-sm mr-1 mt-1`" @click="unbanBreak(info.user._id)"
                                    v-if="$checkPermission('users', 'edit') && info.user.break && +new Date() < new Date(info.user.break).getTime()">
                                Unban (Bets)
                            </button>

                            <button type="button" :class="`btn btn-primary btn-sm mr-1 mt-1`" @click="unbanDepositBreak(info.user._id)"
                                    v-if="$checkPermission('users', 'edit') && info.user.depositLimitBreak && +new Date() < new Date(info.user.depositLimitBreak).getTime()">
                                Unban (Deposits)
                            </button>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <h4 class="mb-3 font-size-15">Info</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0 text-muted">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Games</th>
                                            <td>{{ userAdvanced ? userAdvanced.games : '...' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Register IP</th>
                                            <td>{{ info.user.register_ip }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Latest login IP</th>
                                            <td>{{ info.user.login_ip }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Registered</th>
                                            <th>{{ new Date(info.user.created_at).toLocaleString() }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Last login date</th>
                                            <th>{{ new Date(info.user.login_date).toLocaleString() }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Last activity</th>
                                            <th>{{ new Date(info.user.latest_activity).toLocaleString() }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Referrer</th>
                                            <th v-html="!info.user.referral ? '-' : '<a href=\'/admin/user/'+info.user.referral+'\'></a>'">View profile</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">2FA status</th>
                                            <th>{{ info.user.tfa_enabled ? 'Enabled' : 'Disabled' }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="row">Accounts</th>
                                            <th>
                                                <multiaccounts :id="info.user._id"></multiaccounts>
                                            </th>
                                        </tr>
                                        <tr v-if="$checkPermission('users', 'edit')">
                                            <th scope="row">Change password</th>
                                            <th><button class="btn btn-danger mr-2 mb-1" @click="changePassword">Change password</button><button class="btn btn-danger mb-1" @click="setVIPLevel">Set VIP level</button></th>
                                        </tr>
                                        <tr v-if="$checkPermission('*') && roles">
                                            <th scope="row">Roles</th>
                                            <th>
                                                <div class="mb-1">Edit user roles:</div>
                                                <select @change="$event.target.value === '-' ? false : toggleRole($event.target.value)">
                                                    <option value="-" disabled selected></option>
                                                    <option :value="role.id" v-for="role in roles">{{ info.user.roles.filter(e => e.id === role.id).length === 0 ? 'Add' : 'Remove' }} - {{ role.name }}</option>
                                                </select>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <table class="table dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Currency</th>
                                <th>Games</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Wagered</th>
                                <!--<th>Deposited</th>-->
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Total</td>
                                <td>{{ userAdvanced ? userAdvanced.games : '...' }}</td>
                                <td>{{ userAdvanced ? userAdvanced.wins : '...' }}</td>
                                <td>{{ userAdvanced ? userAdvanced.losses : '...' }}</td>
                            </tr>

                            <tr v-for="currency in currencies" v-if="currency.name">
                                <td>
                                    <div>{{ currency.name }}</div>
                                    <div>{{ info.user['wallet_' + currency.id] }}</div>
                                </td>
                                <td>{{ userAdvanced ? userAdvanced.currencies[currency.id].games : '...' }}</td>
                                <td>{{ userAdvanced ? userAdvanced.currencies[currency.id].wins : '...' }}</td>
                                <td>{{ userAdvanced ? userAdvanced.currencies[currency.id].losses : '...' }}</td>
                                <td>{{ userAdvanced ? (userAdvanced.currencies[currency.id].wagered.toFixed(currency.id.startsWith('local_') ? 2 : 8)) : '...' }} {{ currency.name }}</td>
                                <!--<td>{{ info.currencies[currency.id].deposited ? info.currencies[currency.id].deposited.toFixed(currency.id.startsWith('local_') ? 2 : 8) : '-' }} {{ currency.name }}</td>-->
                                <td><input v-if="$checkPermission('users', 'edit')" class="form-control form-control-sm" :placeholder="currency.name" :value="info.currencies[currency.id].balance.toFixed(currency.id.startsWith('local_') ? 2 : 8)" @input="changeBalance(currency.walletId, $event.target.value)"></td>
                            </tr>
                            </tbody>
                        </table>
                        <hr>
                        <input class="form-control" placeholder="Search..." v-model="search">
                        <table id="transactions" class="table dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th style="width: 80%">Transaction</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="transaction in transactions">
                                <td>{{ new Date(transaction.created_at).toLocaleString() }}</td>
                                <td style="width: 80%">
                                    <div>Message: {{ transaction.data.message ? transaction.data.message : '-' }}</div>
                                    <div>Game: {{ transaction.data.game ? transaction.data.game : '-' }}</div>
                                    <div>
                                        Amount: {{ transaction.amount.toFixed(transaction.currency.startsWith('local_') ? 2 : 8) }} {{ currencies[transaction.currency].name }}
                                        (Before: {{ transaction.old.toFixed(transaction.currency.startsWith('local_') ? 2 : 8) }}, Now: {{ transaction.new.toFixed(transaction.currency.startsWith('local_') ? 2 : 8) }})
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import OverviewModal from "../../modals/OverviewModal";

export default {
    data() {
        return {
            info: null,
            userAdvanced: null,
            transactions: [],
            page: 0,
            isLoadingNextPage: false,

            search: '',

            roles: null
        }
    },
    watch: {
        search() {
            if(this.search.length >= 3) {
                axios.post('/admin/transactionsSearch', { user: this.info.user._id, search: this.search }).then(({ data }) => {
                    this.page = 0;
                    this.transactions = data;
                    this.isLoadingNextPage = true;
                });
            } else {
                this.isLoadingNextPage = false;
                this.load();
            }
        }
    },
    computed: {
        ...mapGetters(['currencies', 'user'])
    },
    methods: {
        unbanBreak(id) {
            axios.post('/admin/unbanBreak', { id: id }).then(() => window.location.reload());
        },
        unbanDepositBreak(id) {
            axios.post('/admin/unbanDepositBreak', { id: id }).then(() => window.location.reload());
        },
        setVIPLevel() {
            const vipLevel = prompt('User won\'t be able to upgrade his VIP level naturally. One time bonus will be available.\nEnter VIP level from 0 to 10 (-1 to revert the changes):');
            if(vipLevel) {
                let level = parseInt(vipLevel);
                if(level < -1 || level > 10) return alert('Invalid VIP level. Accepted: 0 - 10');

                axios.post('/admin/forceVip', {
                    level: level,
                    id: this.info.user._id
                }).then(() => this.$toast.success('Success')).catch(() => alert('Failed to force VIP level'));
            }
        },
        changePassword() {
            const pass = prompt('Enter new password:');
            if(!pass) return;

            axios.post('/admin/changePassword', {
                id: this.info.user._id,
                password: pass
            }).then(() => this.$toast.success('Success. New user password: ' + pass));
        },
        changeBalance(id, balance) {
            axios.post('/admin/balance', {
                id: this.info.user._id,
                balance: balance,
                currency: id
            });
        },
        viewGame(id, game) {
            this.$router.push('/', () => {
                setTimeout(() => {
                    OverviewModal.methods.open(id, game);
                }, 100);
            });
        },
        ban(id) {
            axios.post('/admin/ban', { id: id }).then(() => this.$router.go());
        },
        load() {
            if(this.isLoadingNextPage) return;
            this.isLoadingNextPage = true;

            axios.post('/admin/transactions/' + this.info.user._id + '/' + this.page).then(({ data }) => {
                if(data.length === 0) {
                    this.isLoadingNextPage = true;
                    return;
                }

                this.isLoadingNextPage = false;
                this.transactions = this.transactions.concat(data);
                this.page++;
            }).catch(() => this.isLoadingNextPage = false);
        },
        toggleRole(id) {
            if(id === '*' && this.info.user._id === this.user.user._id)
                if(!confirm('Are you sure? You will remove super admin role (*) from yourself!')) return;

            axios.post('/admin/roles/toggleRole', {
                userId: this.info.user._id,
                roleId: id
            }).then(() => {
                this.$router.go();
            });
        }
    },
    created() {
        axios.post('/admin/user', { id: this.$route.params.id }).then(({ data }) => {
            this.info = data;

            this.load();
        });

        axios.post('/admin/userAdvanced', { id: this.$route.params.id }).then(({ data }) => {
            this.userAdvanced = data;
        });

        if(this.$checkPermission('*')) {
            axios.post('/admin/roles/all').then(({ data }) => this.roles = data.roles);
        }

        window.addEventListener('scroll', () => {
            if(window.scrollY > document.body.scrollHeight - window.innerHeight - document.querySelector('footer').offsetHeight - 100) this.load();
        });
    }
}
</script>
