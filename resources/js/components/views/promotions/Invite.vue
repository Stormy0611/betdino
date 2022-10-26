<template>
    <div class="invite container">
        <div class="head"></div>

        <div class="description">
            <div class="title" v-html="$t('promotions.invite.title')"></div>
            <div class="text">{{ $t('promotions.invite.description') }}</div>
        </div>

        <div class="description">
            <div class="title">{{ $t('promotions.invite.desc') }}</div>
            <div class="text">{{ $t('promotions.invite.desc2') }}</div>
        </div>

        <div class="graph">
            <div class="image"></div>
            <div class="separator"></div>
            <div class="text">{{ $t('promotions.invite.code') }}</div>
            <div class="btn btn-primary" @click="$router.push('/profile/invite')">{{ $t('promotions.invite.copy') }}</div>
        </div>

        <div class="bloggers">
            <div>{{ $t('promotions.invite.1') }}</div>
            <div>{{ $t('promotions.invite.2') }}</div>
        </div>
        <!-- TODO Support Chat -->
        <div class="btn btn-primary" style="width: fit-content; color: white !important; margin: auto; margin-top: 15px; padding: 15px 20px; border-radius: 15px">{{ $t('promotions.invite.consult') }}</div>
    </div>
</template>

<style lang="scss">
    .invite {
        display: flex;
        flex-direction: column;

        span {
            color: #43BB41;
        }

        .bloggers {
            background: #3A5582;
            padding: 15px;
            margin-top: 25px;
            border-radius: 10px;

            div {
                &:first-child {
                    font-weight: 600;
                    margin-bottom: 15px;
                }
            }
        }

        .description {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;

            .title {
                font-size: 1.3em;
                font-weight: 700;
            }

            .text {
                font-weight: 600;
                color: #90A3C7;
                width: 60%;
                margin: auto;
            }
        }

        .head {
            height: 200px;
            width: 100%;
            background: url('/img/misc/promotions/headers/invite.png') no-repeat 10% center;
            background-size: cover;
            margin-top: 15px;
            margin-bottom: 15px;
            border-radius: 10px;

            @media(max-width: 991px) {
                height: 100px;
            }
        }

        .graph {
            background: #293C5B;
            padding: 10px;
            height: fit-content;
            display: flex;
            flex-direction: column;

            .btn {
                padding: 15px 20px;
                color: white !important;
                margin: auto;
                border-radius: 10px;
                margin-top: 15px;
                margin-bottom: 15px;
            }

            .image {
                background: url('/img/misc/promotions/invite-graph.png') no-repeat center;
                height: 500px;
                background-size: contain;
                width: 100%;

                @media(max-width: 991px) {
                    height: 270px;
                }
            }

            .separator {
                height: 2px;
                background: #89A2C8;
                margin-top: 25px;
                margin-bottom: 25px;
            }

            .text {
                text-align: center;
                font-weight: 600;
                font-size: 1.2em;
            }
        }
    }
</style>
