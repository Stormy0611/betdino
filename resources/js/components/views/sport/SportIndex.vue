<template>
    <div class="container sportIndex">
        <template v-if="sportGames">
            <template v-if="active">
                <div class="categories">
                    <div class="category" v-for="category in sportGames" :key="category.id" :class="category.id === active.id ? 'active' : ''" @click="active = category"
                            v-if="category.games.length > 0">
                        <icon :icon="category.icon"></icon>
                    </div>
                </div>
                <div class="line">
                    <loader v-if="!line"></loader>
                    <template v-else>
                        <div class="emptyCategory" v-if="line.games.length === 0">{{ $t('sport.emptyCategoryTitle') }}</div>
                        <sport-game v-for="game in line.games" :key="game.id" :game="game" :category="active"></sport-game>
                    </template>
                </div>
            </template>
            <loader v-else></loader>
        </template>
        <loader v-else></loader>
    </div>
</template>

<script>
    import { mapGetters } from "vuex";
    import OverlayScrollbars from "overlayscrollbars";

    export default {
        data() {
            return {
                active: null,

                line: null,
                updateInterval: null
            }
        },
        watch: {
            active() {
                this.line = null;

                clearInterval(this.updateInterval);
                this.updateInterval = setInterval(() => this.load(), 1500);

                this.load();

                setTimeout(() => {
                    OverlayScrollbars(document.querySelector('.categories'), { scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' });
                }, 1);
            },
            sportGames() {
                if(this.active == null) this.setCategory();
            }
        },
        mounted() {
            if(this.sportGames != null) this.setCategory();
        },
        beforeUnmount() {
            clearInterval(this.updateInterval);
        },
        methods: {
            setCategory() {
                console.log(this.sportGames, this.$route.params.id )
                this.active = this.$route.params.id ? this.sportGames.filter((e) => e.id === this.$route.params.id)[0] : this.sportGames[0];
            },
            load() {
                if(!this.active) return;
                const prevCatId = this.active.id;

                axios.post('/api/sport/live', { type: this.active.id }).then(({ data }) => {
                    if(this.active.id !== prevCatId) return;

                    this.line = data;
                });
            }
        },
        computed: {
            ...mapGetters(['sportGames'])
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sportIndex {
        display: flex;
        flex-direction: column;

        .noGames {
            text-align: center;
            font-weight: 600;
            margin-top: 50px;
            margin-bottom: 50px;
            font-size: 1.2em;
        }

        .categories {
            display: flex;
            margin-top: 50px;
            margin-bottom: 25px;
            flex-shrink: 0;
            max-width: calc(100vw - 30px);

            .os-content {
                display: flex;
            }

            @include media-breakpoint-down('md') {
                margin-top: 0;
            }

            .category {
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 16px;

                @include themed() {
                    background: t('sidebar');
                }

                margin-right: 15px;
                opacity: .5;
                transition: opacity .3s ease, background .3s ease;
                cursor: pointer;
                width: 70px;
                height: 70px;
                flex-shrink: 0;

                &:hover, &.active {
                    opacity: 1;
                }

                &.active {
                    @include themed() {
                        background: t('secondary');
                    }
                }

                &:last-child {
                    margin-right: 0;
                }
            }
        }

        .line {
            display: flex;
            flex-direction: column;
        }

        .loaderContainer {
            display: flex;

            .loader {
                margin: auto;
                margin-top: 50px;
                margin-bottom: 50px;
            }
        }
    }
</style>
