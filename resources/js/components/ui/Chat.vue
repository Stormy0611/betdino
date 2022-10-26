<template>
    <div :class="'chat ' + (chat ? 'visible' : 'hidden')">
        <div class="fixed">
            <div class="chatHeader">
                <icon icon="user"></icon>
                <div class="onlineCount">{{ online }}</div>
                <select class="languageSelector" @change="setLanguage(language)" v-model="language">
                    <option :selected="locale === 'en'" value="en">🇺🇸&emsp;English</option>
                    <option :selected="locale === 'pt-br'" value="pt-br">🇧🇷&emsp;Brasileiro/Português</option>
                    <!--
                    <option :selected="locale === 'ru'" value="ru">🇷🇺&emsp;Русский</option>
                    <option :selected="locale === 'es'" value="es">🇪🇸&emsp;Spanish</option>
                    <option :selected="locale === 'fa'" value="fa">🇮🇷&emsp;Persian</option>
                    <option :selected="locale === 'de'" value="de">🇩🇪&emsp;Deutsch</option>
                    <option :selected="locale === 'id'" value="id">🇮🇩&emsp;Indonesia</option>
                    <option :selected="locale === 'ko'" value="ko">🇰🇷&emsp;한국어</option>
                    <option :selected="locale === 'zh'" value="zh">🇨🇳&emsp;汉语</option>
                    -->
                </select>
            </div>
            <div class="chat-input-hint chatCommands" v-if="expandCommands">
                <div class="chatCommand" v-for="(command, i) in commands" :key="i"
                     @click="expandCommands = false; commandName(command).includes(' ') ? message = '/' + commandName(command).substring(0, commandName(command).indexOf(' ')) : commands[commandName(command)]()">
                    <strong>/{{ commandName(command) }}</strong>
                    {{ $t('general.chat_commands./' + commandName(command).substring(0, !commandName(command).includes(' ') ? commandName(command).length : commandName(command).indexOf(' '))) }}
                </div>
            </div>
            <overlay-scrollbars ref="chatScrollbar" :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" class="messages">
                <vue-context ref="menu" v-if="!isGuest && ($checkPermission('chat_moderator'))">
                    <template slot-scope="child">
                        <li @click.prevent="deleteMessage(child.data.message._id)"><a href="javascript:void(0)">Remove this message</a></li>
                        <li @click.prevent="deleteAllMessagesFrom(child.data.message.user._id)"><a href="javascript:void(0)">Remove all messages</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 5)"><a href="javascript:void(0)">Mute for 5 minutes</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 10)"><a href="javascript:void(0)">Mute for 10 minutes</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 15)"><a href="javascript:void(0)">Mute for 15 minutes</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 30)"><a href="javascript:void(0)">Mute for 30 minutes</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60)"><a href="javascript:void(0)">Mute for 1 hour</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 12)"><a href="javascript:void(0)">Mute for 12 hours</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 24)"><a href="javascript:void(0)">Mute for 1 day</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 24 * 7)"><a href="javascript:void(0)">Mute for 1 week</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 24 * 31)"><a href="javascript:void(0)">Mute for 1 month</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 24 * 31 * 3)"><a href="javascript:void(0)">Mute for 3 months</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 60 * 24 * 31 * 6)"><a href="javascript:void(0)">Mute for 6 months</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 525600)"><a href="javascript:void(0)">Mute for 1 year</a></li>
                        <li @click.prevent="mute(child.data.message.user._id, 525600 * 100)"><a href="javascript:void(0)">Mute forever</a></li>
                    </template>
                </vue-context>

                <template v-for="(message, j) in messages" v-if="!message.user || (!message.channel || (!((user && user.user.ignore ? user.user.ignore : []).includes(message.user._id)) && message.channel === channel))">
                    <div v-if="message.type === 'message'" :key="j" :class="`message from-${message.user.roles.filter(e => e.id === 'chat_moderator').length > 0 ? 'moderator' : 'user'}`" @contextmenu.prevent="$refs.menu.open($event, { message: message })">
                        <div class="avatar">
                            <img :src="message.user.avatar" alt @click="openUserModal(message.user._id)">
                        </div>
                        <div class="message-content">
                            <div class="content">
                                <span class="name" @click="openUserModal(message.user._id)">{{ formatName(message.user.name) }}</span>
                                <div class="firstMessage">{{ message.data }}</div>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'gif'" :key="j" :class="`message from-${message.user.roles.filter(e => e.id === 'chat_moderator').length > 0 ? 'moderator' : 'user'}`" @contextmenu.prevent="!isGuest && ($checkPermission('chat_moderator')) ? $refs.menu.open($event, { message: message }) : ''">
                        <div class="avatar">
                            <img :src="message.user.avatar" alt @click="openUserModal(message.user._id)">
                        </div>
                        <div class="message-content">
                            <div class="content">
                                <span class="name" @click="openUserModal(message.user._id)">{{ formatName(message.user.name) }}</span>
                                <div class="sticker" :style="`background-image: url('${message.data}')`"></div>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'service'" :key="j" class="message service">
                        {{ message.data.message }}
                    </div>
                    <div v-else-if="message.type === 'quiz'" :key="j" class="message quiz">
                        <div class="message-content">
                            <div class="quiz_header">
                                {{ $t('general.quiz') }}
                            </div>
                            <div class="content">
                                <span v-html="message.data.question"></span>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'quiz_answered'" :key="j" class="message quiz">
                        <div class="message-content">
                            <div class="quiz_header">
                                {{ $t('general.quiz') }}
                            </div>
                            <div class="content">
                                <span v-html="message.data.question"></span>
                                <div class="answer">
                                    <div class="answer_header">{{ $t('general.quiz_answer') }}</div>
                                    {{ message.data.correct }}
                                    <div class="answer_user"><span>{{ $t('general.quiz_user') }}</span> <a href="javascript:void(0)" @click="openUserModal(message.data.user._id)">{{ message.data.user.name }}</a></div>
                                    <div><unit :to="message.data.currency" :value="message.data.reward"></unit> {{ currencies[message.data.currency].name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'game_link'" :key="j" :class="`message from-${message.user.roles.filter(e => e.id === 'chat_moderator').length > 0 ? 'moderator' : 'user'}`" @contextmenu.prevent="!isGuest && ($checkPermission('chat_moderator')) ? $refs.menu.open($event, { message: message }) : ''">
                        <div class="avatar">
                            <img :src="message.user.avatar" alt @click="openUserModal(message.user._id)">
                        </div>
                        <div class="message-content">
                            <div class="content">
                                <span @click="openUserModal(message.user._id)" class="name">{{ formatName(message.user.name) }}</span>
                                <div class="game-link" @click="openOverviewModal(message.data._id, message.data.game)">
                                    <div class="icon"><icon :icon="message.data.icon ? message.data.icon : 'fal fa-question-circle'"></icon></div>
                                    <div class="content">
                                        <div>{{ capitalize(message.data.game) }}: #{{ message.data.id }}</div>
                                        <div>{{ $t('general.bets.bet')}}: <unit :to="message.data.currency" :value="message.data.wager"></unit> <icon :icon="message.data.currency" :style="{ color: currencies[message.data.currency].style }"></icon></div>
                                        <div>{{ $t('general.bets.win') + ': ' + message.data.multiplier.toFixed(2) + 'x'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'tip'" :key="j" class="message tip">
                        <div class="avatar">
                            <img :src="message.data.from.avatar" alt @click="openUserModal(message.data.from._id)">
                        </div>
                        <div class="message-content">
                            <img src="/img/misc/tip.png" alt>
                            <div class="content" v-html="$t('general.tip_chat', {
                                link: `/profile`,
                                name: formatName(message.data.from.name),
                                value: rawBitcoin(message.data.currency, parseFloat(message.data.amount)),
                                currency: currencies[message.data.currency].name,
                                icon: currencies[message.data.currency].icon,
                                style: currencies[message.data.currency].style,
                                tolink: `/profile`,
                                toname: formatName(message.data.to.name)
                            })"></div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                    <div v-else-if="message.type === 'rain'" :key="j" class="message rain_bot">
                        <div class="avatar" v-if="message.data.from">
                            <img @click="openUserModal(message.data.from._id)" :src="message.data.from.avatar" alt>
                        </div>
                        <div class="message-content">
                            <div class="content">
                                <div class="rain_users" v-html="getRainUsers(message.data.users)"></div>
                                <div class="mt-2 rain_desc" v-html="$t(`general.${isSummer() ? 'rain' : 'snow'}`, {
                                    sum: rawBitcoin(message.data.currency, message.data.reward),
                                    currency: currencies[message.data.currency].name
                                })"></div>
                                <div class="rain front-row" v-if="isSummer()"></div>
                                <div class="rain back-row" v-if="isSummer()"></div>
                                <div class="snow-back" v-else></div>
                            </div>
                        </div>
                        <div class="like" @click="like(message._id); message.likedByUser = !message.likedByUser">
                            <icon :icon="message.likedByUser ? 'fas fa-heart' : 'fal fa-heart'"></icon>
                            <div class="count">{{ message.liked }}</div>
                        </div>
                    </div>
                </template>
            </overlay-scrollbars>
            <div class="message-send">
                <div v-if="isGuest" class="message-auth-overlay">
                    <button class="btn btn-block btn-primary" @click="openLoginModal">{{ $t('general.auth.login') }}</button>
                </div>
                <div v-if="!isGuest && user.user.mute && +new Date(user.user.mute) > +new Date()" class="message-auth-overlay" style="opacity: 1 !important; text-align: center; font-size: 0.8em;">
                    {{ $t('general.error.muted', { time: new Date(user.user.mute).toLocaleString() }) }}
                </div>
                <div class="column h-100">
                    <div class="user" v-if="!isGuest && !(user.user.mute && +new Date(user.user.mute) > +new Date())">
                        <router-link tag="img" to="/profile" :src="user.user.avatar" alt></router-link>
                    </div>
                    <textarea @keyup.enter="sendChatMessage" v-model="message" class="text-message" :placeholder="$t('general.chat.enter_message')"></textarea>
                </div>
                <div class="column">
                    <div class="column-icon">
                        <div v-if="!isGuest && emojiContainer" class="emoji-container">
                            <div class="content">
                                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                                    <div v-if="emojiCategory === 'gif'">
                                        <div class="search">
                                            <input type="text" placeholder="..." v-model="stickerSearch">
                                        </div>
                                        <loader v-if="stickersAreLoading"></loader>
                                        <div v-else class="stickers">
                                            <div class="sticker" v-for="sticker in stickers"
                                                 :style="`background-image: url('https://i.gifer.com/origin/${sticker.file.path}.gif')`"
                                                 @click="sendSticker(`https://i.gifer.com/origin/${sticker.file.path}.gif`)"></div>
                                        </div>
                                    </div>
                                    <div class="emoji" v-else-if="emojiCategory === 'emoji'" v-for="emoji in unicodeEmoji" @click="addEmoji(emoji)">{{ emoji }}</div>
                                </overlay-scrollbars>
                            </div>
                            <div class="emoji-footer">
                                <div class="content">
                                    <div :class="`emoji-category ${emojiCategory === 'gif' ? 'active' : ''}`" @click="emojiCategory = 'gif'">
                                        GIF
                                    </div>
                                    <div :class="`emoji-category  ${emojiCategory === 'emoji' ? 'active' : ''}`" @click="emojiCategory = 'emoji'">
                                        EMOJI
                                    </div>
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-smile-wink" @click="emojiContainer = !emojiContainer; expandCommands = false;"></i>
                    </div>
                    <div v-if="!isGuest" class="column-icon" @click="expandCommands = !expandCommands; emojiContainer = false"><i class="fal fa-slash fa-rotate-90"></i></div>
                    <div class="column-icon" v-tooltip="$t('general.slowmode')"><i class="fal fa-clock"></i></div>
                    <div class="column-icon" @click="sendChatMessage"><i class="fas fa-paper-plane"></i></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import RainModal from '../modals/RainModal';
    import TipModal from '../modals/TipModal';
    import OverviewModal from '../modals/OverviewModal';
    import OnlineUsersModal from "../modals/OnlineUsersModal";
    import QuizModal from "../modals/QuizModal";
    import bus from "../../bus";
    import UserModal from "../modals/UserModal";

    export default {
        computed: {
            ...mapGetters(['user', 'isGuest', 'chat', 'channel', 'currencies', 'currency', 'unit', 'locale'])
        },
        created() {
            const loadOnline = () => this.whisper('OnlineUsersCount').then((e) => this.online = e.online);
            setInterval(loadOnline, 15000);
            loadOnline();

            if(document.body.clientWidth <= 991 && this.chat)
                this.$store.dispatch('toggleChat');

            this.language = this.locale;

            this.loadChannel();

            if(!this.isGuest && this.$checkPermission('chat_moderator')) {
                this.commands['online'] = () => this.whisper('OnlineUsers').then((response) => OnlineUsersModal.methods.open(response));
                this.commands['quiz'] = () => QuizModal.methods.open();
            }

            Bus.$on('event:chatMessage', (e) => this.messages.push(e.message));
            Bus.$on('event:chatRemoveMessages', (e) => this.messages = this.messages.filter((message) => !e.ids.includes(message._id)));
            Bus.$on('event:chatMessageLike', (e) => {
                const message = this.messages.filter(m => m._id === e.id)[0];
                if(message) message.liked = e.likes;
            });
            Bus.$on('event:chatNewQuiz', (e) => this.messages.push({
                type: 'quiz',
                data: { question: e.quiz }
            }));
            Bus.$on('event:chatQuizAnswered', (e) => this.messages.push({
                data: {
                    user: e.user,
                    question: e.question,
                    correct: e.correct,
                    reward: e.reward,
                    currency: e.currency
                },
                type: 'quiz_answered'
            }));

            Bus.$on('chat:sendServiceMessage', (e) => this.messages.push({
                type: 'service',
                data: { message: this.$i18n.t(e.message, e.data) }
            }));

            this.loadLeaderboard();
        },
        watch: {
            currency() {
                this.loadLeaderboard();
            },
            messages() {
                this.snow();
                this.rain();

                // typo in lib
                setTimeout(() => { if(this.$refs.chatScrollbar) this.$refs.chatScrollbar._osInstace.scroll({ y: '100%' }) }, 50);
            },
            emojiContainer() {
                if(this.emojiContainer && this.emojiCategory === 'gif') {
                    this.stickersAreLoading = false;
                    this.stickers = [];
                    this.stickerPage = 0;
                    this.loadGif();
                }
            },
            emojiCategory() {
                if(this.emojiCategory === 'gif') this.loadGif();
            },
            stickerSearch() {
                this.stickersAreLoading = false;
                this.stickers = [];
                this.stickerPage = 0;
                this.loadGif();
            },
            channel() {
                this.loadChannel();
            }
        },
        methods: {
            openUserModal(id) {
                UserModal.methods.open(id);
            },
            deleteMessage(id) {
                axios.post('/api/chat/moderate/removeMessage', { id: id });
            },
            deleteAllMessagesFrom(user) {
                axios.post('/api/chat/moderate/removeAllFrom', { id: user });
            },
            mute(user, minutes) {
                axios.post('/api/chat/moderate/mute', { id: user, minutes: minutes });
            },
            loadLeaderboard() {
                this.leaderboard = null;
                /*axios.post('/api/leaderboard', {
                    currency: this.currency,
                    positions: 3,
                    type: 'today',
                    orderBy: 'profit'
                }).then(({ data }) => this.leaderboard = data);*/
            },
            loadGif() {
                if(this.stickersAreLoading) return;
                this.stickersAreLoading = true;

                const perPage = 80;
                axios.get(`https://gifer.com/api/${this.stickerSearch === '' ? '' : 'search/'}media?q=${this.stickerSearch}&limit=${perPage}&skip=${this.stickerPage * perPage}`).then(({ data }) => {
                    this.stickerPage += 1;
                    this.stickers = this.stickers.concat(data);
                    this.stickersAreLoading = false;
                }).catch(() => {
                    this.stickersAreLoading = false;
                });
            },
            setLanguage(language) {
                this.$store.dispatch('changeLocale', language);
                this.$store.dispatch('setChatChannel', `${this.isCasino() ? 'casino' : 'sport'}_${language}`);
                this.loadChannel();
            },
            openOverviewModal(id, game) {
                OverviewModal.methods.open(id, game);
            },
            addEmoji(emoji) {
                this.message += (this.message.length === 0 ? '' : ' ') + emoji;
                this.emojiContainer = false;
            },
            snow() {
                $('.snow-back').empty();
                let increment = 0;

                while(increment < 200) {
                    $('.snow-back').append('<div class="snow"></div>');
                    increment++;
                }
            },
            rain() {
                setTimeout(() => {
                    $('.rain').empty();

                    let increment = 0;
                    let drops = "", backDrops = "";

                    while (increment < 100) {
                        const h = (Math.floor(Math.random() * 98 + 1));
                        const fiver = (Math.floor(Math.random() * 5 + 2));
                        const height = (Math.floor(Math.random() * 70 + 25));
                        increment += fiver;
                        drops += `<div class="drop" style="left: ${increment}%; height: ${height}px; bottom: ${fiver + fiver - 1 + 100}%; animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"><div class="stem" style="animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"></div></div>`;
                        backDrops += `<div class="drop" style="right:${increment}%; height: ${height}px; bottom: ${fiver + fiver - 1 + 100}%; animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"><div class="stem" style="animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"></div></div>`;
                    }

                    $('.rain.front-row').append(drops);
                    $('.rain.back-row').append(backDrops);
                }, 500);
            },
            loadChannel() {
                this.messages = [];
                axios.post('/api/chat/history', { channel: this.channel }).then(({ data }) => {
                    this.messages = data.reverse();

                    bus.$emit('chat:sendServiceMessage', { message: 'chat.service.channel_change', data: {
                        channel: this.$i18n.t('chat.service.channel_' + this.channel.split("_")[0], { lang: this.locale.toUpperCase() })
                    } });
                });

                this.snow();
                this.rain();
            },
            formatName(name) {
                if(name.split(" ").length - 1 > 0) name = `${name.split(" ")[0]} ${name.split(" ")[1].substr(0, 1)}.`;
                return name;
            },
            openLoginModal() {
                AuthModal.methods.open('auth');
            },
            getRainUsers(arr) {
                let users = '';
                _.forEach(arr, (e) => users += `<a href="/user/${e._id}" class="disable-pjax" target="_blank">${this.formatName(e.name)}</a>${arr.indexOf(e) === arr.length - 1 ? '' : ', '}`);
                return users;
            },
            isSummer() {
                const month = new Date().getMonth();
                return !(month === 11 || month === 0 || month === 1);
            },
            commandName(command) {
                return Object.keys(this.commands).find(key => this.commands[key] === command);
            },
            sendSticker(gifUrl) {
                this.emojiContainer = false;

                this.whisper('ChatStickerMessage', {
                    url: gifUrl,
                    channel: this.channel
                }).catch((error) => {
                    if(error.response.data.code === 1) this.$toast.error(this.$i18n.t('chat.error.length'));
                    if(error.response.data.code === 2) this.$toast.error(this.$i18n.t('chat.error.muted'));
                });
            },
            sendChatMessage() {
                this.expandCommands = false;
                this.emojiContainer = false;
                this.message = this.message.replaceAll(/\n/g, '');

                for(let i = 0; i < Object.keys(this.commands).length; i++) {
                    const command = Object.keys(this.commands)[i];
                    if(this.message.startsWith(`/${command.substr(0, command.indexOf(' '))}`)) {
                        this.commands[command](this.message.includes(' ') ? this.message.substr(this.message.indexOf(' ') + 1).split(' ') : []);
                        this.message = '';
                        return;
                    }
                }

                this.whisper('ChatMessage', {
                    message: this.message,
                    channel: this.channel
                }).catch((error) => {
                    if(error === 1) this.$toast.error(this.$i18n.t('chat.error.length'));
                    if(error === 2) this.$toast.error(this.$i18n.t('chat.error.muted'));
                });

                this.message = '';
                this.sentNotify = false;
            },
            like(id) {
                if(this.isGuest) this.openLoginModal();
                else axios.post('/api/chat/like', { id: id });
            }
        },
        data() {
            return {
                messages: [],
                message: '',
                emojiContainer: false,
                emojiCategory: 'gif',
                chatChannels: '',
                channels: false,
                sentNotify: false,
                expandCommands: false,
                language: null,

                online: '...',

                stickersAreLoading: false,
                stickerPage: 0,
                stickerSearch: '',
                stickers: [],
                unicodeEmoji: ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤔', '😐', '😑', '😶', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '😴', '😌', '🤓', '😛', '😜', '😝', '🤤', '😒', '😓', '😔', '😕', '🙃', '🤑', '😲', '☹', '🙁', '😖', '😞', '😟', '😤', '😢', '😭', '😦', '😧', '😨', '😩', '😬', '😰', '😱', '😳', '😵', '😡', '😠', '😇', '🤠', '🤡', '🤥', '😷', '🤒', '🤕', '🤢', '🤧', '😈', '👿', '👹', '👺', '💀', '☠', '👻', '👽', '👾', '🤖', '💩', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙈', '🙉', '🙊', '👦', '👦🏻', '👦🏼', '👦🏽', '👦🏾', '👦🏿', '👧', '👧🏻', '👧🏼', '👧🏽', '👧🏾', '👧🏿', '👨', '👨🏻', '👨🏼', '👨🏽', '👨🏾', '👨🏿', '👩', '👩🏻', '👩🏼', '👩🏽', '👩🏾', '👩🏿', '👴', '👴🏻', '👴🏼', '👴🏽', '👴🏾', '👴🏿', '👵', '👵🏻', '👵🏼', '👵🏽', '👵🏾', '👵🏿', '👶', '👶🏻', '👶🏼', '👶🏽', '👶🏾', '👶🏿', '👼', '👼🏻', '👼🏼', '👼🏽', '👼🏾', '👼🏿', '👮', '👮🏻', '👮🏼', '👮🏽', '👮🏾', '👮🏿', '🕵', '🕵🏻', '🕵🏼', '🕵🏽', '🕵🏾', '🕵🏿', '💂', '💂🏻', '💂🏼', '💂🏽', '💂🏾', '💂🏿', '👷', '👷🏻', '👷🏼', '👷🏽', '👷🏾', '👷🏿', '👳', '👳🏻', '👳🏼', '👳🏽', '👳🏾', '👳🏿', '👱', '👱🏻', '👱🏼', '👱🏽', '👱🏾', '👱🏿', '🎅', '🎅🏻', '🎅🏼', '🎅🏽', '🎅🏾', '🎅🏿', '🤶', '🤶🏻', '🤶🏼', '🤶🏽', '🤶🏾', '🤶🏿', '👸', '👸🏻', '👸🏼', '👸🏽', '👸🏾', '👸🏿', '🤴', '🤴🏻', '🤴🏼', '🤴🏽', '🤴🏾', '🤴🏿', '👰', '👰🏻', '👰🏼', '👰🏽', '👰🏾', '👰🏿', '🤵', '🤵🏻', '🤵🏼', '🤵🏽', '🤵🏾', '🤵🏿', '🤰', '🤰🏻', '🤰🏼', '🤰🏽', '🤰🏾', '🤰🏿', '👲', '👲🏻', '👲🏼', '👲🏽', '👲🏾', '👲🏿', '🙍', '🙍🏻', '🙍🏼', '🙍🏽', '🙍🏾', '🙍🏿', '🙎', '🙎🏻', '🙎🏼', '🙎🏽', '🙎🏾', '🙎🏿', '🙅', '🙅🏻', '🙅🏼', '🙅🏽', '🙅🏾', '🙅🏿', '🙆', '🙆🏻', '🙆🏼', '🙆🏽', '🙆🏾', '🙆🏿', '💁', '💁🏻', '💁🏼', '💁🏽', '💁🏾', '💁🏿', '🙋', '🙋🏻', '🙋🏼', '🙋🏽', '🙋🏾', '🙋🏿', '🙇', '🙇🏻', '🙇🏼', '🙇🏽', '🙇🏾', '🙇🏿', '🤦', '🤦🏻', '🤦🏼', '🤦🏽', '🤦🏾', '🤦🏿', '🤷', '🤷🏻', '🤷🏼', '🤷🏽', '🤷🏾', '🤷🏿', '💆', '💆🏻', '💆🏼', '💆🏽', '💆🏾', '💆🏿', '💇', '💇🏻', '💇🏼', '💇🏽', '💇🏾', '💇🏿', '🚶', '🚶🏻', '🚶🏼', '🚶🏽', '🚶🏾', '🚶🏿', '🏃', '🏃🏻', '🏃🏼', '🏃🏽', '🏃🏾', '🏃🏿', '💃', '💃🏻', '💃🏼', '💃🏽', '💃🏾', '💃🏿', '🕺', '🕺🏻', '🕺🏼', '🕺🏽', '🕺🏾', '🕺🏿', '👯', '🕴', '🗣', '👤', '👥', '🤺', '🏇', '⛷', '🏂', '🏌', '🏄', '🏄🏻', '🏄🏼', '🏄🏽', '🏄🏾', '🏄🏿', '🚣', '🚣🏻', '🚣🏼', '🚣🏽', '🚣🏾', '🚣🏿', '🏊', '🏊🏻', '🏊🏼', '🏊🏽', '🏊🏾', '🏊🏿', '⛹', '⛹🏻', '⛹🏼', '⛹🏽', '⛹🏾', '⛹🏿', '🏋', '🏋🏻', '🏋🏼', '🏋🏽', '🏋🏾', '🏋🏿', '🚴', '🚴🏻', '🚴🏼', '🚴🏽', '🚴🏾', '🚴🏿', '🚵', '🚵🏻', '🚵🏼', '🚵🏽', '🚵🏾', '🚵🏿', '🏎', '🏍', '🤸', '🤸🏻', '🤸🏼', '🤸🏽', '🤸🏾', '🤸🏿', '🤼', '🤼🏻', '🤼🏼', '🤼🏽', '🤼🏾', '🤼🏿', '🤽', '🤽🏻', '🤽🏼', '🤽🏽', '🤽🏾', '🤽🏿', '🤾', '🤾🏻', '🤾🏼', '🤾🏽', '🤾🏾', '🤾🏿', '🤹', '🤹🏻', '🤹🏼', '🤹🏽', '🤹🏾', '🤹🏿', '👫', '👬', '👭', '💏', '👩‍❤️‍💋‍👨', '👨‍❤️‍💋‍👨', '👩‍❤️‍💋‍👩', '💑', '👩‍❤️‍👨', '👨‍❤️‍👨', '👩‍❤️‍👩', '👪', '👨‍👩‍👦', '👨‍👩‍👧', '👨‍👩‍👧‍👦', '👨‍👩‍👦‍👦', '👨‍👩‍👧‍👧', '👨‍👨‍👦', '👨‍👨‍👧', '👨‍👨‍👧‍👦', '👨‍👨‍👦‍👦', '👨‍👨‍👧‍👧', '👩‍👩‍👦', '👩‍👩‍👧', '👩‍👩‍👧‍👦', '👩‍👩‍👦‍👦', '👩‍👩‍👧‍👧', '🏻', '🏼', '🏽', '🏾', '🏿', '💪', '💪🏻', '💪🏼', '💪🏽', '💪🏾', '💪🏿', '🤳', '🤳🏻', '🤳🏼', '🤳🏽', '🤳🏾', '🤳🏿', '👈', '👈🏻', '👈🏼', '👈🏽', '👈🏾', '👈🏿', '👉', '👉🏻', '👉🏼', '👉🏽', '👉🏾', '👉🏿', '☝', '☝🏻', '☝🏼', '☝🏽', '☝🏾', '☝🏿', '👆', '👆🏻', '👆🏼', '👆🏽', '👆🏾', '👆🏿', '🖕', '🖕🏻', '🖕🏼', '🖕🏽', '🖕🏾', '🖕🏿', '👇', '👇🏻', '👇🏼', '👇🏽', '👇🏾', '👇🏿', '✌', '✌🏻', '✌🏼', '✌🏽', '✌🏾', '✌🏿', '🤞', '🤞🏻', '🤞🏼', '🤞🏽', '🤞🏾', '🤞🏿', '🖖', '🖖🏻', '🖖🏼', '🖖🏽', '🖖🏾', '🖖🏿', '🤘', '🤘🏻', '🤘🏼', '🤘🏽', '🤘🏾', '🤘🏿', '🤙', '🤙🏻', '🤙🏼', '🤙🏽', '🤙🏾', '🤙🏿', '🖐', '🖐🏻', '🖐🏼', '🖐🏽', '🖐🏾', '🖐🏿', '✋', '✋🏻', '✋🏼', '✋🏽', '✋🏾', '✋🏿', '👌', '👌🏻', '👌🏼', '👌🏽', '👌🏾', '👌🏿', '👍', '👍🏻', '👍🏼', '👍🏽', '👍🏾', '👍🏿', '👎', '👎🏻', '👎🏼', '👎🏽', '👎🏾', '👎🏿', '✊', '✊🏻', '✊🏼', '✊🏽', '✊🏾', '✊🏿', '👊', '👊🏻', '👊🏼', '👊🏽', '👊🏾', '👊🏿', '🤛', '🤛🏻', '🤛🏼', '🤛🏽', '🤛🏾', '🤛🏿', '🤜', '🤜🏻', '🤜🏼', '🤜🏽', '🤜🏾', '🤜🏿', '🤚', '🤚🏻', '🤚🏼', '🤚🏽', '🤚🏾', '🤚🏿', '👋', '👋🏻', '👋🏼', '👋🏽', '👋🏾', '👋🏿', '👏', '👏🏻', '👏🏼', '👏🏽', '👏🏾', '👏🏿', '✍', '✍🏻', '✍🏼', '✍🏽', '✍🏾', '✍🏿', '👐', '👐🏻', '👐🏼', '👐🏽', '👐🏾', '👐🏿', '🙌', '🙌🏻', '🙌🏼', '🙌🏽', '🙌🏾', '🙌🏿', '🙏', '🙏🏻', '🙏🏼', '🙏🏽', '🙏🏾', '🙏🏿', '🤝', '🤝🏻', '🤝🏼', '🤝🏽', '🤝🏾', '🤝🏿', '💅', '💅🏻', '💅🏼', '💅🏽', '💅🏾', '💅🏿', '👂', '👂🏻', '👂🏼', '👂🏽', '👂🏾', '👂🏿', '👃', '👃🏻', '👃🏼', '👃🏽', '👃🏾', '👃🏿', '👣', '👀', '👁', '👁‍🗨', '👅', '👄', '💋', '💘', '❤', '💓', '💔', '💕', '💖', '💗', '💙', '💚', '💛', '💜', '🖤', '💝', '💞', '💟', '❣', '💌', '💤', '💢', '💣', '💥', '💦', '💨', '💫', '💬', '🗨', '🗯', '💭', '🕳', '👓', '🕶', '👔', '👕', '👖', '👗', '👘', '👙', '👚', '👛', '👜', '👝', '🛍', '🎒', '👞', '👟', '👠', '👡', '👢', '👑', '👒', '🎩', '🎓', '⛑', '📿', '💄', '💍', '💎', '🐵', '🐒', '🦍', '🐶', '🐕', '🐩', '🐺', '🦊', '🐱', '🐈', '🦁', '🐯', '🐅', '🐆', '🐴', '🐎', '🦌', '🦄', '🐮', '🐂', '🐃', '🐄', '🐷', '🐖', '🐗', '🐽', '🐏', '🐑', '🐐', '🐪', '🐫', '🐘', '🦏', '🐭', '🐁', '🐀', '🐹', '🐰', '🐇', '🐿', '🦇', '🐻', '🐨', '🐼', '🐾', '🦃', '🐔', '🐓', '🐣', '🐤', '🐥', '🐦', '🐧', '🕊', '🦅', '🦆', '🦉', '🐸', '🐊', '🐢', '🦎', '🐍', '🐲', '🐉', '🐳', '🐋', '🐬', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚', '🦀', '🦐', '🦑', '🦋', '🐌', '🐛', '🐜', '🐝', '🐞', '🕷', '🕸', '🦂', '💐', '🌸', '💮', '🏵', '🌹', '🥀', '🌺', '🌻', '🌼', '🌷', '🌱', '🌲', '🌳', '🌴', '🌵', '🌾', '🌿', '☘', '🍀', '🍁', '🍂', '🍃', '🍇', '🍈', '🍉', '🍊', '🍋', '🍌', '🍍', '🍎', '🍏', '🍐', '🍑', '🍒', '🍓', '🥝', '🍅', '🥑', '🍆', '🥔', '🥕', '🌽', '🌶', '🥒', '🍄', '🥜', '🌰', '🍞', '🥐', '🥖', '🥞', '🧀', '🍖', '🍗', '🥓', '🍔', '🍟', '🍕', '🌭', '🌮', '🌯', '🥙', '🥚', '🍳', '🥘', '🍲', '🥗', '🍿', '🍱', '🍘', '🍙', '🍚', '🍛', '🍜', '🍝', '🍠', '🍢', '🍣', '🍤', '🍥', '🍡', '🍦', '🍧', '🍨', '🍩', '🍪', '🎂', '🍰', '🍫', '🍬', '🍭', '🍮', '🍯', '🍼', '🥛', '☕', '🍵', '🍶', '🍾', '🍷', '🍸', '🍹', '🍺', '🍻', '🥂', '🥃', '🍽', '🍴', '🥄', '🔪', '🏺', '🌍', '🌎', '🌏', '🌐', '🗺', '🗾', '🏔', '⛰', '🌋', '🗻', '🏕', '🏖', '🏜', '🏝', '🏞', '🏟', '🏛', '🏗', '🏘', '🏙', '🏚', '🏠', '🏡', '🏢', '🏣', '🏤', '🏥', '🏦', '🏨', '🏩', '🏪', '🏫', '🏬', '🏭', '🏯', '🏰', '💒', '🗼', '🗽', '⛪', '🕌', '🕍', '⛩', '🕋', '⛲', '⛺', '🌁', '🌃', '🌄', '🌅', '🌆', '🌇', '🌉', '♨', '🌌', '🎠', '🎡', '🎢', '💈', '🎪', '🎭', '🖼', '🎨', '🎰', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗', '🚘', '🚙', '🚚', '🚛', '🚜', '🚲', '🛴', '🛵', '🚏', '🛣', '🛤', '⛽', '🚨', '🚥', '🚦', '🚧', '🛑', '⚓', '⛵', '🛶', '🚤', '🛳', '⛴', '🛥', '🚢', '✈', '🛩', '🛫', '🛬', '💺', '🚁', '🚟', '🚠', '🚡', '🚀', '🛰', '🛎', '🚪', '🛌', '🛏', '🛋', '🚽', '🚿', '🛀', '🛀🏻', '🛀🏼', '🛀🏽', '🛀🏾', '🛀🏿', '🛁', '⌛', '⏳', '⌚', '⏰', '⏱', '⏲', '🕰', '🕛', '🕧', '🕐', '🕜', '🕑', '🕝', '🕒', '🕞', '🕓', '🕟', '🕔', '🕠', '🕕', '🕡', '🕖', '🕢', '🕗', '🕣', '🕘', '🕤', '🕙', '🕥', '🕚', '🕦', '🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘', '🌙', '🌚', '🌛', '🌜', '🌡', '☀', '🌝', '🌞', '⭐', '🌟', '🌠', '☁', '⛅', '⛈', '🌤', '🌥', '🌦', '🌧', '🌨', '🌩', '🌪', '🌫', '🌬', '🌀', '🌈', '🌂', '☂', '☔', '⛱', '⚡', '❄', '☃', '⛄', '☄', '🔥', '💧', '🌊', '🎃', '🎄', '🎆', '🎇', '✨', '🎈', '🎉', '🎊', '🎋', '🎍', '🎎', '🎏', '🎐', '🎑', '🎀', '🎁', '🎗', '🎟', '🎫', '🎖', '🏆', '🏅', '🥇', '🥈', '🥉', '⚽', '⚾', '🏀', '🏐', '🏈', '🏉', '🎾', '🎱', '🎳', '🏏', '🏑', '🏒', '🏓', '🏸', '🥊', '🥋', '🥅', '🎯', '⛳', '⛸', '🎣', '🎽', '🎿', '🎮', '🕹', '🎲', '♠', '♥', '♦', '♣', '🃏', '🀄', '🎴', '🔇', '🔈', '🔉', '🔊', '📢', '📣', '📯', '🔔', '🔕', '🎼', '🎵', '🎶', '🎙', '🎚', '🎛', '🎤', '🎧', '📻', '🎷', '🎸', '🎹', '🎺', '🎻', '🥁', '📱', '📲', '☎', '📞', '📟', '📠', '🔋', '🔌', '💻', '🖥', '🖨', '⌨', '🖱', '🖲', '💽', '💾', '💿', '📀', '🎥', '🎞', '📽', '🎬', '📺', '📷', '📸', '📹', '📼', '🔍', '🔎', '🔬', '🔭', '📡', '🕯', '💡', '🔦', '🏮', '📔', '📕', '📖', '📗', '📘', '📙', '📚', '📓', '📒', '📃', '📜', '📄', '📰', '🗞', '📑', '🔖', '🏷', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '💹', '💱', '💲', '✉', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳', '✏', '✒', '🖋', '🖊', '🖌', '🖍', '📝', '💼', '📁', '📂', '🗂', '📅', '📆', '🗒', '🗓', '📇', '📈', '📉', '📊', '📋', '📌', '📍', '📎', '🖇', '📏', '📐', '✂', '🗃', '🗄', '🗑', '🔒', '🔓', '🔏', '🔐', '🔑', '🗝', '🔨', '⛏', '⚒', '🛠', '🗡', '⚔', '🔫', '🏹', '🛡', '🔧', '🔩', '⚙', '🗜', '⚗', '⚖', '🔗', '⛓', '💉', '💊', '🚬', '⚰', '⚱', '🗿', '🛢', '🔮', '🛒', '🏧', '🚮', '🚰', '♿', '🚹', '🚺', '🚻', '🚼', '🚾', '🛂', '🛃', '🛄', '🛅', '⚠', '🚸', '⛔', '🚫', '🚳', '🚭', '🚯', '🚱', '🚷', '📵', '🔞', '☢', '☣', '⬆', '↗', '➡', '↘', '⬇', '↙', '⬅', '↖', '↕', '↔', '↩', '↪', '⤴', '⤵', '🔃', '🔄', '🔙', '🔚', '🔛', '🔜', '🔝', '🛐', '⚛', '🕉', '✡', '☸', '☯', '✝', '☦', '☪', '☮', '🕎', '🔯', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '⛎', '🔀', '🔁', '🔂', '▶', '⏩', '⏭', '⏯', '◀', '⏪', '⏮', '🔼', '⏫', '🔽', '⏬', '⏸', '⏹', '⏺', '⏏', '🎦', '🔅', '🔆', '📶', '📳', '📴', '♻', '📛', '⚜', '🔰', '🔱', '⭕', '✅', '☑', '✔', '✖', '❌', '❎', '➕', '➖', '➗', '➰', '➿', '〽', '✳', '✴', '❇', '‼', '⁉', '❓', '❔', '❕', '❗', '〰', '©', '®', '™', '#️⃣', '*️⃣', '0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟', '💯', '🔠', '🔡', '🔢', '🔣', '🔤', '🅰', '🆎', '🅱', '🆑', '🆒', '🆓', 'ℹ', '🆔', 'Ⓜ', '🆕', '🆖', '🅾', '🆗', '🅿', '🆘', '🆙', '🆚', '🈁', '🈂', '🈷', '🈶', '🈯', '🉐', '🈹', '🈚', '🈲', '🉑', '🈸', '🈴', '🈳', '㊗', '㊙', '🈺', '🈵', '▪', '▫', '◻', '◼', '◽', '◾', '⬛', '⬜', '🔶', '🔷', '🔸', '🔹', '🔺', '🔻', '💠', '🔘', '🔲', '🔳', '⚪', '⚫', '🔴', '🔵', '🏁', '🚩', '🎌', '🏴', '🏳', '🇦🇨', '🇦🇩', '🇦🇪', '🇦🇫', '🇦🇬', '🇦🇮', '🇦🇱', '🇦🇲', '🇦🇴', '🇦🇶', '🇦🇷', '🇦🇸', '🇦🇹', '🇦🇺', '🇦🇼', '🇦🇽', '🇦🇿', '🇧🇦', '🇧🇧', '🇧🇩', '🇧🇪', '🇧🇫', '🇧🇬', '🇧🇭', '🇧🇮', '🇧🇯', '🇧🇱', '🇧🇲', '🇧🇳', '🇧🇴', '🇧🇶', '🇧🇷', '🇧🇸', '🇧🇹', '🇧🇻', '🇧🇼', '🇧🇾', '🇧🇿', '🇨🇦', '🇨🇨', '🇨🇩', '🇨🇫', '🇨🇬', '🇨🇭', '🇨🇮', '🇨🇰', '🇨🇱', '🇨🇲', '🇨🇳', '🇨🇴', '🇨🇵', '🇨🇷', '🇨🇺', '🇨🇻', '🇨🇼', '🇨🇽', '🇨🇾', '🇨🇿', '🇩🇪', '🇩🇬', '🇩🇯', '🇩🇰', '🇩🇲', '🇩🇴', '🇩🇿', '🇪🇦', '🇪🇨', '🇪🇪', '🇪🇬', '🇪🇭', '🇪🇷', '🇪🇸', '🇪🇹', '🇪🇺', '🇫🇮', '🇫🇯', '🇫🇰', '🇫🇲', '🇫🇴', '🇫🇷', '🇬🇦', '🇬🇧', '🇬🇩', '🇬🇪', '🇬🇫', '🇬🇬', '🇬🇭', '🇬🇮', '🇬🇱', '🇬🇲', '🇬🇳', '🇬🇵', '🇬🇶', '🇬🇷', '🇬🇸', '🇬🇹', '🇬🇺', '🇬🇼', '🇬🇾', '🇭🇰', '🇭🇲', '🇭🇳', '🇭🇷', '🇭🇹', '🇭🇺', '🇮🇨', '🇮🇩', '🇮🇪', '🇮🇱', '🇮🇲', '🇮🇳', '🇮🇴', '🇮🇶', '🇮🇷', '🇮🇸', '🇮🇹', '🇯🇪', '🇯🇲', '🇯🇴', '🇯🇵', '🇰🇪', '🇰🇬', '🇰🇭', '🇰🇮', '🇰🇲', '🇰🇳', '🇰🇵', '🇰🇷', '🇰🇼', '🇰🇾', '🇰🇿', '🇱🇦', '🇱🇧', '🇱🇨', '🇱🇮', '🇱🇰', '🇱🇷', '🇱🇸', '🇱🇹', '🇱🇺', '🇱🇻', '🇱🇾', '🇲🇦', '🇲🇨', '🇲🇩', '🇲🇪', '🇲🇫', '🇲🇬', '🇲🇭', '🇲🇰', '🇲🇱', '🇲🇲', '🇲🇳', '🇲🇴', '🇲🇵', '🇲🇶', '🇲🇷', '🇲🇸', '🇲🇹', '🇲🇺', '🇲🇻', '🇲🇼', '🇲🇽', '🇲🇾', '🇲🇿', '🇳🇦', '🇳🇨', '🇳🇪', '🇳🇫', '🇳🇬', '🇳🇮', '🇳🇱', '🇳🇴', '🇳🇵', '🇳🇷', '🇳🇺', '🇳🇿', '🇴🇲', '🇵🇦', '🇵🇪', '🇵🇫', '🇵🇬', '🇵🇭', '🇵🇰', '🇵🇱', '🇵🇲', '🇵🇳', '🇵🇷', '🇵🇸', '🇵🇹', '🇵🇼', '🇵🇾', '🇶🇦', '🇷🇪', '🇷🇴', '🇷🇸', '🇷🇺', '🇷🇼', '🇸🇦', '🇸🇧', '🇸🇨', '🇸🇩', '🇸🇪', '🇸🇬', '🇸🇭', '🇸🇮', '🇸🇯', '🇸🇰', '🇸🇱', '🇸🇲', '🇸🇳', '🇸🇴', '🇸🇷', '🇸🇸', '🇸🇹', '🇸🇻', '🇸🇽', '🇸🇾', '🇸🇿', '🇹🇦', '🇹🇨', '🇹🇩', '🇹🇫', '🇹🇬', '🇹🇭', '🇹🇯', '🇹🇰', '🇹🇱', '🇹🇲', '🇹🇳', '🇹🇴', '🇹🇷', '🇹🇹', '🇹🇻', '🇹🇼', '🇹🇿', '🇺🇦', '🇺🇬', '🇺🇲', '🇺🇸', '🇺🇾', '🇺🇿', '🇻🇦', '🇻🇨', '🇻🇪', '🇻🇬', '🇻🇮', '🇻🇳', '🇻🇺', '🇼🇫', '🇼🇸', '🇽🇰', '🇾🇪', '🇾🇹', '🇿🇦', '🇿🇲', '🇿🇼'],

                leaderboard: null,

                commands: {
                    'user [name]': (args) => {
                        axios.post('/api/user/find', { name: args[0].replace('.', '') }).then(({ data }) => this.openUserModal(data.id)).catch(() => this.$toast.error(this.$i18n.t('general.error.unknown_user')));
                    },
                    'bet [id]': (args) => {
                        axios.post('/api/game/find', { id: args[0] }).then(({ data }) => OverviewModal.methods.open(data.id, data.game)).then(() => this.$toast.error(this.$i18n.t('general.error.unknown_game')));
                    },
                    'ignore [name]': (args) => {
                        axios.post('/api/user/ignore', { name: args[0].replace('.', '') }).then(() => {
                            this.$store.dispatch('update');
                            this.$toast.success(this.$i18n.t('general.ignore'));
                        }).catch(() => {
                            this.$toast.error(this.$i18n.t('general.error.unknown_user'));
                        });
                    },
                    'unignore [name]': (args) => {
                        axios.post('/api/user/unignore', { name: args[0].replace('.', '') }).then(({ data }) => {
                            this.$store.dispatch('update');
                            this.$toast.success(this.$i18n.t('general.unignore'));
                        }).catch(() => {
                            this.$toast.error(this.$i18n.t('general.error.unknown_user'));
                        });
                    },
                    'tip': () => {
                        TipModal.methods.open();
                    },
                    'rain': () => {
                        RainModal.methods.open();
                    }
                }
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    $send-height: 120px;

    .chat.hidden {
        width: 0 !important;
    }

    .chat {
        width: $chat-width;
        flex-shrink: 0;
        z-index: 38000;
        position: relative;
        transition: width 0.3s ease;
        pointer-events: none;

        .v-context {
            color: black !important;
            border-radius: 0;
            padding-left: 10px;
            padding-right: 10px;
            right: 0;
            width: 230px;
        }

        @include themed() {
            .chatHeader {
                background: #2F3A4D;
                height: 50px;
                padding: 10px 15px;
                display: flex;
                align-items: center;

                svg, i {
                    margin-right: 5px;
                    color: #8FA2C6;
                    margin-top: -1px;
                }

                .onlineCount {
                    margin-right: 15px;
                    color: #8FA2C6;
                }

                select {
                    width: 130px;
                    font-size: 11px;
                    background: #394760;
                    border: none;
                }
            }

            .topWinners {
                height: 150px;
                display: flex;
                flex-direction: column;

                .loaderContainer {
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transform: scale(0.4) translateY(55%);
                    position: absolute;
                    margin-top: -21px;
                }

                .winners {
                    display: flex;
                    margin-top: 88px;
                    width: 100%;
                    align-items: center;
                    justify-content: center;

                    .winner {
                        position: relative;
                        margin-right: 15px;
                        cursor: pointer;

                        &:last-child {
                            margin-right: 0;
                        }

                        .avatar {
                            display: inline-flex;
                            width: 56px;
                            height: 56px;
                            border-radius: 3px;
                            position: relative;
                            margin-right: 11px;
                        }

                        svg {
                            width: 32px;
                            height: 32px;
                            position: absolute;
                            left: 50%;
                            transform: translateX(-50%);
                            top: 30px;
                        }

                        &.first {
                            margin-top: -35px;
                        }
                    }
                }

                .header {
                    width: 100%;
                }
            }

            .emoji-container {
                $emoji-footer-height: 35px;
                flex-direction: column;
                position: absolute;
                background: t('sidebar');
                color: t('text');
                border: 1px solid t('border');
                border-radius: 3px;
                height: 375px;
                right: 20px;
                width: 320px;
                z-index: 50;
                top: -300px;

                .stickers {
                    display: flex;
                    flex-wrap: wrap;
                    padding-top: 42px;
                }

                .sticker {
                    background-color: t('border');
                    width: 50%;
                    height: 80px;
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                }

                .search {
                    margin-bottom: 10px;
                    position: fixed;
                    width: 100%;

                    input {
                        font-size: 0.8em;
                        padding: 10px 15px;
                        width: 298px;
                    }
                }

                .os-host {
                    width: 100%;

                    .loaderContainer {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        margin-top: 20px;
                    }
                }

                .os-content {
                    height: 100% !important;
                }

                .content {
                    height: calc(100% - #{$emoji-footer-height});
                    padding: 10px;
                    display: flex;
                    flex-wrap: wrap;

                    .emoji {
                        display: inline-flex;
                        width: 10%;
                        height: $emoji-footer-height;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                    }
                }

                .emoji-footer {
                    $emoji-category-width: 32px;
                    display: flex;
                    height: $emoji-footer-height;
                    border-top: 1px solid rgba(t('text'), 0.2);

                    .os-content {
                        display: flex !important;
                    }

                    .emoji-category {
                        width: $emoji-category-width;
                        height: 100%;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        font-size: 0.8em;
                        font-weight: 600;
                        opacity: 0.8;
                        transition: opacity 0.3s ease, color 0.3s ease;
                        margin: 0 5px 0 5px;
                        margin-right: 1px;
                        color: t('text');

                        i, img {
                            position: absolute;
                        }

                        img {
                            width: 18px;
                            height: 18px;
                        }

                        &:hover, &.active {
                            opacity: 1;
                        }

                        &.active {
                            color: t('secondary');
                        }
                    }

                    .emoji-container {
                        opacity: 1;
                    }

                    .content {
                        padding: unset !important;
                        display: inline-flex;
                        width: calc(100% - #{$emoji-category-width});
                        height: $emoji-footer-height;

                        .emoji-category {
                            margin-right: 0;
                        }
                    }
                }

                &:after {
                    content: '';
                    border: solid 10px transparent;
                    border-top-color: t('sidebar');
                    position: absolute;
                    bottom: -20px;
                    right: 245px;
                }
            }

            .emoji-container.active {
                display: flex;
            }
        }

        .fixed {
            position: fixed;
            height: calc(100% - #{$header-height});
            width: $chat-width;
            top: $header-height;
            pointer-events: all;

            @include themed() {
                background: t('sidebar');

                .chat-input-hint {
                    position: absolute;
                    bottom: $send-height;
                    width: 100%;
                    background: darken(t('sidebar'), 1.5%);
                    z-index: 3000;

                    .chatCommand {
                        width: 100%;
                        padding: 10px;
                        background: darken(t('sidebar'), 1.5%);
                        transition: background 0.3s ease;

                        &:hover {
                            cursor: pointer;
                            background: lighten(t('sidebar'), 1.5%);
                        }
                    }

                    .hint-content {
                        padding: 15px;
                        max-height: 250px;

                        .hint-tag-name {
                            background: darken(t('sidebar'), 1.5%);
                            opacity: 0.75;
                            transition: background 0.3s ease, opacity 0.3s ease;
                            cursor: pointer;

                            &:hover {
                                background: darken(t('sidebar'), 2.5%);
                                opacity: 1;
                                cursor: pointer;
                            }
                        }
                    }

                    .hint-footer {
                        background: darken(t('sidebar'), 2.5%);
                        padding: 15px;
                    }
                }

                .messages {
                    height: calc(100% - #{$send-height} - 50px);
                    padding: 15px;
                    position: relative;

                    .loader {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 2;
                    }

                    .highlight {
                        display: inline-flex;
                        background: t('sidebar');
                        box-shadow: inset 0 0 1px 1px orangered;
                        border-radius: 3px;
                        width: fit-content;
                        padding: 0 10px;
                    }

                    .message.tip {
                        .message-content {
                            display: inline-block;
                            background: linear-gradient(to right, #3EDA3B, #0496FF, #45E1C5);
                            color: white;

                            a {
                                color: white;
                            }

                            .content {
                                display: unset !important;
                                flex-direction: unset !important;
                            }
                        }
                    }

                    .message.quiz {
                        .message-content {
                            display: flex;
                            flex-direction: column;
                            border-top: 2px solid t('secondary');
                            border-bottom: 2px solid t('secondary');

                            * {
                                user-select: none;
                                -webkit-user-drag: none;
                                pointer-events: none;
                            }

                            .quiz_header {
                                display: flex;
                                flex-direction: row;
                                color: t('secondary');
                                margin-bottom: 5px;

                                span {
                                    color: rgba(t('text'), 0.4);
                                    display: flex;
                                    margin-left: auto;
                                }
                            }

                            .answer {
                                .answer_header {
                                    color: t('secondary');
                                    font-size: 1.1em;
                                    margin-top: 5px;
                                }

                                .answer_user {
                                    span {
                                        color: t('secondary');
                                    }
                                }
                            }
                        }
                    }

                    .message.rain_bot {
                        margin-bottom: 10px;

                        .message-content {
                            background: unset;
                            margin-bottom: 0;
                            overflow: hidden;
                            border-top: 2px solid t('secondary');
                            border-bottom: 2px solid t('secondary');
                            background: #5e66b3;
                            color: white;
                            position: relative;

                            .content {
                                display: flex;
                                flex-direction: column;
                            }

                            &:before {
                                content: '';
                                z-index: 1;
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(black, 0.35);
                            }
                        }

                        .rain_desc {
                            font-weight: 600;
                            color: white;
                        }

                        .rain_users {
                            color: #b1becf !important;

                            a {
                                color: #b1becf !important;
                                &:hover {
                                    color: white !important;
                                }
                            }
                        }

                        .content {
                            z-index: 22;
                            flex-wrap: wrap;
                            white-space: break-spaces;
                            text-shadow: 0 0 1px black;
                        }
                    }

                    .message {
                        display: flex;
                        flex-direction: row;
                        position: relative;

                        &.message.service {
                            padding-bottom: 15px;
                            color: white;
                            font-size: 0.8em;
                            font-weight: 600;
                            margin-top: 15px;
                            opacity: .25;
                        }

                        .avatar {
                            display: inline-flex;
                            width: 40px;
                            height: 40px;
                            border-radius: 3px;
                            position: relative;
                            margin-right: 11px;

                            .vipRank {
                                position: absolute;
                                top: 5px;
                                right: -2px;
                                z-index: 1;

                                svg {
                                    cursor: pointer;
                                    width: 12px;
                                    height: 12px;
                                }
                            }

                            img {
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                cursor: pointer;
                            }
                        }

                        .like {
                            display: flex;
                            align-items: center;
                            flex-direction: column;
                            margin-left: 12px;
                            margin-bottom: auto;
                            margin-top: auto;
                            position: relative;
                            top: -10px;
                            cursor: pointer;
                            transition: all .3s ease;

                            &:hover {
                                transform: scale(1.05);
                            }

                            .count {
                                margin-top: 5px;
                                font-size: .8em;
                            }
                        }

                        .message-content {
                            background: #2E3B51;
                            display: flex;
                            flex-direction: row;
                            padding: 12px 13px;
                            position: relative;
                            width: 100%;
                            align-items: center;
                            font-size: 1em;
                            border-radius: 10px;
                            font-weight: 600;
                            color: #8FA2C6;
                            margin-bottom: 25px;

                            .name {
                                font-weight: 600;
                                cursor: pointer;
                                word-break: keep-all;
                                color: white;
                            }

                            .firstMessage {
                                //word-break: break-all;
                                color: #93acd3;
                                margin-top: 5px;
                            }

                            .content {
                                word-break: break-word;
                                line-height: 1.3em;
                                width: 100%;

                                .sticker {
                                    width: 100%;
                                    background-color: t('border');
                                    height: 135px;
                                    margin-top: 10px;
                                    background-position: center;
                                    background-size: cover;
                                    border-radius: 3px;
                                    background-repeat: no-repeat;
                                }

                                .game-link {
                                    display: flex;
                                    flex-direction: row;
                                    margin-top: 5px;

                                    .icon, .content {
                                        border: 1px solid rgba(t('text'), .1);
                                        margin-top: 1px;
                                        cursor: pointer;
                                    }

                                    .icon {
                                        width: 20%;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        border-top-left-radius: 3px;
                                        border-bottom-left-radius: 3px;
                                        border-right: none;

                                        i, svg {
                                            font-size: 1.3em;
                                        }
                                    }

                                    .content {
                                        width: 80%;
                                        padding: 10px;
                                        border-top-right-radius: 3px;
                                        border-bottom-right-radius: 3px;
                                    }
                                }
                            }
                        }
                    }

                    .message.from-moderator {
                        .message-content {
                            box-shadow: 0 0 1px 1px #8371ff;
                        }

                        .avatar img {
                            box-shadow: 0 0 1px 1px #8371ff;
                        }
                    }

                    .message.from-admin {
                        .message-content {
                            box-shadow: 0 0 1px 1px #e74c3c;
                        }

                        .avatar img {
                            box-shadow: 0 0 1px 2px #e74c3c;
                        }
                    }

                }

                .message-send {
                    height: $send-height;
                    padding: 15px;
                    display: flex;
                    flex-direction: column;
                    position: relative;
                    background: #27354B;

                    .message-auth-overlay {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(t('sidebar'), 0.75);
                        z-index: 100;
                        display: flex;
                        align-items: center;
                        text-align: center;
                        justify-content: center;
                        padding: 35px;
                        opacity: 0;
                        transition: opacity 0.3s ease;
                        &:hover {
                            opacity: 1;
                        }
                    }

                    .column {
                        display: flex;
                        flex-direction: row;

                        &:first-child {
                            .user {
                                position: absolute;
                                top: 27px;
                                left: 26px;

                                img {
                                    width: 22px;
                                    height: 22px;
                                    cursor: pointer;
                                    border-radius: 50%;
                                }

                                .vipRank {
                                    position: absolute;
                                    bottom: 30px;
                                    right: -2px;

                                    svg {
                                        width: 14px;
                                        height: 14px;
                                    }
                                }
                            }
                        }

                        &:last-child {
                            margin-top: 15px;
                        }

                        textarea {
                            background: #394760;
                            border-radius: 10px;
                            padding: 15px 10px 10px 40px;
                            height: 50px;
                            width: 100%;
                            border: none;
                            resize: none;
                            color: t('text');
                            font-size: 0.85em;
                            transition: color 0.3s ease, background 0.3s ease;

                            &::placeholder {
                                color: white;
                            }

                            &:active, &:focus, &:hover {
                                background: darken(#394760, 2%);
                            }
                        }

                        @include only_safari('.column-icon i', (
                            position: relative,
                            top: -3px
                        ));

                        .column-icon {
                            color: t('link');
                            transition: color 0.3s ease;
                            z-index: 10;
                            cursor: pointer;
                            margin-right: 10px;
                            font-size: 1.1em;

                            &:first-child {
                                padding-right: 10px;
                                border-right: 1px solid t('link');
                            }

                            &:last-child {
                                margin-right: 0;
                                margin-left: auto;
                                color: t('secondary');
                            }

                            &:hover {
                                color: t('link-hover');
                            }
                        }
                    }
                }
            }
        }
    }

    @include media-breakpoint-down(md) {
        .chat.visible {
            .fixed {
                display: block !important;
            }
        }

        .chat {
            width: 0 !important;

            .fixed {
                display: none;
                left: 0;
                top: $header-height;
                width: 100vw !important;
                height: calc(100% - 55px - #{$header-height});

                .emoji-container {
                    right: unset;
                    left: 5px;

                    &:after {
                        right: 291px !important;
                    }
                }

                .topWinners {
                    display: none;
                }

                .messages {
                    height: calc(100% - #{$send-height} - 70px) !important;
                }
            }
        }
    }

    @media(max-width: 1200px) {
        .chat, .chat .fixed {
            width: 200px;

            .ribbon {
                transform: scale(0.6) translateX(-85%) !important;
            }

            .emoji-container:after {
                right: 143px;
            }
        }

        .slider {
            .glide .glide__slide {
                background-size: cover !important;
            }
        }
    }
</style>
