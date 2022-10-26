<template>
    <div>
        <div class="row page-title">
            <div class="col-md-12">
                <h4 class="mb-1 mt-0">Create User</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-dark">Register</a></h5>
                        <hr>
                        <div class="text-muted">
                            <div class="form-group mt-2">
                                <input v-model="username" type="text" class="form-control" placeholder="Login">
                            </div>
                            <div class="form-group mt-2">
                                <input v-model="email" type="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="form-group mt-2">
                                <input v-model="password" type="password" class="form-control" placeholder="Password">
                            </div>
                            <template v-if="roles">
                                <button class="btn btn-primary" @click="createUser" :disabled="password.length === 0 || email.length === 0 || username.length === 0">Create</button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-dark">Select roles</a></h5>
                        <hr>
                        <div class="text-muted">
                            <template v-if="roles">
                                <div class="form-group mt-2">
                                    <div v-for="role in roles" :key="role.id">
                                        <button class="btn mb-2" :class="pickedRoles.includes(role.id) ? 'btn-danger' : 'btn-primary'" @click="pickedRoles.includes(role.id) ? pickedRoles = pickedRoles.filter(e => e !== role.id) : pickedRoles.push(role.id)">
                                            {{ pickedRoles.includes(role.id) ? 'Remove' : 'Add' }} - {{ role.name }}
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div v-else>Loading...</div>
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
                roles: null,
                username: '',
                email: '',
                password: '',
                pickedRoles: []
            }
        },
        created() {
            axios.post('/admin/roles/all').then(({ data }) => this.roles = data.roles);
        },
        methods: {
            createUser() {
                axios.post('/admin/createUser', {
                    email: this.email,
                    name: this.username,
                    password: this.password,
                    roles: this.pickedRoles
                }).then(({ data }) => {
                    this.$toast.success('Created successfully');
                    this.username = '';
                    this.email = '';
                }).catch((e) => {
                    const errors = e.response.data.errors;
                    Object.keys(errors).forEach(key => {
                        const values = errors[key];
                        switch (key) {
                            case 'email': {
                                values.forEach(value => {
                                    if (value === 'validation.email')
                                        this.$toast.error('Invalid email');
                                    else if (value === 'validation.unique')
                                        this.$toast.error('This email is already registered');
                                });
                                break;
                            }
                            case 'name': {
                                values.forEach(value => {
                                    if (value === 'validation.regex')
                                        this.$toast.error('Login has less than 4 characters or contains invalid symbols');
                                    else if (value === 'validation.unique')
                                        this.$toast.error('This login is already registered, pick something else');
                                });
                                break;
                            }
                            case 'password': {
                                values.forEach(value => {
                                    if (value === 'validation.min.string')
                                        this.$toast.error('Password should have at least 5 characters');
                                });
                                break;
                            }
                        }
                    });
                });
            }
        }
    }
</script>
