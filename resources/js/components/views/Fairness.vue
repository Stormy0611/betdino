<template>
    <div class="container fairness">
        <div class="title">
            <div class="t-title" v-html="$t('fairness.title')"></div>
            <div class="text" v-html="$t('fairness.description')"></div>
        </div>
        <div class="mobile-hero"></div>
        <div class="description">
            <div class="d-title">{{ $t('fairness.how') }}</div>
            <div class="d-desc">{{ $t('fairness.how2') }}</div>

            <div class="step">
                <div class="number">01</div>
                <div class="content">
                    <div class="s-title">{{ $t('fairness.01.title') }}</div>
                    <div class="s-text" v-html="$t('fairness.01.description')"></div>
                </div>
                <div class="step-img img-1"></div>
            </div>
            <div class="step">
                <div class="number">02</div>
                <div class="content">
                    <div class="s-title">{{ $t('fairness.02.title') }}</div>
                    <div class="s-text" v-html="$t('fairness.02.description')"></div>
                </div>
                <div class="step-img img-2"></div>
            </div>
            <div class="step">
                <div class="number">03</div>
                <div class="content">
                    <div class="s-title">{{ $t('fairness.03.title') }}</div>
                    <div class="s-text" v-html="$t('fairness.03.description')"></div>
                </div>
                <div class="step-img img-3"></div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .fairness {
        .title {
            padding: 80px 0;
            background: url('/img/misc/hero.png') no-repeat right center;
            background-size: contain;
            width: 100%;

            @media(max-width: 991px) {
                background: unset;
                padding: 0;
                margin-top: 15px;
                margin-bottom: 15px;
            }

            .t-title {
                font-weight: 700;
                font-size: 2.5em;
                line-height: 1.3em;
                margin-bottom: 15px;
                text-transform: uppercase;
            }

            .text {
                font-weight: 600;
                font-size: 1.1em;
                color: #90A3C7;
            }
        }

        .mobile-hero {
            display: none;

            @media(max-width: 991px) {
                display: block;
            }

            background: url('/img/misc/hero.png') no-repeat right center;
            width: 100%;
            height: 200px;
            background-size: contain;
        }

        .description {
            padding: 25px;
            background: linear-gradient(180deg, #11294E 0%, #28879D 100%);
            border-radius: 25px;
            margin-top: -30px;
            display: flex;
            flex-direction: column;

            .step {
                display: flex;
                margin-bottom: 15px;
                align-items: center;

                .step-img {
                    width: 50%;
                    margin-left: 15px;
                    background-size: contain;
                    height: 200px;
                    background-repeat: no-repeat;
                    background-position: center;

                    &.img-1 {
                        background-image: url('/img/misc/fairness-1.png');
                    }

                    &.img-2 {
                        background-image: url('/img/misc/fairness-2.png');
                    }

                    &.img-3 {
                        background-image: url('/img/misc/fairness-3.png');
                    }
                }

                .content {
                    width: 80%;
                    margin-right: 15px;
                    margin-left: 15px;

                    .s-title {
                        font-weight: 600;
                        font-size: 1.15em;
                        margin-bottom: 15px;
                    }

                    .s-text {
                        font-weight: 600;
                        color: #CDDAF1;
                        width: 80%;
                    }
                }

                .number {
                    font-family: 'Roboto', sans-serif;
                    font-style: normal;
                    font-weight: 800;
                    font-size: 100px;
                    line-height: 117px;
                    display: flex;
                    align-items: center;

                    background: linear-gradient(180.2deg, #0AFF06 -5.54%, #50B2FF 99.82%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    text-fill-color: transparent;

                    margin-right: 15px;

                    opacity: 0.7;
                }

                @media(max-width: 991px) {
                    flex-direction: column;

                    .content {
                        .s-title {
                            text-align: center;
                            position: relative;
                            top: -50px;
                            margin-bottom: -30px;
                        }

                        .s-text {
                            width: 100%;
                        }
                    }

                    .step-img {
                        margin-left: unset;
                        margin-top: 15px;
                        width: 100%;
                    }
                }
            }

            .d-title {
                text-transform: uppercase;
                font-weight: 600;
                margin-bottom: 5px;
                text-align: center;
                font-size: 1.25em;
            }

            .d-desc {
                color: #BACBEA;
                text-align: center;
                width: 80%;
                margin: auto;
                margin-bottom: 25px;
            }
        }
    }
</style>
