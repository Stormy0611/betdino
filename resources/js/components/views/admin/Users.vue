<template>
    <div>
        <div class="row page-title align-items-center">
            <div class="col-sm-4 col-xl-6">
                <h4 class="mb-1 mt-0">Users</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="search-bar">
                            <input class="form-control" placeholder="Search" v-model="search">
                            <button class="btn btn-primary" @click="exportXLSX">Export as .xlsx</button>
                        </div>
                        <div class="table-responsive-container">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Invitation Code</th>
                                    <th>Num. of Invites</th>
                                    <th>VIP level</th>
                                    <th>Deposited ($)</th>
                                    <th>Withdrawn ($)</th>
                                    <th>Registered</th>
                                </tr>
                                </thead>
                                <tbody>
                                <router-link v-if="users" tag="tr" :to="'/admin/user/'+user._id" v-for="user in users" style="cursor: pointer" :key="user._id">
                                    <td><img alt :src="user.avatar" style="width: 32px; height: 32px; margin-right: 5px;"> {{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.inviteCode }}</td>
                                    <td>{{ user.numberOfInvites }}</td>
                                    <td>{{ user.vipLevel }}</td>
                                    <td>{{ user.depositedTotal }}</td>
                                    <td>{{ user.withdrawnUsdTotal }}</td>
                                    <td>{{ new Date(user.created_at).toLocaleString() }}</td>
                                </router-link>
                                <div v-else>This may take a while...</div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import zipcelx from 'zipcelx';

    export default {
        data() {
            return {
                users: [],
                isLoadingNextPage: false,
                page: 0,

                search: ''
            }
        },
        watch: {
            search() {
                if(this.search.length < 3) {
                    this.isLoadingNextPage = false;
                    this.load();
                    return;
                }

                this.page = 0;
                this.users = null;
                this.isLoadingNextPage = true;

                axios.post('/admin/searchUsers', { search: this.search }).then(({ data }) => {
                    this.users = data;
                });
            }
        },
        methods: {
            load() {
                if(this.isLoadingNextPage) return;
                this.isLoadingNextPage = true;

                axios.post('/admin/users/' + this.page).then(({ data }) => {
                    if(data.length === 0) {
                        this.isLoadingNextPage = true;
                        return;
                    }

                    this.users = this.users.concat(data);
                    this.page++;
                    this.isLoadingNextPage = false;
                }).catch(() => this.isLoadingNextPage = false);
            },
            exportXLSX() {
                try {
                    let config = {
                        filename: 'user-list-exported' + (this.search.length === 0 ? '' : '-' + this.search.replaceAll(" ", '-')),
                        sheet: {
                            data: [
                                [
                                    {
                                        value: 'Username',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Email',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Invitation Code',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Num. of Invites',
                                        type: 'string'
                                    },
                                    {
                                        value: 'VIP level',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Deposited ($)',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Withdrawn ($)',
                                        type: 'string'
                                    },
                                    {
                                        value: 'Registered',
                                        type: 'string'
                                    }
                                ]
                            ]
                        }
                    };

                    this.users.forEach(user => {
                        let row = [];

                        row.push({
                            value: user.name,
                            type: 'string'
                        });
                        row.push({
                            value: user.email,
                            type: 'string'
                        });
                        row.push({
                            value: user.inviteCode,
                            type: 'string'
                        });
                        row.push({
                            value: user.numberOfInvites,
                            type: 'number'
                        });
                        row.push({
                            value: user.vipLevel,
                            type: 'number'
                        });
                        row.push({
                            value: user.depositedTotal,
                            type: 'number'
                        });
                        row.push({
                            value: user.withdrawnUsdTotal,
                            type: 'number'
                        });
                        row.push({
                            value: new Date(user.created_at).toLocaleString(),
                            type: 'string'
                        });

                        config.sheet.data.push(row);
                    });

                    zipcelx(config);
                } catch (e) {
                    this.$toast.error('Failed to export');
                }
            }
        },
        created() {
            window.addEventListener('scroll', () => {
                if(window.scrollY > document.body.scrollHeight - window.innerHeight - document.querySelector('footer').offsetHeight - 100) this.load();
            });

            this.load();
        }
    }
</script>

<style lang="scss">
    .search-bar {
        display: flex;

        input {
            flex: 1;
            margin-right: 15px;
        }
    }
</style>
