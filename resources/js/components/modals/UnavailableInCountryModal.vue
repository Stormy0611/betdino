<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['country'])
        },
        methods: {
            open(unavailableCountry, button) {
                Bus.$emit('modal:new', {
                    name: 'unavailable',
                    dismissible: false,
                    closeOnBackdrop: false,
                    component: {
                        data() {
                            return {
                                unavailableCountry: unavailableCountry,
                                button: button
                            }
                        },
                        template: `
                            <div>
                                <div class="logo"><img src="/img/misc/logo.png"></div>
                                <div class="warn">{{ unavailableCountry }}</div>
                                <div class="btn btn-block btn-primary" @click="close">{{ button }}</div>
                            </div>
                        `,
                        methods: {
                            close() {
                                Bus.$emit('modal:close');
                                this.$store.dispatch('ignoreUnavailableCountryMessage');
                            }
                        }
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.unavailable {
        max-width: 350px;

        .logo {
            display: flex;

            img {
                width: 150px;
                height: 100px;
                margin: auto;
                margin-bottom: 15px;
                margin-top: 15px;
            }
        }

        .os-content {
            padding-top: 10px !important;
        }

        .warn {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn {
            text-transform: uppercase;
            font-weight: 600;
        }
    }
</style>
