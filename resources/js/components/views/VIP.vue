<template>
    <div class="vipPage" :class="isGuest ? 'guest' : ''" v-if="vip">
        <template v-if="isGuest">
            <div class="container-fluid">
                <div class="page-title">
                    <div>{{ $t('vip.title') }}</div>
                    <div>{{ $t('vip.title2') }}</div>
                </div>
                <div class="benefits">
                    <div class="benefits-image"></div>
                    <div class="benefit">
                        <div class="icon"></div>
                        <div class="desc">
                            <div>{{ $t('vip.benefits.1.title') }}</div>
                            <div>{{ $t('vip.benefits.1.description') }}</div>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="icon"></div>
                        <div class="desc">
                            <div>{{ $t('vip.benefits.2.title') }}</div>
                            <div>{{ $t('vip.benefits.2.description') }}</div>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="icon"></div>
                        <div class="desc">
                            <div>{{ $t('vip.benefits.3.title') }}</div>
                            <div>{{ $t('vip.benefits.3.description') }}</div>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="icon"></div>
                        <div class="desc">
                            <div>{{ $t('vip.benefits.4.title') }}</div>
                            <div>{{ $t('vip.benefits.4.description') }}</div>
                        </div>
                    </div>
                </div>
                <div class="howItWorks">
                    <div>{{ $t('vip.how.1') }}</div>
                    <div>{{ $t('vip.how.2') }}</div>
                </div>
                <div class="steps">
                    <div class="step">
                        <div class="step-header">
                            <div class="icon"></div>
                            <div class="number">01</div>
                        </div>
                        <div class="s-title">{{ $t('vip.steps.1.title') }}</div>
                        <div class="text">{{ $t('vip.steps.1.description') }}</div>
                    </div>
                    <div class="step">
                        <div class="step-header">
                            <div class="icon"></div>
                            <div class="number">02</div>
                        </div>
                        <div class="s-title">{{ $t('vip.steps.2.title') }}</div>
                        <div class="text">{{ $t('vip.steps.2.description') }}</div>
                    </div>
                    <div class="step">
                        <div class="step-header">
                            <div class="icon"></div>
                            <div class="number">03</div>
                        </div>
                        <div class="s-title">{{ $t('vip.steps.3.title') }}</div>
                        <div class="text">{{ $t('vip.steps.3.description') }}</div>
                    </div>
                </div>
                <div class="playNow">
                    <button class="btn btn-primary" @click="$router.push('/')">{{ $t('vip.steps.playNow') }}</button>
                </div>
                <div class="youCanReach">
                    <div>{{ $t('vip.youCanReach.1') }}</div>
                    <div>{{ $t('vip.youCanReach.2') }}</div>
                </div>
                <!--
                <div class="all-levels">
                    <div class="level" v-for="n in width > 1760 ? 5 : (width > 1560 ? 2 : 1)" :key="n">
                        <div class="icon" :class="`level-${index(vipLevelIndex + n)}`"></div>
                        <div class="title">{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].name }}</div>
                        <div class="title-sm">{{ $t('vip.allLevels.level', { level: index(vipLevelIndex + n) }) }}</div>

                        <div class="description" v-if="vip.filter(e => e.level === index(vipLevelIndex + n))[0].oneTimeBonus > 0">
                            <div>{{ $t('vip.allLevels.oneTimeBonus') }}</div>
                            <div>R$ {{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].oneTimeBonus }}</div>
                        </div>
                        <div class="description">
                            <div>{{ $t('vip.allLevels.withdraws') }}</div>
                            <div><b>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].numberOfWithdrawals }}</b>/day</div>
                        </div>
                        <div class="description">
                            <div>{{ $t('vip.allLevels.fee') }}</div>
                            <div>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].withdrawFee.toFixed(2) }}%</div>
                        </div>
                        <div class="description">
                            <div>{{ $t('vip.allLevels.depositFeeRef') }}</div>
                            <div>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].referralDepositFee.toFixed(2) }}%</div>
                        </div>
                        <div class="claimBonus">
                            <button class="btn btn-primary" v-if="index(vipLevelIndex + n) !== 0 && !isGuest && user.user.vipLevel >= index(vipLevelIndex + n) && !claimed.includes(index(vipLevelIndex + n))
                            && user.user['vip_' + index(vipLevelIndex + n) + '_bonus_claimed'] === undefined" @click="claimBonus(index(vipLevelIndex + n))">{{ $t('vip.allLevels.claim') }}</button>
                        </div>
                        <div class="yourLevel" v-if="!isGuest && user.user.vipLevel === index(vipLevelIndex + n)">{{ $t('vip.allLevels.yourLevel') }}</div>
                    </div>
                </div>
                <div class="scroll">
                    <div class="btn btn-primary" @click="vipLevelIndex--"><i class="fal fa-chevron-left"></i></div>
                    <div class="btn btn-primary" @click="vipLevelIndex++"><i class="fal fa-chevron-right"></i></div>
                </div>
                -->
                <div class="vip-banner">
                    <div class="content">
                        <div class="b-title">{{ $t('vip.allLevels.about.title') }}</div>
                        <div class="b-desc">{{ $t('vip.allLevels.about.description') }}</div>
                        <div class="sub">{{ $t('vip.allLevels.about.sub') }}</div>
                        <div class="b-features">
                            <div class="feature">
                                <div><i class="fal fa-check"></i></div>
                                <div>{{ $t('vip.allLevels.about.features.1') }}</div>
                            </div>
                            <div class="feature">
                                <div><i class="fal fa-check"></i></div>
                                <div>{{ $t('vip.allLevels.about.features.2') }}</div>
                            </div>
                            <div class="feature">
                                <div><i class="fal fa-check"></i></div>
                                <div>{{ $t('vip.allLevels.about.features.3') }}</div>
                            </div>
                            <div class="feature">
                                <div><i class="fal fa-check"></i></div>
                                <div>{{ $t('vip.allLevels.about.features.4') }}</div>
                            </div>
                            <div class="feature">
                                <div><i class="fal fa-check"></i></div>
                                <div>{{ $t('vip.allLevels.about.features.5') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="container" v-else>
            <div class="top">
                <div class="yourVipLevel">
                    <div class="v-title">{{ $t('vip.logged.title') }}</div>
                    <div class="v-title-2">{{ $t('vip.logged.level', { level: user.user.vipLevel }) }}</div>
                    <div class="level">
                        <img :src="`/img/misc/vip/${user.user.vipLevel}.png`" alt>
                    </div>
                    <div class="level-s">
                        <div class="title">{{ $t('vip.logged.deposit') }}</div>
                        <div class="v-progress">
                            <div class="v-title">
                                <template v-if="user.user.vipLevel < 10">
                                    R${{ vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) }}
                                    /
                                    R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) }}
                                </template>
                                <span>
                                    <template v-if="user.user.vipLevel < 10">
                                        {{ fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) }}%
                                    </template>
                                    <template v-else>
                                        100%
                                    </template>
                                </span>
                            </div>
                            <div class="v-bar">
                                <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="level-s">
                        <div class="title">{{ $t('vip.logged.bet') }}</div>
                        <div class="v-progress">
                            <div class="v-title">
                                <template v-if="user.user.vipLevel < 10">
                                    R${{ vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) }}
                                    /
                                    R${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) }}
                                </template>
                                <span>
                                    <template v-if="user.user.vipLevel < 10">
                                        {{ fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) }}%
                                    </template>
                                    <template v-else>
                                        100%
                                    </template>
                                </span>
                            </div>
                            <div class="v-bar">
                                <div :style="{ width: user.user.vipLevel < 10 ? fixPercent(vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) / vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) * 100) + '%' : '100%' }"></div>
                            </div>
                        </div>
                    </div>
                    <template v-if="user.user.vipLevel < 10">
                        <div class="desc">{{ $t('vip.logged.description', { level: user.user.vipLevel + 1 }) }}</div>
                        <div class="state">
                            <div class="st">
                                <div>{{ $t('vip.logged.bet') }}</div>
                                <div>${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].wagerRequirement.toFixed(2) }}</div>
                            </div>
                            <div class="st">
                                <div>{{ $t('vip.logged.dep') }}</div>
                                <div>${{ vip.filter(e => e.level === user.user.vipLevel + 1)[0].depositRequirement.toFixed(2) }}</div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="vipBonus">
                    <div class="v-title">{{ $t('vip.logged.full') }}</div>
                    <div class="chests">
                        <div class="chest" v-for="level in 11" :key="level" :class="(user.user.vipLevel + 1 === level - 1 ? 'next' : '') + ' '
                                + (user.user.vipLevel >= level - 1 && !claimed.includes(level - 1) && user.user['vip_' + (level - 1) + '_bonus_claimed'] === undefined && level - 1 > 0 ? 'take' : '')"
                                @click="user.user.vipLevel >= level - 1 && !claimed.includes(level - 1) && user.user['vip_' + (level - 1) + '_bonus_claimed'] === undefined && level - 1 > 0 ? claimBonus(level - 1) : false">
                            <div class="chest-top">
                                <div class="chest-img" :class="level - 1 === 0 || (user.user.vipLevel >= level - 1) ? 'unlocked' : 'locked'"></div>
                                <div class="chest-reward" v-if="level - 1 > 0">$ {{ vip.filter(e => e.level === level - 1)[0].oneTimeBonus.toFixed(2) }}</div>
                            </div>
                            <div class="chest-bottom">
                                LV {{ level - 1 }}
                            </div>
                        </div>
                    </div>
                    <div class="desc">
                        {{ $t('vip.logged.desc2') }}
                    </div>
                    <div class="state">
                        <div class="st">
                            <div>{{ $t('vip.logged.totalBet') }}</div>
                            <div>R$ {{ vip.filter(e => e.type === 'data')[0].wagered.toFixed(2) }}</div>
                        </div>
                        <div class="st">
                            <div>{{ $t('vip.logged.totalDeposit') }}</div>
                            <div>R$ {{ vip.filter(e => e.type === 'data')[0].deposited.toFixed(2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logged-steps">
                <div class="step">
                    <div class="step-header">
                        <div class="icon"></div>
                        <div class="number">01</div>
                    </div>
                    <div class="text">{{ $t('vip.logged.steps.1') }}</div>
                </div>
                <div class="step">
                    <div class="step-header">
                        <div class="icon"></div>
                        <div class="number">02</div>
                    </div>
                    <div class="text">{{ $t('vip.logged.steps.2') }}</div>
                </div>
                <div class="step">
                    <div class="step-header">
                        <div class="icon"></div>
                        <div class="number">03</div>
                    </div>
                    <div class="text">{{ $t('vip.logged.steps.3') }}</div>
                </div>
            </div>
            <div class="all-levels">
                <div class="level" v-for="n in width <= 1120 ? (width < 390 ? 1 : 2) : 3" :key="n">
                    <div class="icon" :class="`level-${index(vipLevelIndex + n)}`"></div>
                    <div class="title">{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].name }}</div>
                    <div class="title-sm">{{ $t('vip.allLevels.level', { level: index(vipLevelIndex + n) }) }}</div>

                    <div class="requirements">
                        <div class="requirement">
                            <div>{{ $t('vip.totalDeposits') }}</div>
                            <div>R$ {{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].depositRequirement }}</div>
                        </div>
                        <div class="requirement">
                            <div>{{ $t('vip.totalWagered') }}</div>
                            <div>R$ {{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].wagerRequirement }}</div>
                        </div>
                    </div>

                    <div class="description" v-if="vip.filter(e => e.level === index(vipLevelIndex + n))[0].oneTimeBonus > 0">
                        <div>{{ $t('vip.allLevels.oneTimeBonus') }}</div>
                        <div>R$ {{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].oneTimeBonus }}</div>
                    </div>
                    <div class="description">
                        <div>{{ $t('vip.allLevels.withdraws') }}</div>
                        <div><b>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].numberOfWithdrawals }}</b>/24hr</div>
                    </div>
                    <div class="description">
                        <div>{{ $t('vip.allLevels.fee') }}</div>
                        <div>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].withdrawFee.toFixed(2) }}%</div>
                    </div>
                    <div class="description">
                        <div>{{ $t('vip.allLevels.depositFeeRef') }}</div>
                        <div>{{ vip.filter(e => e.level === index(vipLevelIndex + n))[0].referralDepositFee.toFixed(2) }}%</div>
                    </div>
                </div>
            </div>
            <div class="scroll">
                <div class="btn btn-primary" @click="vipLevelIndex--"><i class="fal fa-chevron-left"></i></div>
                <div class="btn btn-primary" @click="vipLevelIndex++"><i class="fal fa-chevron-right"></i></div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import OverlayScrollbars from 'overlayscrollbars';

    export default {
        data() {
            return {
                vipLevelIndex: -3,
                width: 0,
                claimed: []
            }
        },
        computed: {
            ...mapGetters(['vip', 'isGuest', 'user', 'currencies', 'currency'])
        },
        watch: {
            vip() {
                OverlayScrollbars(document.querySelector('.chests'), {
                    overflowBehavior: { y: 'hidden' },
                    scrollbars: { visibility: 'visible' },
                    className: 'os-theme-thin-light'
                });
            }
        },
        methods: {
            fixPercent(e) {
                return e >= 100 ? 100 : e.toFixed(2);
            },
            index(n) {
                if(this.width < 1761) n = n + 2;
                return n < 0 ? (Math.abs(n) % 11 === 0 ? 0 : 11 - Math.abs(n) % 11) : Math.abs(n) % 11;
            },
            claimBonus(level) {
                if(window.$claiming) return;
                window.$claiming = true;

                axios.post('/api/claimOneTimeVipBonus', {
                    level: level
                }).then(({ data }) => {
                    window.$claiming = false;
                    this.claimed.push(level);
                }).catch(() => {
                    this.$toast.error('Failed to claim bonus');
                    window.$claiming = false;
                });
            }
        },
        mounted() {
            this.width = window.innerWidth;
            window.addEventListener('resize', () => this.width = window.innerWidth);

            if(!this.isGuest && this.vip) {
                this.vipLevelIndex += this.user.user.vipLevel;

                OverlayScrollbars(document.querySelector('.chests'), {
                    overflowBehavior: { y: 'hidden' },
                    scrollbars: { visibility: 'visible' },
                    className: 'os-theme-thin-light'
                });
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/themes";

    .vipPage {
        display: flex;
        flex-direction: column;

        &.guest {
            padding: 0 100px;
        }

        .logged-steps {
            display: flex;
            margin-top: 35px;

            .step {
                width: 100%;
                margin-right: 15px;
                background: linear-gradient(119.77deg, #0042B0 2.99%, #3471D9 100%);
                border-radius: 10px;
                padding: 25px;

                .text {
                    color: #CDDAF1;
                    text-align: center;
                    font-weight: 600;

                    @media(max-width: 1560px) {
                        width: unset;
                    }
                }

                .step-header {
                    display: flex;
                    margin-bottom: 5px;

                    .icon {
                        width: 82px;
                        height: 82px;
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                    }

                    .number {
                        margin-left: auto;
                        background: linear-gradient(180.2deg, #95BCFF -5.54%, #008FFF 99.82%);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                        text-fill-color: transparent;
                        font-size: 100px;
                        font-weight: 900;
                        line-height: 100px;

                        @media(max-width: 1560px) {
                            font-size: 50px;
                            line-height: 50px;
                        }

                        @media(min-width: 991px) and (max-width: 1440px) {
                            display: none;
                        }
                    }
                }

                &:nth-child(1) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/ls-1.png');
                    }
                }

                &:nth-child(2) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/ls-2.png');
                    }
                }

                &:nth-child(3) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/ls-3.png');
                    }
                }

                &:last-child {
                    margin-right: 0;
                }
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .step {
                    margin-right: 0;
                    margin-bottom: 15px;

                    .step-header {
                        margin-bottom: 10px;
                    }

                    &:last-child {
                        margin-bottom: 0;
                    }
                }
            }
        }

        @media(max-width: 1400px) {
            &.guest {
                padding: 0 50px;
            }
        }

        @media(max-width: 991px) {
            &.guest {
                padding: 0 25px;
            }
        }

        .top {
            display: flex;

            .vipBonus {
                display: flex;
                flex-direction: column;
                width: 0;
                flex: 1;

                @media(max-width: 991px) {
                    flex: unset;
                    width: calc(100vw - 30px);
                }

                .chests {
                    display: flex;
                    align-items: center;
                    height: 160px;
                    width: calc(100% - 1px);
                    margin-top: 15px;

                    @media(max-width: 991px) {
                        height: 140px;
                    }

                    .os-scrollbar-vertical {
                        display: none;
                    }

                    .chest {
                        flex-shrink: 0;
                        margin-right: 35px;
                        transition: all .3s ease;
                        position: relative;
                        top: -15px;

                        &.take {
                            cursor: pointer;

                            @keyframes take {
                                0% {
                                    transform: scale(1);
                                }

                                50% {
                                    transform: scale(1.05);
                                }

                                100% {
                                    transform: scale(1);
                                }
                            }

                            animation: take 1s ease infinite;
                        }

                        .chest-top {
                            background: linear-gradient(180deg, rgba(29, 95, 207, 0.33) 0%, rgba(15, 32, 61, 0.54) 100%);
                            border-radius: 10px 10px 0px 0px;
                            position: relative;
                            padding: 10px 25px;
                            font-weight: 600;

                            .chest-reward {
                                position: absolute;
                                bottom: 2px;
                                left: 50%;
                                transform: translateX(-50%);
                                text-align: center;
                                white-space: nowrap;
                                font-weight: 800;
                                font-size: 14px;
                                line-height: 17px;
                                color: #C0CFEA;
                            }

                            .chest-img {
                                width: 60px;
                                height: 60px;
                                background-size: cover;
                                background-repeat: no-repeat;
                                opacity: .6;
                                margin: auto;

                                &.unlocked {
                                    background-image: url('/img/misc/vip/unlocked.png');
                                }

                                &.locked {
                                    background-image: url('/img/misc/vip/locked.png');
                                }
                            }
                        }

                        .chest-bottom {
                            background: #0C1E3E;
                            border-radius: 0px 0px 10px 10px;
                            padding: 10px 25px;
                            font-weight: 600;
                            text-align: center;
                        }

                        &.next {
                            .chest-top {
                                .chest-reward {
                                    color: #FFD338;
                                }
                            }

                            .chest-bottom {
                                color: #FFD338;
                            }
                        }

                        @media(max-width: 991px) {
                            margin-right: 5px;

                            .chest-top {
                                padding: 5px 10px;

                                .chest-img {
                                    width: 40px;
                                    height: 40px;
                                }
                            }
                        }
                    }

                    .os-content {
                        display: flex;
                        height: 100% !important;
                        align-items: center;
                    }
                }

                .v-title {
                    text-align: center;
                    font-weight: 600;
                    text-transform: uppercase;
                    position: relative;
                    top: -15px;
                }

                .desc {
                    margin-bottom: 15px;
                    font-weight: 700;
                    font-size: 12px;
                    line-height: 15px;
                    margin-top: auto;
                }

                .state {
                    display: flex;

                    .st {
                        margin-right: 15px;
                        text-align: center;
                        width: 100%;
                        font-weight: 600;
                        background: #162D55;
                        border-radius: 10px;
                        padding: 5px 0;

                        div {
                            &:first-child {
                                font-size: 1.1em;
                            }

                            &:last-child {
                                font-size: 1.2em;
                                color: #FFD338;
                            }
                        }

                        &:last-child {
                            margin-right: 0;
                        }
                    }

                    @media(max-width: 991px) {
                        flex-direction: column;

                        .st {
                            margin-right: 0;

                            &:first-child {
                                margin-bottom: 15px;
                            }
                        }
                    }
                }
            }

            @media(max-width: 991px) {
                flex-direction: column;
            }

            .yourVipLevel {
                width: 320px;
                margin-right: 25px;
                flex-shrink: 0;
                background: linear-gradient(158.63deg, #111721 9.3%, #3471D9 93.33%);
                border-radius: 10px;
                padding: 20px;

                @media(max-width: 991px) {
                    width: 100%;
                    margin-right: 0;
                    margin-bottom: 25px;
                }

                .v-title {
                    text-align: center;
                    font-weight: 600;
                    text-transform: uppercase;
                }

                .v-title-2 {
                    text-align: center;
                    font-weight: 600;
                    text-transform: uppercase;
                    color: #39E036;
                }

                .level {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-top: 15px;
                    margin-bottom: 15px;
                }

                .desc {
                    font-weight: 600;
                    margin-bottom: 10px;
                }

                .state {
                    display: flex;

                    .st {
                        margin-right: 15px;
                        text-align: center;
                        width: 100%;
                        font-weight: 600;
                        background: #162D55;
                        border-radius: 10px;
                        padding: 5px 0;

                        div {
                            &:first-child {
                                font-size: 1.1em;
                            }

                            &:last-child {
                                font-size: 1.2em;
                                color: #FFD338;
                            }
                        }

                        &:last-child {
                            margin-right: 0;
                        }
                    }
                }

                .level-s {
                    border-radius: 10px;
                    margin-bottom: 15px;
                    width: 100%;
                    padding: 10px;

                    .title {
                        font-weight: 600;
                    }

                    .v-progress {
                        .v-title {
                            display: flex;
                            color: #90A3C7;
                            align-items: center;
                            margin-bottom: 5px;

                            span {
                                margin-left: auto;
                            }
                        }

                        .v-bar {
                            height: 10px;
                            width: 100%;
                            background: #687C9C;
                            border-radius: 10px;

                            div {
                                height: 100%;
                                border-radius: inherit;

                                @include themed() {
                                    background: t('secondary');
                                }
                            }
                        }
                    }
                }
            }

            .vipBonus {
                background: #263C62;
                border-radius: 10px;
                padding: 35px;
            }
        }

        .container-fluid {
            max-width: 1350px;
        }

        .vip-banner {
            border-radius: 10px;
            background: url('/img/misc/vip/vip-banner.png') no-repeat center;
            background-size: cover;
            background-position-x: right;

            @media(max-width: 991px) {
                background: linear-gradient(104.12deg, #943d6b 42.39%, #c53c4e 79.94%);
            }

            .content {
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(104.12deg, rgba(86, 52, 158, 0.5) 42.39%, rgba(200, 62, 75, 0) 79.94%);
                border-radius: 10px;
                padding: 50px;

                .b-desc {
                    margin-top: 15px;
                    width: 50%;
                    font-weight: 600;
                    font-size: 1em;
                    margin-bottom: 30px;

                    @media(max-width: 991px) {
                        width: 100%;
                    }
                }

                .b-title {
                    font-weight: 700;
                    font-size: 2.5em;
                }

                .sub {
                    width: 50%;
                    font-weight: 600;
                    font-size: 1.1em;

                    @media(max-width: 991px) {
                        width: 100%;
                    }
                }

                .b-features {
                    color: #39FF35;
                    margin-top: 15px;
                    font-weight: 600;

                    .feature {
                        display: flex;
                        margin-bottom: 10px;

                        div:first-child {
                            margin-right: 10px;
                        }
                    }
                }
            }
        }

        @media(max-width: 991px) {
            font-size: .8em;
        }

        .steps {
            display: flex;

            .step {
                width: 100%;
                margin-right: 15px;
                background: linear-gradient(137.82deg, #11294D 14.77%, #298CA2 100%);
                border-radius: 10px;
                padding: 25px;

                .s-title {
                    text-transform: uppercase;
                    width: 50%;
                    font-weight: 600;
                    font-size: 1.4em;
                    margin-bottom: 15px;

                    @media(max-width: 1560px) {
                        width: unset;
                    }
                }

                .text {
                    color: #CDDAF1;
                    width: 70%;

                    @media(max-width: 1560px) {
                        width: unset;
                    }
                }

                .step-header {
                    display: flex;
                    margin-bottom: 5px;

                    .icon {
                        width: 82px;
                        height: 82px;
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                    }

                    .number {
                        margin-left: auto;
                        background: linear-gradient(180.2deg, #0AFF06 -5.54%, #50B2FF 99.82%);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                        text-fill-color: transparent;
                        font-size: 100px;
                        font-weight: 900;
                        line-height: 100px;

                        @media(max-width: 1560px) {
                            font-size: 50px;
                            line-height: 50px;
                        }

                        @media(min-width: 991px) and (max-width: 1440px) {
                            display: none;
                        }
                    }
                }

                &:nth-child(1) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/s-1.png');
                    }
                }

                &:nth-child(2) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/s-2.png');
                    }
                }

                &:nth-child(3) {
                    .step-header .icon {
                        background-image: url('/img/misc/vip/icons/s-3.png');
                    }
                }

                &:last-child {
                    margin-right: 0;
                }
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .step {
                    margin-right: 0;
                    margin-bottom: 15px;

                    .step-header {
                        margin-bottom: 10px;
                    }

                    &:last-child {
                        margin-bottom: 0;
                    }
                }
            }
        }

        .howItWorks, .youCanReach {
            text-transform: uppercase;
            margin: auto;
            margin-bottom: 25px;
            margin-top: 25px;
            text-align: center;
            width: 590px;

            @media(max-width: 1560px) {
                width: unset;
            }

            div {
                &:first-child {
                    font-size: 2em;
                    font-weight: 600;
                    text-transform: uppercase;
                    margin-bottom: 20px;
                }

                &:last-child {
                    font-size: 1.1em;
                    font-weight: 600;
                    color: #90A3C7;
                }
            }
        }

        .youCanReach {
            width: 700px;
            margin-top: 70px;

            @media(max-width: 1560px) {
                width: unset;
            }
        }

        .benefits {
            position: relative;

            .benefits-image {
                position: absolute;
                right: 0;
                top: -110px;
                width: 430px;
                height: 430px;
                background: url('/img/misc/vip/icons/dino.png') no-repeat center;
                background-size: contain;

                @media(max-width: 1682px) {
                    width: 250px;
                    height: 250px;
                    top: -50px;
                }

                @media(max-width: 1400px) {
                    display: none;
                }
            }

            .benefit {
                margin-bottom: 15px;
                display: flex;
                align-items: center;

                &:last-child {
                    margin-bottom: 0;
                }

                &:nth-child(2) .icon {
                    background-image: url('/img/misc/vip/icons/b-1.png');
                }

                &:nth-child(3) .icon {
                    background-image: url('/img/misc/vip/icons/b-2.png');
                }

                &:nth-child(4) .icon {
                    background-image: url('/img/misc/vip/icons/b-3.png');
                }

                &:nth-child(5) .icon {
                    background-image: url('/img/misc/vip/icons/b-4.png');
                }

                .icon {
                    width: 52px;
                    height: 52px;
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                    margin-right: 25px;
                }

                .desc {
                    div {
                        &:first-child {
                            text-transform: uppercase;
                            font-size: 1.35em;
                            font-weight: 700;
                        }

                        &:last-child {
                            font-size: 1.1em;
                            color: #90A3C7;
                            margin-top: 5px;
                            font-weight: 600;
                        }
                    }
                }

                @media(max-width: 991px) {
                    flex-direction: column;
                    justify-content: center;
                    text-align: center;
                    align-items: center;

                    .icon {
                        margin-bottom: 15px;
                        margin-right: 0;
                    }
                }
            }
        }

        .btn-telegram {
            background: #3D85F0;
            width: fit-content;
            padding: 15px 20px;
            border-radius: 10px;
            margin-top: 35px;
            transition: all .3s ease;
            cursor: pointer;

            &:hover {
                transform: scale(1.05);
            }

            i, svg {
                margin-right: 5px;
            }
        }

        .scroll {
            display: flex;
            margin-top: 30px;
            margin-bottom: 50px;

            @media(max-width: 1761px) {
                margin-top: 25px;
                margin-bottom: 25px;
            }

            @media(max-width: 991px) {
                margin-top: 0;
                margin-bottom: 0;
            }

            .btn {
                background: #39455A;
                border-radius: 10px;
                color: white !important;

                &:hover {
                    background: lighten(#39455A, 5%) !important;
                }

                &:first-child {
                    margin-left: auto;
                    margin-right: 15px;
                }

                &:last-child {
                    margin-right: auto;
                }
            }
        }

        .playNow {
            display: flex;
            margin-top: 45px;
            justify-content: center;

            .btn {
                color: white !important;
                border-radius: 10px;
                padding: 15px 30px;
                font-weight: 600;
            }
        }

        .all-levels {
            display: flex;
            margin-top: 100px;

            @media(max-width: 991px) {
                margin-top: 60px;
            }

            .level {
                display: flex;
                flex-direction: column;
                background: #243A5C;
                position: relative;
                width: 100%;
                padding: 45px;
                text-align: center;
                margin-right: 20px;
                height: auto;
                background: url('/img/misc/vip_rect.png');
                background-size: 100% 100%;
                background-position: center;
                background-repeat: no-repeat;

                .requirements {
                    display: flex;
                    margin-top: 20px;

                    .requirement {
                        width: 100%;
                        background: #162D55;
                        margin-right: 10px;
                        padding: 10px;
                        border-radius: 10px;

                        div:first-child {
                            font-size: 1.1em;
                            font-weight: 600;
                        }

                        div:last-child {
                            color: #4ED223;
                            font-weight: 600;
                            font-size: 1.1em;
                            white-space: nowrap;
                        }
                    }
                }

                @media(max-width: 1600px) {
                    margin-right: 10px;
                    padding: 15px;
                }

                @media(max-width: 1400px) {
                    font-size: .8em;
                }

                @media(max-width: 991px) {
                    margin-right: 0;
                    transform: scale(0.9);
                    font-size: 0.7em;
                }

                &:last-child {
                    margin-right: 0;
                }

                .icon {
                    background-repeat: no-repeat;
                    background-size: cover;
                    width: 76px;
                    height: 82px;
                    margin-left: auto;
                    margin-right: auto;
                    margin-top: -72px;

                    &.level-0 {
                        background-image: url('/img/misc/vip/0.png');
                    }

                    &.level-1 {
                        background-image: url('/img/misc/vip/1.png');
                        background-size: 50px 60px;
                        background-position: center;
                    }

                    &.level-2 {
                        background-image: url('/img/misc/vip/2.png');
                    }

                    &.level-3 {
                        background-image: url('/img/misc/vip/3.png');
                    }

                    &.level-4 {
                        background-image: url('/img/misc/vip/4.png');
                    }

                    &.level-5 {
                        background-image: url('/img/misc/vip/5.png');
                    }

                    &.level-6 {
                        background-image: url('/img/misc/vip/6.png');
                    }

                    &.level-7 {
                        background-image: url('/img/misc/vip/7.png');
                    }

                    &.level-8 {
                        background-image: url('/img/misc/vip/8.png');
                    }

                    &.level-9 {
                        background-image: url('/img/misc/vip/9.png');
                    }

                    &.level-10 {
                        background-image: url('/img/misc/vip/10.png');
                    }
                }

                .title {
                    font-weight: 600;
                    font-size: 1.6em;
                    margin-top: 10px;
                }

                .title-sm {
                    text-transform: uppercase;
                }

                .description {
                    margin-top: 20px;
                    text-transform: uppercase;
                    border-bottom: 1px solid #577198;
                    padding-bottom: 10px;

                    &:last-child {
                        border-bottom: none;
                    }

                    div:first-child {
                        color: #4ED223;
                        font-weight: 600;
                        margin-bottom: 2px;
                        font-size: 1.1em;
                    }
                }

                .yourLevel {
                    @include themed() {
                        color: t('secondary');
                    }

                    text-align: center;
                    text-transform: uppercase;
                    font-family: 'Roboto', sans-serif;
                    font-weight: 600;
                    margin-top: 15px;
                }

                .claimBonus {
                    .btn {
                        margin-top: 20px;
                        text-transform: uppercase;

                        @include themed() {
                            background: t('secondary');
                            color: white !important;

                            &:hover, &:active {
                                background: lighten(t('secondary'), 5%) !important;
                            }
                        }

                        font-family: 'Roboto', sans-serif;
                        font-weight: 600;
                        font-size: 0.9em;
                    }
                }
            }
        }

        .page-title {
            margin-top: 50px;
            position: relative;
            width: 100%;
            margin-bottom: 30px;

            @media(max-width: 991px) {
                text-align: center;
                font-size: .8em;
            }

            div {
                font-size: 3em;
                font-weight: 600;
                line-height: 1.2em;

                &:nth-child(1), &:nth-child(2) {
                    text-transform: uppercase;
                }

                &:nth-child(2) {
                    @include themed() {
                        color: t('secondary');
                    }
                }

                &:nth-child(3) {
                    margin-top: 15px;
                    color: #90A3C7;
                    font-size: 1.3em;
                }
            }
        }

        .vip-levels-preview-text {
            text-align: center;
            width: 330px;
            text-transform: uppercase;
            margin-top: -50px;
            font-weight: 600;
            font-size: 1.35em;
        }
    }
</style>
