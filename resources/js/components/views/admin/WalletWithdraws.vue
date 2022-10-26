<template>
    <div class="adminWithdrawsList">
        <div class="row page-title align-items-center">
            <div class="col-sm-4 col-xl-6">
                <h4 class="mb-1 mt-0">Withdraws</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Payment ID</th>
                                    <th>Username</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <td>Status</td>
                                    <td>Manage</td>
                                </tr>
                            </thead>
                            <tbody>
                                <div class="p-2" v-if="!withdraws">
                                    Loading...
                                </div>
                                <tr v-else v-for="withdraw in withdraws" :key="withdraw.data._id">
                                    <td>{{ withdraw.data._id }}</td>
                                    <td>{{ withdraw.data.note ? withdraw.data.note : '-' }}</td>
                                    <td style="cursor: pointer" @click="$router.push('/admin/user/' + withdraw.user._id)">
                                        <div><img alt :src="withdraw.user.avatar" style="width: 32px; height: 32px; margin-right: 5px;"> {{ withdraw.user.name }}</div>
                                        <multiaccounts class="mt-2" :id="withdraw.user._id"></multiaccounts>
                                    </td>
                                    <td>{{ new Date(withdraw.data.created_at).toLocaleString() }}</td>
                                    <td>
                                        <div v-if="withdraw.data.cpfType">CPF type: {{ withdraw.data.cpfType }}</div>
                                        <div v-if="withdraw.data.whatsApp">WhatsApp: {{ withdraw.data.whatsApp }}</div>
                                        <div v-if="withdraw.data.cpf">CPF: {{ withdraw.data.cpf }}</div>
                                        <div v-if="withdraw.data.chavePix">Chave PIX: {{ withdraw.data.chavePix }}</div>
                                        <div v-if="withdraw.data.address">{{ withdraw.data.address }}</div>
                                    </td>
                                    <td>{{ withdraw.data.sum }} {{ currencies[withdraw.data.currency].name }}</td>
                                    <td>
                                        <span style="color: red" v-if="withdraw.data.status === 0">Pending</span>
                                        <span style="color: green" v-else-if="withdraw.data.status === 1">Paid</span>
                                        <span style="color: orange" v-else-if="withdraw.data.status === 2">Declined (Reason: {{ withdraw.data.decline_reason }})</span>
                                        <span style="color: cornflowerblue" v-else-if="withdraw.data.status === 3">Ignored</span>
                                        <span style="color: black" v-else-if="withdraw.data.status === 4">Cancelled (by user)</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <template v-if="withdraw.data.status === 0">
                                                <button class="btn btn-primary mr-2" @click="acceptWithdraw(withdraw.data._id)"><feather size="16" type="check-circle"></feather></button>
                                                <button class="btn btn-danger mr-2" @click="declineWithdraw(withdraw.data._id)"><feather size="16" type="x"></feather></button>
                                                <button class="btn btn-secondary" @click="ignoreWithdraw(withdraw.data._id)"><feather size="16" type="clock" style="margin-top: -1px; margin-right: 5px;"></feather> Ignore</button>
                                            </template>
                                            <template v-else-if="withdraw.data.status === 3">
                                                <button class="btn btn-secondary" @click="unignoreWithdraw(withdraw.data._id)"><feather size="16" type="clock" style="margin-top: -1px; margin-right: 5px;"></feather> Stop ignoring</button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination" v-if="maxPages">
                            <div class="prev" @click="page -= 2" v-if="page - 2 >= 1">{{ page - 2 }}</div>
                            <div class="prev" @click="page--" v-if="page - 1 >= 1">{{ page - 1 }}</div>
                            <div class="current">{{ page }}</div>
                            <div class="next" @click="page++" v-if="page + 1 <= maxPages">{{ page + 1 }}</div>
                            <div class="next" @click="page += 2" v-if="page + 2 <= maxPages">{{ page + 2 }}</div>
                            <div class="jump" @click="jump">...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                withdraws: null,
                page: 1,
                maxPages: null
            }
        },
        computed: {
            ...mapGetters(['currencies'])
        },
        watch: {
            page() {
                this.loadPage();
            }
        },
        methods: {
            ignoreWithdraw(id) {
                axios.post('/admin/wallet/ignore', { id: id }).then(() => this.loadPage());
            },
            acceptWithdraw(id) {
                const note = prompt('Enter withdraw payment id:');
                if(note) axios.post('/admin/wallet/accept', { id: id, note: note }).then(() => this.loadPage());
            },
            declineWithdraw(id) {
                axios.post('/admin/wallet/decline', { id: id, reason: prompt('Decline reason') }).then(() => this.loadPage());
            },
            unignoreWithdraw(id) {
                axios.post('/admin/wallet/unignore', { id: id }).then(() => this.loadPage());
            },
            jump() {
                const page = parseInt(prompt(`Enter page (from 1 to ${this.maxPages}):`));
                if(page && !isNaN(page) && page >= 1 && page <= this.maxPages) {
                    this.page = page;
                } else alert('Invalid page: ' + page);
            },
            loadPage() {
                this.invoices = null;

                axios.post('/admin/wallet/withdraws', {
                    page: this.page
                }).then(({ data }) => {
                    this.withdraws = data.withdraws;
                    this.maxPages = data.maxPages;
                });
            }
        },
        mounted() {
            this.loadPage();
        }
    }
</script>

<style lang="scss">
    .adminWithdrawsList {
        .btn {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination {
            font-size: 16px;

            .current {
                color: #5369f8;
                font-weight: 600;
            }

            div {
                margin-right: 5px;
                cursor: pointer;

                &:last-child {
                    margin-right: 0;
                }
            }
        }
    }
</style>
