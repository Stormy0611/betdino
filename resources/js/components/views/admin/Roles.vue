<template>
    <div>
        <div class="row page-title">
            <div class="col-md-12">
                <div class="float-right">
                    <button class="btn btn-primary" @click="createRole">Create role</button>
                </div>
                <h4 class="mb-1 mt-0">Roles</h4>
            </div>
        </div>
        <div class="row" v-if="roles">
            <role v-for="role in roles.roles" :role="role" :permissions="roles.allPermissions" :key="role.id"></role>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                roles: null
            }
        },
        created() {
            axios.post('/admin/roles/all').then(({ data }) => this.roles = data);
        },
        methods: {
            createRole() {
                const name = prompt('Enter role name:');
                if(!name) return;

                axios.post('/admin/roles/new', {
                    name: name,
                    id: name.toLowerCase().replaceAll(' ', '_')
                }).then(() => this.$router.go());
            }
        }
    }
</script>
