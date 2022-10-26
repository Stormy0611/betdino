<template>
    <div class="adminDepositList">
        <div class="row page-title align-items-center">
            <div class="col-sm-4 col-xl-6">
                <h4 class="mb-1 mt-0">Deposits</h4>
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
                                    <th>Aggregator ID</th>
                                    <th>Username</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <td>Status</td>
                                    <td>Manage</td>
                                </tr>
                            </thead>
                            <tbody>
                                <div class="p-2" v-if="!invoices">
                                    Loading...
                                </div>
                                <tr v-else v-for="invoice in invoices" :key="invoice.data._id">
                                    <td>{{ invoice.data._id }}</td>
                                    <td>{{ invoice.data.internal_id }}</td>
                                    <td style="cursor: pointer" @click="$router.push('/admin/user/' + invoice.user._id)"><img alt :src="invoice.user.avatar" style="width: 32px; height: 32px; margin-right: 5px;"> {{ invoice.user.name }}</td>
                                    <td>{{ new Date(invoice.data.created_at).toLocaleString() }}</td>
                                    <td>{{ invoice.data.aggregator ? invoice.data.aggregator : invoice.data.currency }}</td>
                                    <td>{{ invoice.data.sum }} {{ currencies[invoice.data.currency].name }}</td>
                                    <td>
                                        <span style="color: red" v-if="invoice.data.status === 0">Pending</span>
                                        <span style="color: green" v-else-if="invoice.data.status === 1">Paid</span>
                                        <span style="color: orange" v-else-if="invoice.data.status === 2">Cancelled</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <template v-if="invoice.data.status === 0">
                                                <button class="btn btn-primary mr-2" @click="accept(invoice.data._id)"><feather size="16" type="check-circle"></feather></button>
                                                <button class="btn btn-danger" @click="cancel(invoice.data._id)"><feather size="16" type="x"></feather></button>
                                            </template>
                                            <!--
                                            <template v-else-if="invoice.data.status === 1">
                                                <button class="btn btn-danger" @click="cancel(invoice.data._id)"><feather size="16" type="x" style="margin-top: -1px; margin-right: 5px;"></feather> Cancel</button>
                                            </template>
                                            -->
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
                invoices: null,
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
            accept(id) {
                axios.post('/admin/wallet/invoice/accept', { id: id }).then(() => this.loadPage());
            },
            cancel(id) {
                axios.post('/admin/wallet/invoice/cancel', { id: id }).then(() => this.loadPage());
            },
            jump() {
                const page = parseInt(prompt(`Enter page (from 1 to ${this.maxPages}):`));
                if(page && !isNaN(page) && page >= 1 && page <= this.maxPages) {
                    this.page = page;
                } else alert('Invalid page: ' + page);
            },
            loadPage() {
                this.invoices = null;

                axios.post('/admin/wallet/invoices', {
                    page: this.page
                }).then(({ data }) => {
                    this.invoices = data.invoices;
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
    .adminDepositList {
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
