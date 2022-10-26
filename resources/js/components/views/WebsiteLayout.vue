<template>
    <div class="d-flex" v-if="!$route.fullPath.startsWith('/admin')">
        <preloader></preloader>
        <modal></modal>
        <website-notifications></website-notifications>
        <search></search>
        <div class="wrapper">
            <layout-sidebar></layout-sidebar>
            <div class="pageWrapper">
                <layout-header></layout-header>
                <global-notifications></global-notifications>
                <router-view :key="$route.fullPath" class="pageContent"></router-view>
                <live-feed></live-feed>
                <layout-footer></layout-footer>
            </div>
        </div>
        <chat></chat>
        <bet-slip></bet-slip>
        <floating-buttons></floating-buttons>
        <mobile-menu></mobile-menu>
        <profit-monitoring></profit-monitoring>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import UnavailableInCountryModal from "../modals/UnavailableInCountryModal";
    import BannerModal from "../modals/BannerModal";

    export default {
        computed: {
            ...mapGetters(['country', 'ignoreUnavailableCountryMessage'])
        },
        watch: {
            country() {
                //this.validateCountry();
            }
        },
        mounted() {
            //this.validateCountry();

            BannerModal.methods.open();
        },
        methods: {
            validateCountry() {
                if(this.ignoreUnavailableCountryMessage || !this.country) return;

                const invalidCountries = [
                    'US', 'SG', 'SX', 'BQ', 'AW', 'NL', 'FR', 'CUW', 'AW', 'BES'
                ];

                if(invalidCountries.includes(this.country))
                    UnavailableInCountryModal.methods.open(this.$i18n.t('general.unavailable_country', { country: this.country }),
                        this.$i18n.t('general.not_in_this_country', { country: this.country }));
            }
        }
    }
</script>
