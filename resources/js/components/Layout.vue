<template>
    <admin-layout v-if="!isGuest && $route.fullPath.toLowerCase().startsWith('/admin')"></admin-layout>
    <website-layout v-else></website-layout>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Bus from "../bus";

    export default {
        created() {
            this.$store.dispatch('changeLocale', this.$store.state.locale);
            this.$store.dispatch('switchTheme', this.$store.state.theme);
            this.$store.dispatch('update');
            this.$store.dispatch('updateData');

            this.reconnectToWS();

            if(typeof URLSearchParams === 'function') {
                const params = new URLSearchParams(window.location.search);
                if(params.has('c')) this.setCookie('c', params.get('c'));
            }

            this.$store.dispatch('setChatChannel', (this.isCasino() ? 'casino_' : 'sport_') + this.locale);
        },
        computed: {
            ...mapGetters(['user', 'isGuest', 'currencies', 'currency', 'locale'])
        },
        methods: {
            reconnectToWS() {
                window.Echo.connector.disconnect();

                window.Echo = new window.LaravelEcho({
                    broadcaster: 'socket.io',
                    host: `${window.location.hostname}:2087`,
                    auth: {
                        headers: {
                            Authorization: `Bearer ${this.isGuest ? null : this.user.token}`
                        }
                    }
                });

                window.Echo.connector.socket.on('connect', () => Bus.$emit('ws:connect'));
                window.Echo.connector.socket.on('disconnect', () => Bus.$emit('ws:disconnect'));

                Echo.private(`App.User.${this.isGuest ? 'Guest' : this.user.user._id}`)
                    .listen('WhisperResponse', (e) => Bus.$emit('event:whisperResponse', e))
                    .listen('BalanceModification', (e) => Bus.$emit('event:balanceModification', e))
                    .listen('BonusBalanceTransferred', (e) => Bus.$emit('event:bonusBalanceTransferred', e));

                Echo.channel('Everyone').listen('ChatMessage', (e) => Bus.$emit('event:chatMessage', e))
                    .listen('ChatRemoveMessages', (e) => Bus.$emit('event:chatRemoveMessages', e))
                    .listen('ChatMessageLike', (e) => Bus.$emit('event:chatMessageLike', e))
                    .listen('NewQuiz', (e) => Bus.$emit('event:chatNewQuiz', e))
                    .listen('QuizAnswered', (e) => Bus.$emit('event:chatQuizAnswered', e))
                    .listen('LiveFeedGame', (e) => Bus.$emit('event:liveGame', e))
                    .listen('LiveFeedSportGame', (e) => Bus.$emit('event:liveSportGame', e))

                    .listen('MultiplayerBettingStateChange', (e) => Bus.$emit('event:multiplayerBettingStateChange', e))
                    .listen('MultiplayerBetCancellation', (e) => Bus.$emit('event:multiplayerBetCancellation', e))
                    .listen('MultiplayerGameFinished', (e) => Bus.$emit('event:multiplayerGameFinished', e))
                    .listen('MultiplayerGameBet', (e) => Bus.$emit('event:multiplayerGameBet', e))
                    .listen('MultiplayerTimerStart', (e) => Bus.$emit('event:multiplayerTimerStart', e))
                    .listen('MultiplayerDataUpdate', (e) => Bus.$emit('event:multiplayerDataUpdate', e));

                if(!this.isGuest) Echo.channel(`App.User.${this.user.user._id}`).notification((notification) => Bus.$emit('event:notification', notification));
            }
        },
        watch: {
            $route(to, from) {
                if((to.path.startsWith("/sport") && !from.path.startsWith("/sport")) || (from.path.startsWith("/sport") && !to.path.startsWith("/sport")))
                    this.$store.dispatch('setChatChannel', (!to.path.startsWith('/sport') ? 'casino_' : 'sport_') + this.locale);
            },
            locale() {
              // this.$router.replace({ params: { lang: this.locale } }).catch(() => {});
            },
            isGuest() {
                this.reconnectToWS();

                if(this.$route.meta.requiresAuth) this.$router.push('/');
                else if(this.$route.meta.requiresPermission) {
                    let flag = true;

                    this.$route.meta.requiresPermission.forEach(permission => {
                        if(flag) flag = window.$permission.$checkPermission(permission.id, permission.type);
                    });

                    if(flag) this.$router.push('/');
                }
            }
        }
    }
</script>
