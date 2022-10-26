<script>
    import Bus from '../../bus';

    export default {
        methods: {
            open(user) {
                Bus.$emit('modal:new', {
                    name: 'user',
                    component: {
                        data() {
                            return {
                                userId: user,
                                info: null
                            }
                        },
                        created() {
                            axios.post('/api/profile/getUser', { id: this.userId }).then(({ data }) => {
                                this.info = data;
                            });
                        },
                        template: `
                          <loader v-if="!info"></loader>
                          <div class="profile" v-else>
                            <div class="profile-header">
                              <div class="avatar" :style="{ backgroundImage: 'url(' + info.user.avatar + ')' }"></div>
                              <div class="name">{{ info.user.name }}</div>
                              <div class="date">{{ $t('userModal.joined', { date: new Date(info.user.created_at).toLocaleString() }) }}</div>
                            </div>
                            <div class="profile-stats">
                                <div class="stat">
                                   <div>{{ $t('userModal.wagered') }}</div>
                                   <div>R$ {{ info.wagered.toFixed(2) }}</div>
                                </div>
                                <div class="stat">
                                   <div>{{ $t('userModal.totalGames') }}</div>
                                   <div>{{ info.games }}</div>
                                </div>
                            </div>
                          </div>`
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .xmodal.user {
        max-width: 350px;

        .modal_content {
            padding: 0 !important;

            .profile-stats {
                display: flex;

                .stat {
                    margin: 10px;
                    width: calc(100% - 20px - 1px);
                    border: 1px solid lighten(#2A3446, 2.5%);
                    padding: 20px;
                    text-align: center;

                    div:first-child {
                        opacity: .6;
                    }

                    div:last-child {
                        font-weight: 600;
                    }
                }
            }

            .profile-header {
                background: lighten(#2A3446, 2.5%);
                display: flex;
                flex-direction: column;

                .avatar {
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    width: 50px;
                    height: 50px;
                    margin: auto;
                    margin-top: 30px;
                    margin-bottom: 15px;
                    border-radius: 50%;
                }

                .name {
                    font-weight: 600;
                    text-align: center;
                    font-size: 1.1em;
                }

                .date {
                    text-align: center;
                    font-size: .9em;
                    margin-bottom: 15px;
                }
            }
        }

        .loader {
            margin: auto;
        }
    }
</style>
