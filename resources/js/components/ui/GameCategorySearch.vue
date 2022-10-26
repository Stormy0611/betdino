<template>
    <div class="c-search">
        <div class="search-input" @click="openSearch">
            <icon icon="fal fa-search"></icon>
            <input :placeholder="$t('general.search')">
        </div>
    </div>
</template>

<script>
    import Bus from "../../bus";

    export default {
        methods: {
            openSearch() {
                Bus.$emit('search:toggle');
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/themes";

    .c-search {
        @include themed() {
            display: flex;
            background: t('accentBackground');
            padding: 15px;
            border-radius: 10px;
            color: t('accentText');
            margin-top: 60px;
            align-items: center;
            padding-left: 25px;

            @media(max-width: 1500px) {
                margin-top: 20px;
            }

            @media (max-width: 991px) {
                background: transparent;
                flex-direction: column;
                align-items: unset;
                padding: unset;
                margin-top: 15px;

                .search-input {
                    margin-left: unset;
                    margin-top: 10px;

                    input {
                        background: transparent;
                        border: 1px solid #3A455A;
                    }
                }
            }
        }

        .search-input {
            margin-left: auto;
            position: relative;
            cursor: pointer;

            input {
                border-radius: 10px;
                background: #3A455A;
                padding-left: 40px;
                pointer-events: none;

                &::placeholder {
                    color: #90A3C7;
                }
            }

            svg, i {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                left: 15px;
                margin-top: -1px;
            }
        }
    }
</style>
