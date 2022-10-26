<script>
import Bus from '../../bus';
import { mapGetters } from 'vuex';

export default {
    methods: {
        open() {
            Bus.$emit('modal:new', {
                name: 'rakeback',
                component: {
                    data() {
                        return {
                            rakeback: null,
                            disabled: false
                        }
                    },
                    computed: {
                        ...mapGetters(['currencies'])
                    },
                    methods: {
                        load() {
                            this.rakeback = null;
                            axios.post('/api/data/rakeback').then(({ data }) => this.rakeback = data);
                        },
                        claim() {
                            if(this.disabled) return;
                            this.disabled = true;

                            axios.post('/api/data/claimRakeback').then(() => {
                                this.$toast.success('Available cashback is claimed successfully');
                                this.disabled = false;

                                this.load();
                            }).catch(() => {
                                this.disabled = false;
                                this.$toast.error('You can collect cashback after 4 hours');
                            });
                        }
                    },
                    template: `
                            <loader v-if="!rakeback"></loader>
                            <div v-else class="rakebackContent">
                              <div class="p">Cashback</div>
                              <div class="category">
                                <div class="name">
                                  You will receive {{ rakeback.percent }}% cashback from your bets.
                                </div>
                                <small>* Cashback is not available in sport betting.<br>You can collect cashback every 4 hours.</small>
                                <div class="borderCategory"></div>
                              </div>
                              <div class="p">Available cashback:</div>
                              <div class="rakebackValues">
                                <div class="rakebackValue" v-for="rakeback in rakeback.rakeback" v-if="currencies[rakeback.id]" :key="rakeback.id">
                                  <icon :icon="currencies[rakeback.id].icon" :style="{ color: currencies[rakeback.id].style }"></icon> <unit :to="rakeback.id" :value="rakeback.value"></unit>
                                </div>
                              </div>
                              <button class="btn btn-primary claimRakeback" :disabled="disabled" @click="claim">Claim</button>
                            </div>`,
                    created() {
                        this.load();
                    }
                }
            });
        }
    }
}
</script>

<style lang="scss">
@import "resources/sass/variables";
@import "resources/sass/themes";

.xmodal.rakeback {
    max-width: 410px;

    .modal_content {
        padding: 40px !important;

        .loader {
            margin: auto;
        }

        .p {
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .rakebackValue {
            margin-bottom: 10px;

            &:last-child {
                margin-bottom: 0;
            }
        }

        .category {
            .name {
                font-size: .9em;
            }

            .description {

            }

            .borderCategory {
                width: 60px;
                height: 2px;
                background: #1ce1ac;
                margin-top: 5px;
                margin-bottom: 15px;
            }
        }

        .claimRakeback {
            margin-top: 25px;
        }
    }
}
</style>
