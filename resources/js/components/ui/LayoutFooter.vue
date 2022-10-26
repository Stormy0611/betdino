<template>
    <footer>
        <div class="container">
            <div class="footer">
                <div class="column grid">
                    <div class="links">
                        <div class="name">BetDino</div>
                        <div class="link-group">
                            <router-link to="/">{{ $t('general.head.allGames') }}</router-link>
                            <router-link to="/casino/game/tag/slots">{{ $t('general.head.slots') }}</router-link>
                            <router-link to="/casino/game/tag/multiplayer">{{ $t('general.head.live') }}</router-link>
                            <router-link to="/casino/game/provider/Originals">{{ $t('general.head.originals') }}</router-link>
                        </div>
                    </div>
                    <div class="links">
                        <div class="name">{{ $t('footer.features.title') }}</div>
                        <div class="link-group">
                            <router-link to="/affiliates">{{ $t('footer.features.affiliates') }}</router-link>
                            <router-link to="/referral">{{ $t('footer.features.referral') }}</router-link>
                        </div>
                    </div>
                    <div class="links">
                        <div class="name">{{ $t('footer.features.promotions') }}</div>
                        <div class="link-group">
                            <router-link to="/promotions">{{ $t('footer.features.promotions') }}</router-link>
                            <router-link to="/promotions">{{ $t('footer.features.daily') }}</router-link>
                        </div>
                    </div>
                    <div class="links">
                        <div class="name">{{ $t('footer.features.sport') }}</div>
                        <div class="link-group">
                            <router-link to="/coming-soon">{{ $t('footer.features.sport') }}</router-link>
                            <router-link to="/coming-soon">{{ $t('footer.features.liveSport') }}</router-link>
                        </div>
                    </div>
                    <div class="links">
                        <div class="name">{{ $t('footer.features.more') }}</div>
                        <div class="link-group">
                            <router-link to="/fairness">{{ $t('footer.features.fairness') }}</router-link>
                            <router-link to="/privacyPolicy">{{ $t('footer.features.privacy') }}</router-link>
                            <router-link to="/terms">{{ $t('footer.features.terms') }}</router-link>
                            <router-link to="/">{{ $t('footer.features.support') }}</router-link>
                        </div>
                    </div>
                    <div class="notes">
                        <i>18+</i>
                        <small>{{ $t('footer.legal') }}</small>
                    </div>
                </div>
                <div class="column payment">
                    <img src="/img/misc/footer/payment.png" alt>
                </div>
                <div class="column">
                    <div class="social-icons">
                        <div class="social-icon" onclick="window.open('https://example.com', '_blank')">
                            <icon icon="fab fa-facebook"></icon>
                        </div>
                        <div class="social-icon" onclick="window.open('https://example.com', '_blank')">
                            <icon icon="fab fa-instagram"></icon>
                        </div>
                        <div class="social-icon" onclick="window.open('https://example.com', '_blank')">
                            <icon icon="fab fa-twitter"></icon>
                        </div>
                        <div class="social-icon" onclick="window.open('https://example.com', '_blank')">
                            <icon icon="fab fa-telegram"></icon>
                        </div>
                        <div class="social-icon" onclick="window.open('https://example.com', '_blank')">
                            <icon icon="fab fa-discord"></icon>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="copy" v-html="$t('footer.copy')"></div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
    import { mapGetters } from 'vuex';
    import TermsModal from "../modals/TermsModal";

    export default {
        computed: {
            ...mapGetters(['isGuest', 'currencies'])
        },
        methods: {
            openTerms(type) {
                TermsModal.methods.open(type);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    footer {
        @include themed() {
            background: #192230;
            margin-top: 15px;

            .footer {
                display: flex;
                flex-direction: column;
                padding: 0 50px;

                .column {
                    display: flex;
                    width: 100%;
                    align-items: center;
                    padding: 25px 0;
                    border-bottom: 2px solid rgba(85,101,126,.2);

                    &.payment {
                        img {
                            width: 0;
                            flex: 1;
                        }

                        @media(max-width: 991px) {
                            display: none;
                        }
                    }

                    &:last-child {
                        border-bottom: none;
                    }

                    &.grid {
                        display: grid;
                        grid-gap: 15px;
                        gap: 15px;
                        grid-template-columns: auto auto auto auto auto 266px;
                        align-items: flex-start;
                        padding: 40px 0;

                        @media(max-width: 1456px) {
                            display: flex;
                            flex-direction: column;
                        }

                        @media(max-width: 991px) {
                            display: flex;
                        }
                    }

                    .notes {
                        display: flex;
                        flex-direction: column;

                        @media(max-width: 991px) {
                            margin-top: 10px;
                        }

                        i {
                            width: 36px;
                            height: 36px;
                            border: 2px solid #fff;
                            border-radius: 50%;
                            text-align: center;
                            line-height: 32px;
                            font-style: normal;
                            font-weight: 600;
                            font-size: 13px;
                            margin-bottom: 15px;
                        }

                        small {
                            font-size: 11px;
                            color: #55657e;
                        }
                    }

                    .links {
                        display: flex;
                        flex-direction: column;

                        .name {
                            text-transform: uppercase;
                            margin-bottom: 15px;
                            color: #93acd3;
                            font-weight: 600;
                        }

                        .link-group {
                            display: flex;
                            flex-direction: column;

                            a {
                                color: #55657e;
                                margin-bottom: 5px;

                                &:last-child {
                                    margin-bottom: 0;
                                }

                                &:hover {
                                    color: white;
                                }
                            }
                        }
                    }

                    .social-icons {
                        display: flex;

                        .social-icon {
                            margin-right: 10px;
                            border-radius: 10px;
                            background: #2A3546;
                            color: #93acd3;
                            padding: 5px 10px;
                            transition: background .3s ease, color .3s ease;
                            cursor: pointer;

                            &:hover {
                                color: white;
                                background: lighten(#2A3546, 5%);
                            }

                            &:last-child {
                                margin-right: 0;
                            }
                        }
                    }

                    .copy {
                        opacity: .3;
                        font-size: 11px;
                    }
                }
            }
        }
    }

    @include media_breakpoint_down(md) {
        footer .footer {
            padding-bottom: 53px !important;

            .column {
                flex-direction: column;

                .copy {
                    text-align: center;
                    margin-bottom: 5px;
                }
            }
        }
    }
</style>
