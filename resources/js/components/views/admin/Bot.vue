<template>
    <div>
        <div class="row page-title">
            <div class="col-md-12">
                <div class="float-right" v-if="status !== null">
                    <button class="btn btn-primary" @click="start">{{ status ? 'Stop' : 'Start' }}</button>
                </div>
                <h4 class="mb-1 mt-0">Bot Settings</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settings">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-dark">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            <div class="form-group mt-2">
                                <input @input="change(setting.name, $event.target.value)" :value="setting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                status: null,
                settings: []
            }
        },
        created() {
            axios.post('/admin/bot/status').then(({ data }) => this.status = data.status);
            axios.post('/admin/bot/settings').then(({ data }) => this.settings = data);
        },
        methods: {
            change(key, value) {
                axios.post('/admin/settings/edit', { key: key, value: value.length === 0 ? '0' : value });
            },
            start() {
                axios.post('/admin/bot/start').then(() => {
                    this.$toast.success('Success');
                    this.status = !this.status;
                });
            }
        }
    }
</script>
