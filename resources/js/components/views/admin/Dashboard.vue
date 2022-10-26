<template>
    <div class="container-fluid">
        <div class="row page-title align-items-center">
            <div class="col-sm-4 col-xl-6">
                <h4 class="mb-1 mt-0">Stats</h4>
            </div>
        </div>

        <div class="dashboard">
            <div class="spinner-border d-flex ml-auto mr-auto" v-if="!deposits"></div>
            <div v-else v-html="deposits"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                deposits: null
            }
        },
        methods: {
            loadScripts() {
                if(this.deposits) {
                    setTimeout(() => {
                        let i = 0;
                        _.forEach($('script'), (e) => {
                            setTimeout(() => {
                                $('body').append($('<script>').html($(e).html()));
                                window.dispatchEvent(new Event('resize'));
                            });
                        }, i * 25);

                        i++;
                    });
                }
            }
        },
        watch: {
            deposits() {
                this.loadScripts();
            }
        },
        created() {
            axios.post('/admin/stats/deposits').then(({ data }) => this.deposits = data.dashboard).catch(() => this.$toast.error('Failed to load income data'));
        }
    }
</script>
