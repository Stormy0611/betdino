<template>
    <div class="promotion-bonus-50 container">
        <div class="head"></div>
        <div class="description">
            <div class="title" v-html="$t('promotions.bonus50.title')"></div>
            <div class="text" v-html="$t('promotions.bonus50.description')"></div>
        </div>

        <div class="title">{{ $t('promotions.bonus50.title2') }}</div>
        <div class="b-cards">
            <div class="b-card">
                <div>{{ $t('promotions.bonus50.limit.1') }}</div>
                <div>{{ $t('promotions.bonus50.limit.2') }}</div>
            </div>
            <div class="b-card">
                <div>{{ $t('promotions.bonus50.bonus.1') }}</div>
                <div>{{ $t('promotions.bonus50.bonus.2') }}</div>
            </div>
        </div>

        <div class="description">
            <div class="title-2">{{ $t('promotions.bonus50.rules') }}</div>
            <ul>
                <li>{{ $t('promotions.bonus50.list.1') }}</li>
                <li>{{ $t('promotions.bonus50.list.2') }}</li>
                <li>{{ $t('promotions.bonus50.list.3') }}</li>
                <li>{{ $t('promotions.bonus50.list.4') }}</li>
            </ul>
        </div>
    </div>
</template>

<style lang="scss">
    .promotion-bonus-50 {
        ul {
            li {
                margin-bottom: 10px;
                font-weight: 600;

                &:last-child {
                    margin-bottom: 0;
                }
            }
        }

        .b-cards {
            display: flex;

            @media(max-width: 991px) {
                flex-direction: column;

                .b-card {
                    margin: unset;
                    margin-top: 25px;
                    margin-bottom: 15px;
                    width: 100%;
                }
            }

            .b-card {
                text-align: center;
                width: calc(100% - 20px);
                background: linear-gradient(180deg, #0E1A2C 6.25%, #1C5768 100%);
                border-radius: 20px;
                padding: 50px 0;
                margin: 10px;
                margin-top: 25px;

                div:first-child {
                    font-weight: 600;
                    font-size: 1.2em;
                    text-transform: uppercase;
                    color: #43BB41;
                }

                div:last-child {
                    font-weight: 700;
                    font-size: 1.3em;
                }
            }
        }

        .title {
            text-align: center;
            margin-top: 35px;
            font-size: 1.5em;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .description {
            .title-2 {
                font-weight: 700;
                color: #43BB41;
                font-size: 1.5em;
                margin-top: 25px;
                margin-bottom: 15px;
            }

            .title {
                font-weight: 700;
                font-size: 1.5em;
                margin-top: 25px;

                span {
                    color: #43BB41;
                }
            }

            .text {
                font-weight: 600;
                color: #90A3C7;
            }
        }

        .head {
            height: 200px;
            width: 100%;
            background: url('/img/misc/promotions/headers/bonus50.png') no-repeat 10% center;
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
