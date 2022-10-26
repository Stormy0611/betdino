<template>
    <div>
        <preloader></preloader>
        <div style="background: url('https://psv4.userapi.com/c237031/u303431563/docs/d31/16076ca1b9ed/Novy_proekt_92.png?extra=0lf3xxM7EASpcvLB3vfLvJaHa23RL4fa3bSLFUtpWLum6wuH1avmX810qbJcJd96dOY6_8p61zFgawoDcX1E89exaB6BbAZZmQMrMW85xogxS9SON1UlKeubgjsBiQZkoFuJ82KBb2dgyU55y4B_b3ihulWy') no-repeat center; background-size: cover; width: 100%; z-index: 555; opacity: .15; pointer-events: none; height: 100%; position: fixed; top: 0; left: 0; filter: invert(1);"></div>
        <div id="wrapper">
            <div class="navbar navbar-expand flex-column flex-md-row navbar-custom">
                <div class="container-fluid">
                    <router-link to="/admin" class="navbar-brand mr-0 mr-md-2 logo" style="font-size: 1.8em; cursor: pointer">
                        betdino.io
                    </router-link>
                    <ul class="navbar-nav bd-navbar-nav flex-row list-unstyled menu-left mb-0">
                        <li class="">
                            <button class="button-menu-mobile open-left disable-btn" @click="leftBarActive = !leftBarActive">
                                <feather class="menu-icon" type="menu"></feather>
                                <feather class="close-icon" type="x"></feather>
                            </button>
                        </li>
                    </ul>
                    <img :src="user.user.avatar" width="28" height="28" class="ml-auto mr-1" alt style="border-radius: 50%; cursor: pointer;" @click="$checkPermission('users') ? $router.push('/admin/user/' + user.user._id) : false">
                    <ul class="navbar-nav flex-row d-flex list-unstyled topnav-menu float-right" style="margin-top: 12px" @click="toggleRightBar" v-if="$checkPermission('*')">
                        <li class="dropdown notification-list">
                            <a href="javascript:void(0);" class="nav-link">
                                <feather type="settings"></feather>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="left-side-menu" :style="leftBarActive ? { display: 'block' } : {}">
                <div class="sidebar-content">
                    <div id="sidebar-menu" class="slimscroll-menu">
                        <ul class="metismenu" id="menu-bar">
                            <li class="menu-title">Website</li>

                            <admin-multi-level-dropdown icon="activity" text="Dashboard"
                                :links="[{ link: '/admin', text: 'Stats' }, { link: '/admin/gameStats', text: 'Games' }]"></admin-multi-level-dropdown>

                            <li v-if="$checkPermission('promocodes')">
                                <router-link to="/admin/promo">
                                    <feather type="percent"></feather>
                                    <span> Promocodes</span>
                                </router-link>
                            </li>

                            <admin-multi-level-dropdown icon="users" text="Users" v-if="$checkPermission('users')"
                                :links="[{ link: '/admin/users', text: 'List' }, { link: '/admin/create_user', text: 'Create', condition: $checkPermission('users', 'create') }]"></admin-multi-level-dropdown>

                            <li v-if="$checkPermission('notifications')">
                                <router-link to="/admin/notifications">
                                    <feather type="bell"></feather>
                                    <span> Notifications</span>
                                </router-link>
                            </li>

                            <li v-if="$checkPermission('banner')">
                                <router-link to="/admin/banner">
                                    <feather type="info"></feather>
                                    <span> Banner</span>
                                </router-link>
                            </li>

                            <li v-if="$checkPermission('vip')">
                                <router-link to="/admin/vip">
                                    <feather type="star"></feather>
                                    <span> VIP</span>
                                </router-link>
                            </li>

                            <li class="menu-title" v-if="$checkPermission('withdraws') || $checkPermission('wallet')">Wallet</li>

                            <admin-multi-level-dropdown icon="clock" text="Wallet" v-if="$checkPermission('withdraws')"
                                :links="[{ link: '/admin/wallet/deposits', text: 'Deposits' }, { link: '/admin/wallet/withdraws', text: 'Withdraws' }]"></admin-multi-level-dropdown>

                            <li v-if="$checkPermission('wallet')">
                                <router-link to="/admin/currency">
                                    <feather type="disc"></feather>
                                    <span> Currencies</span>
                                </router-link>
                            </li>
                            <template v-if="$checkPermission('*')">
                                <li class="menu-title">Administrator</li>
                                <li>
                                    <router-link to="/admin/roles">
                                        <feather type="settings"></feather>
                                        <span> Roles</span>
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/admin/activity">
                                        <feather type="alert-triangle"></feather>
                                        <span> Activity</span>
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/admin/modules">
                                        <feather type="git-merge"></feather>
                                        <span> Modules</span>
                                    </router-link>
                                </li>
                                <admin-multi-level-dropdown icon="shuffle" text="Bot"
                                    :links="[{ link: '/admin/bot', text: 'Bets' }, { link: '/admin/chat_bot', text: 'Chat' }]"></admin-multi-level-dropdown>
                                <li class="menu-title">Server</li>
                                <li>
                                    <router-link to="/admin/settings">
                                        <feather type="settings"></feather>
                                        <span> Settings</span>
                                    </router-link>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="window.open('/admin/logs', '_blank');">
                                        <feather type="align-left"></feather>
                                        <span class="badge badge-danger float-right" style="background: #fd0c31" v-if="info">
                                        {{ info.logs.critical }}
                                    </span>
                                        <span class="badge badge-danger float-right" style="position: relative; right: 5px" v-if="info">
                                        {{ info.logs.error }}
                                    </span>
                                        <span> Logs</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">
                                        <feather type="hash"></feather>
                                        <span class="badge badge-primary float-right" v-if="info">
                                        {{ info.version }}
                                    </span>
                                        <span> Version</span>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="content-page">
                <div class="content">
                    <router-view :key="$route.fullPath" class="container-fluid pageContent"></router-view>
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ new Date().getFullYear() }} &copy; betdino.io
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <div class="right-bar">
            <div class="rightbar-title">
                <h5 class="m-0">Games</h5>
            </div>
            <div class="slimscroll-menu">
                <div class="p-3">
                    <div v-for="game in games" :key="game.id" v-if="(game.type === 'Originals' || game.type === 'Slots (Originals)') && !game.isPlaceholder" class="custom-control custom-checkbox mb-2 clickable" @click="toggleGame(game.id)">
                        <input :checked="!game.isDisabled" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label clickable">{{ game.name }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="rightbar-overlay" @click="toggleRightBar"></div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                info: null,
                leftBarActive: false
            }
        },
        computed: {
            ...mapGetters(['games', 'user'])
        },
        mounted() {
            axios.post('/admin/info').then(({ data }) => this.info = data);
        },
        methods: {
            toggleGame(id) {
                axios.post('/admin/toggle', { name: id });
                this.$store.dispatch('updateData');
            },
            toggleRightBar() {
                const active = $('.rightbar-overlay').hasClass('active');
                $('.rightbar-overlay').toggle().toggleClass('active');
                $('.right-bar').css({'right': active ? '-270px' : 0});
            }
        }
    }
</script>
