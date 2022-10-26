<template>
    <div class="container-fluid">
        <div class="row page-title align-items-center">
            <div class="col-sm-4 col-xl-6">
                <h4 class="mb-1 mt-0">Stats</h4>
            </div>
        </div>

        <div class="dashboard_games">
            <div class="spinner-border d-flex ml-auto mr-auto mt-3" v-if="!gamesAnalytics"></div>
            <div v-else v-html="gamesAnalytics"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                gamesAnalytics: null
            }
        },
        methods: {
            loadScripts() {
                if(this.gamesAnalytics) {
                    setTimeout(() => {
                        let i = 0;
                        _.forEach($('script'), (e) => {
                            setTimeout(() => {
                                $('body').append($('<script>').html($(e).html()));
                                window.dispatchEvent(new Event('resize'));
                            }, i * 25);

                            i++;
                        });
                    });
                }
            }
        },
        watch: {
            gamesAnalytics() {
                this.loadScripts();
            }
        },
        created() {
            axios.post('/admin/stats/games').then(({ data }) => this.gamesAnalytics = data.games).catch(() => this.$toast.error('Failed to load game data'));
        }
    }
</script>
