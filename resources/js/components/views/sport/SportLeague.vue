<template>
    <div class="sportIndexContainer container">
        <loader v-if="!line"></loader>
        <template v-else>
            <div class="category-header">
                <icon :icon="category.icon"></icon>
                {{ category.id }}
            </div>
            <div class="sportGames">
                <div class="emptyCategory" v-if="line.length === 0">{{ $t('sport.emptyCategoryTitle') }}</div>
                <sport-game v-for="game in line" :key="game.id" :game="game" :category="category"></sport-game>
            </div>
        </template>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                line: null,
                updateInterval: null,

                category: null
            }
        },
        created() {
            this.category = this.sportGames.filter((e) => e.id === this.$route.params.category)[0];
            this.loadLine();

            this.updateInterval = setInterval(() => this.loadLine(), 1500);
        },
        computed: {
            ...mapGetters(['sportGames'])
        },
        methods: {
            loadLine() {
                axios.post('/api/sport/line', { league_id: this.$route.params.id }).then(({ data }) => this.line = data);
            }
        },
        beforeUnmount() {
            clearInterval(this.updateInterval);
        }
    }
</script>
