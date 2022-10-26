<template>
    <div :class="active ? 'active' : ''" @click="add">
        <slot></slot>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Bus from '../../bus';

    export default {
        computed: {
            ...mapGetters(['betSlip'])
        },
        created() {
            this.hash = this.game.id + this.market.name + this.runner.name;

            Bus.$on('betSlip:clear', () => this.active = false);
            Bus.$on('betSlip:remove:' + this.hash, () => this.active = false);
        },
        props: {
            game: {
                type: Object,
                required: true
            },
            runner: {
                type: Object,
                required: true
            },
            market: {
                type: Object,
                required: true
            },
            category: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                hash: null,
                active: false
            }
        },
        methods: {
            add() {
                if(!this.game.open || !this.market.open || !this.runner.open) return;

                if(!this.betSlip) this.$store.dispatch('toggleBetSlip', true);

                this.active = true;

                let object = new Vue({
                    data: {
                        game: this.game,
                        market: this.market,
                        runner: this.runner,
                        hash: this.hash,
                        category: this.category
                    }
                });

                Bus.$emit('betSlip:add', object);
            }
        }
    }
</script>
