import VueRouter from 'vue-router';
import AuthModal from "./components/modals/AuthModal";

const routes = [
    { path: '*', component: require('./components/views/PageNotFound.vue').default },

    { path: '/', component: require('./components/views/CasinoIndex.vue').default },
    { path: '/casino', component: require('./components/views/CasinoIndex.vue').default },

    { path: '/casino/game/provider/:provider', component: require('./components/views/GameProvider.vue').default },
    { path: '/casino/game/tag/:tag', component: require('./components/views/GameTag.vue').default },
    { path: '/casino/game/:id', component: require('./components/views/Game.vue').default },

/*    { path: '/sport', component: require('./components/views/sport/SportIndex.vue').default },
    { path: '/sport/category/:id', component: require('./components/views/sport/SportIndex.vue').default },
    { path: '/sport/game/:category/:id', component: require('./components/views/sport/SportGame.vue').default },
    { path: '/sport/league/:category/:id', component: require('./components/views/sport/SportLeague.vue').default },*/

    { path: '/password/reset/:user/:token', component: require('./components/views/CasinoIndex.vue').default },
    { path: '/promotions', component: require('./components/views/Bonus.vue').default },
    { path: '/fairness', component: require('./components/views/Fairness.vue').default },
    { path: '/profile/:tab?/:subTab?/:subTabValue?', component: require('./components/views/Profile.vue').default, meta: { requiresAuth: true } },
    { path: '/vip', component: require('./components/views/VIP.vue').default },
    { path: '/affiliates', component: require('./components/views/Affiliates.vue').default },
    { path: '/referral', component: require('./components/views/Referral.vue').default, meta: { requiresAuth: true } },
    { path: '/coming-soon', component: require('./components/views/ComingSoon.vue').default },

    { path: '/bonus-50', component: require('./components/views/promotions/Bonus50.vue').default },
    { path: '/vip-bonus', component: require('./components/views/promotions/VipBonus.vue').default },
    { path: '/distributor-benefits', component: require('./components/views/promotions/DistributorBenefits.vue').default },
    { path: '/invite', component: require('./components/views/promotions/Invite.vue').default },

    { path: '/terms', component: require('./components/views/Terms.vue').default },
    { path: '/privacyPolicy', component: require('./components/views/PrivacyPolicy.vue').default },
    { path: '/payment_error', component: require('./components/views/PaymentError.vue').default },

    {
        path: '/admin',
        component: require('./components/views/admin/Dashboard.vue').default,
        meta: { requiresPermission: [ { id: 'dashboard', type: 'active' } ] }
    },
    {
        path: '/admin/gameStats',
        component: require('./components/views/admin/GameStats.vue').default,
        meta: { requiresPermission: [ { id: 'dashboard', type: 'active' } ] }
    },
    {
        path: '/admin/roles',
        component: require('./components/views/admin/Roles.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] }
    },
    {
        path: '/admin/promo',
        component: require('./components/views/admin/Promo.vue').default,
        meta: { requiresPermission: [ { id: 'promocodes', type: 'active' } ] } },
    {
        path: '/admin/settings',
        component: require('./components/views/admin/Settings.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] } },
    {
        path: '/admin/notifications',
        component: require('./components/views/admin/Notifications.vue').default,
        meta: { requiresPermission: [ { id: 'notifications', type: 'active' } ] } },
    {
        path: '/admin/users',
        component: require('./components/views/admin/Users.vue').default,
        meta: { requiresPermission: [ { id: 'users', type: 'active' } ] } },
    {
        path: '/admin/create_user',
        component: require('./components/views/admin/CreateUser.vue').default,
        meta: { requiresPermission: [ { id: 'users', type: 'create' } ] }
    },
    {
        path: '/admin/user/:id',
        component: require('./components/views/admin/User.vue').default,
        meta: { requiresPermission: [ { id: 'users', type: 'active' } ] }
    },
    {
        path: '/admin/wallet/deposits',
        component: require('./components/views/admin/WalletDeposits.vue').default,
        meta: { requiresPermission: [ { id: 'withdraws', type: 'active' } ] }
    },
    {
        path: '/admin/wallet/withdraws',
        component: require('./components/views/admin/WalletWithdraws.vue').default,
        meta: { requiresPermission: [ { id: 'withdraws', type: 'active' } ] }
    },
    {
        path: '/admin/modules',
        component: require('./components/views/admin/Modules.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] } },
    {
        path: '/admin/currency',
        component: require('./components/views/admin/Currency.vue').default,
        meta: { requiresPermission: [ { id: 'wallet', type: 'active' } ] } },
    {
        path: '/admin/activity',
        component: require('./components/views/admin/Activity.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] } },
    {
        path: '/admin/bot',
        component: require('./components/views/admin/Bot.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] } },
    {
        path: '/admin/chat_bot',
        component: require('./components/views/admin/ChatBot.vue').default,
        meta: { requiresPermission: [ { id: '*', type: 'active' } ] }
    },
    {
        path: '/admin/banner',
        component: require('./components/views/admin/Banner.vue').default,
        meta: { requiresPermission: [ { id: 'banner', type: 'active' } ] }
    },
    {
        path: '/admin/vip',
        component: require('./components/views/admin/VIP.vue').default,
        meta: { requiresPermission: [ { id: 'vip', type: 'active' } ] }
    }
];

const router = new VueRouter({
    routes,
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        else return { x: 0, y: 0 };
    }
});

router.beforeEach((to, from, next) => {
    const user = (JSON.parse(localStorage.getItem('vuex')) ?? []).user;

    $('.flatpickr-calendar, .modal-backdrop').remove();
    $('body').removeClass('modal-open');

    const updateLayout = () => {
        window.Layout.Previous = true;
        $('[data-rendered-layout]').remove();
        $('head').append($('<style>').attr('data-rendered-layout', 1).html(atob(window.Layout[to.fullPath.startsWith('/admin') && (user && window.$permission.$checkPermission('dashboard')) ? 'Backend' : 'Frontend'])));
    };

    const redirect = () => {
        if(window.Layout.Previous && ((to.fullPath.startsWith('/admin') && from.fullPath.startsWith('/admin')) || (!to.fullPath.startsWith('/admin') && !from.fullPath.startsWith('/admin'))))
            return next();

        updateLayout();
        next();
    };

    if(to.matched.some(record => record.meta.requiresAuth)) {
        if(!(JSON.parse(localStorage.getItem('vuex')) ?? []).user) {
            updateLayout();
            AuthModal.methods.open('auth');
            return false;
        } else redirect();
    }

    if(to.matched.some(record => record.meta.requiresPermission)) {
        setTimeout(() => {
            let flag = true;

            to.meta.requiresPermission.forEach(permission => {
                if(flag) flag = window.$permission.$checkPermission(permission.id, permission.type);
            });

            if(flag) redirect();
            else {
                updateLayout();
            }
        });
    } else redirect();
});

export default router;
