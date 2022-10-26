<template>
    <div :class="'floatingButtons ' + (chat ? '' : 'chatIsHidden')">
        <div class="floatingButton" @click="toggleChat">
            <icon icon="chat-outline"></icon>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['chat'])
        },
        methods: {
            toggleChat() {
                this.$store.dispatch('toggleChat');
            }
        }
    }
</script>
