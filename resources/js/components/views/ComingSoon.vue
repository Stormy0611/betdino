<template>
    <div class="comingSoon container">
        <div class="image"></div>
        <div class="text">{{ $t('comingSoon') }}</div>
    </div>
</template>

<style lang="scss">
    .comingSoon {
        .image {
            background: url('/img/misc/coming-soon.png') no-repeat center;
            background-size: contain;
            width: 100%;
            height: 270px;
            margin-top: 25px;
        }

        .text {
            font-size: 48px;
            font-weight: 800;
            font-style: italic;
            text-shadow: 0 4px 4px rgba(102, 244, 55, 0.14);
            color: #38E035;
            text-align: center;
            margin-bottom: 25px;

            @media(max-width: 991px) {
                font-size: 1.4em;
                text-transform: uppercase;
                margin-top: -10px;
            }
        }
    }
</style>
