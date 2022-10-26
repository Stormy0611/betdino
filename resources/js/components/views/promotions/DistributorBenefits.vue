<template>
    <div class="distributorBenefits container">
        <div class="head"></div>
        <div class="description">
            <div class="title" v-html="$t('promotions.benefits.title')"></div>
            <div class="text">{{ $t('promotions.benefits.description') }}</div>
        </div>

        <div class="desc">
            <div class="desc-c">
                <icon icon="fal fa-file-check"></icon>
                <div class="title">{{ $t('promotions.benefits.activity.1') }}</div>
                <div class="text">{{ $t('promotions.benefits.activity.2') }}</div>
            </div>
            <div class="desc-c">
                <icon icon="gift"></icon>
                <div class="title">{{ $t('promotions.benefits.bonus.1') }}</div>
                <div class="text">{{ $t('promotions.benefits.bonus.2') }}</div>
            </div>
        </div>

        <div class="table-view">
            <div class="title">{{ $t('promotions.benefits.table.title') }}</div>
            <div class="t">
                <div class="t-h">
                    <div>{{ $t('promotions.benefits.table.active') }}</div>
                    <div>{{ $t('promotions.benefits.table.bonus') }}</div>
                </div>
                <div class="t-r">
                    <div>5</div>
                    <div>R$6</div>
                </div>
                <div class="t-r">
                    <div>20</div>
                    <div>R$26</div>
                </div>
                <div class="t-r">
                    <div>100</div>
                    <div>R$80</div>
                </div>
                <div class="t-r">
                    <div>500</div>
                    <div>R$188</div>
                </div>
                <div class="t-r">
                    <div>1000</div>
                    <div>R$1288</div>
                </div>
                <div class="t-r">
                    <div>5000</div>
                    <div>R$13,888</div>
                </div>
                <div class="t-r">
                    <div>50000</div>
                    <div>R$188,888</div>
                </div>
            </div>
        </div>

        <div class="desc-2" v-html="$t('promotions.benefits.table.desc')"></div>

        <div class="desc">
            <div class="desc-c">
                <icon icon="fal fa-bell"></icon>
                <div class="title">{{ $t('promotions.benefits.table.reminder.1') }}</div>
                <div class="text">{{ $t('promotions.benefits.table.reminder.2') }}</div>
            </div>
            <div class="desc-c">
                <icon icon="fal fa-info-circle"></icon>
                <div class="title">{{ $t('promotions.benefits.table.details.1') }}</div>
                <div class="text">{{ $t('promotions.benefits.table.details.2') }}</div>
            </div>
        </div>

        <div class="desc single">
            <div class="desc-c">
                <icon icon="fal fa-file-check"></icon>
                <div class="title">{{ $t('promotions.benefits.guide.1') }}</div>
                <div class="text">{{ $t('promotions.benefits.guide.2') }}</div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .distributorBenefits {
        span {
            color: #43BB41;
        }

        .desc {
            display: flex;
            margin-bottom: 25px;

            &.single {
                @media(min-width: 991px) {
                    .desc-c {
                        width: 50%;
                        margin: auto;
                    }
                }
            }

            @media(max-width: 991px) {
                flex-direction: column;

                .desc-c {
                    margin-bottom: 25px;
                }
            }

            .desc-c {
                width: 100%;
                display: flex;
                text-align: center;
                align-items: center;
                justify-content: center;
                flex-direction: column;

                i, svg {
                    color: #43BB41;
                    font-size: 2em;
                    margin: auto;
                    margin-bottom: 15px;
                }

                .title {
                    text-transform: uppercase;
                    font-size: 1.1em;
                    font-weight: 600;
                }

                .text {
                    font-weight: 600;
                    color: #90A3C7;
                    width: 80%;
                }
            }
        }

        .desc-2 {
            text-align: center;
            margin: auto;
            margin-top: 35px;
            margin-bottom: 35px;
            font-weight: 600;
            width: 75%;
        }

        .description {
            position: relative;
            margin-top: 50px;
            margin-bottom: 50px;

            .title {
                font-weight: 600;
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            .text {
                color: #90A3C7;
                font-weight: 600;
                width: 50%;
            }


            @media(max-width: 991px) {
                margin-top: 25px;
                margin-bottom: 25px;

                .text {
                    width: 100%;
                }
            }
        }

        .table-view {
            text-align: center;
            margin-top: 50px;

            .title {
                font-weight: 700;
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            .text {
                font-weight: 600;
                font-size: 1.1em;
                color: #90A3C7;
            }

            .t {
                margin-top: 15px;
                font-weight: 600;
                border-radius: 10px;

                .t-h, .t-r {
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    div {
                        width: 50%;
                        padding: 15px;
                    }
                }

                .t-h {
                    background: #3B5884;
                    border-top-left-radius: 10px;
                    border-top-right-radius: 10px;
                }

                .t-r {
                    background: #1B2431;
                    margin-bottom: 2px;

                    div:last-child {
                        color: #43BB41;
                    }
                }
            }
        }

        .head {
            height: 200px;
            width: 100%;
            background: url('/img/misc/promotions/headers/d-b.png') no-repeat 10% center;
            background-size: cover;
            margin-top: 15px;
            margin-bottom: 15px;
            border-radius: 10px;

            @media(max-width: 991px) {
                height: 100px;
            }
        }
    }
</style>
