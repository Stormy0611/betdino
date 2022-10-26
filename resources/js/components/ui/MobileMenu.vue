<template>
    <div>
        <div class="mobile-menu">
            <div class="control" @click="$router.push('/')" :class="$route.path === '/' || $route.path === '/casino' ? 'active' : ''">
                <icon icon="originals"></icon>
                <div>Games</div>
            </div>
            <div class="control" @click="$router.push('/vip')" :class="$route.path === '/vip' ? 'active' : ''">
                <icon icon="vip"></icon>
                <div>VIP</div>
            </div>
            <div class="control" @click="$router.push('/referral')" :class="$route.path === '/referral' ? 'active' : ''">
                <icon icon="share"></icon>
                <div>Share</div>
            </div>
            <div class="control" @click="$store.dispatch('toggleChat');">
                <icon icon="chat"></icon>
                <div>{{ $t('general.head.chat') }}</div>
            </div>
            <div class="control" @click="$store.dispatch('toggleChat', false); toggleSidebarMobile()">
                <icon icon="menu"></icon>
                <div>Menu</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import Bus from "../../bus";

    export default {
        methods: {
            openLoginModal() {
                AuthModal.methods.open('auth');
            },
            toggleSidebarMobile() {
                Bus.$emit('sidebar:toggleMobile');
            }
        },
        computed: {
            ...mapGetters(['theme', 'games', 'isGuest', 'user', 'sportGames'])
        }
    }
</script>
