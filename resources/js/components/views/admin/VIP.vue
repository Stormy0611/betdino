<template>
    <div>
        <div class="row page-title">
            <div class="col-md-12">
                <h4 class="mb-1 mt-0">VIP</h4>
            </div>
        </div>
        <div class="row" v-if="vip">
            <div class="col-xl-2 col-lg-6" v-for="n in 11" :key="n">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-dark">Level {{ n - 1 }}</a></h5>
                        <hr>
                        <div class="mt-2">
                            <div>Name</div>
                            <input @input="edit(n - 1, 'name', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].name" class="form-control mt-1" type="text" placeholder="Name">
                        </div>
                        <div class="mt-2" v-if="n - 1 !== 0">
                            <div>Deposit requirement</div>
                            <input @input="edit(n - 1, 'depositRequirement', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].depositRequirement" class="form-control mt-1" type="text" placeholder="Deposit requirement ($)">
                        </div>
                        <div class="mt-2" v-if="n - 1 !== 0">
                            <div>Wager requirement</div>
                            <input @input="edit(n - 1, 'wagerRequirement', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].wagerRequirement" class="form-control mt-1" type="text" placeholder="Wager requirement ($)">
                        </div>
                        <div class="mt-2">
                            <div>Number of withdrawals per day</div>
                            <input @input="edit(n - 1, 'numberOfWithdrawals', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].numberOfWithdrawals" class="form-control mt-1" type="text" placeholder="Number of withdrawals per day">
                        </div>
                        <div class="mt-2">
                            <div>Max. withdrawal</div>
                            <input @input="edit(n - 1, 'maxWithdrawal', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].maxWithdrawal" class="form-control mt-1" type="text" placeholder="Max. withdraw ($)">
                        </div>
                        <div class="mt-2">
                            <div>Withdrawal fee (%)</div>
                            <input @input="edit(n - 1, 'withdrawFee', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].withdrawFee" class="form-control mt-1" type="text" placeholder="Withdraw fee (%)">
                        </div>
                        <div class="mt-2" v-if="n - 1 !== 0">
                            <div>One-time bonus after level is reached</div>
                            <input @input="edit(n - 1, 'oneTimeBonus', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].oneTimeBonus" class="form-control mt-1" type="text" placeholder="One-time bonus after level is reached">
                        </div>
                        <div class="mt-2">
                            <div>Invite bonus</div>
                            <input @input="edit(n - 1, 'inviteBonus', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].inviteBonus" class="form-control mt-1" type="text" placeholder="Invite bonus">
                        </div>
                        <div class="mt-2">
                            <div>Referral deposit fee (%)</div>
                            <input @input="edit(n - 1, 'referralDepositFee', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].referralDepositFee" class="form-control mt-1" type="text" placeholder="Referral deposit fee (%)">
                        </div>
                        <div class="mt-2">
                            <div>Monthly free withdrawal amount (R$)</div>
                            <input @input="edit(n - 1, 'monthlyFreeWithdrawalAmount', $event.target.value)" :value="vip.filter(e => e.level === n - 1)[0].monthlyFreeWithdrawalAmount" class="form-control mt-1" type="text" placeholder="Monthly free withdrawal amount">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(['vip'])
        },
        methods: {
            edit(level, key, value) {
                axios.post('/admin/editVIP', {
                    level: level,
                    key: key,
                    value: value
                }).catch(() => this.$toast.error('Save failed'));
            }
        }
    }
</script>

<style lang="scss">

</style>
