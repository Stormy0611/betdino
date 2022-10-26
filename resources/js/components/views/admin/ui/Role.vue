<template>
    <div class="col-xl-3 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5><a href="javascript:void(0)" class="text-dark">{{ role.name }}</a></h5>
                <small>{{ role.id }}</small>
                <hr>
                <div class="text-muted mt-2">
                    <template v-if="!edit">
                        <template v-if="role.permissions.length === 0">Click "Edit" to add permissions to this role</template>
                        <div v-for="permission in role.permissions" v-if="permission.permissions.active">
                            <div>{{ permission.name }}</div>
                            <small>{{ permission.description }}</small>
                            <div class="mt-2">
                                <div v-if="permission.permissions.edit">Can edit</div>
                                <div v-if="permission.permissions.create">Can create</div>
                                <div v-if="permission.permissions.delete">Can delete</div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div v-for="permission in permissions" v-if="permission.id !== '*'">
                            <div>{{ permission.name }}</div>
                            <small>{{ permission.description }}</small>

                            <div class="mt-2">
                                <div>
                                    <div class="custom-control custom-checkbox mb-2 clickable" @click="togglePermission(permission.id, 'active')">
                                        <input :checked="hasPermission(permission.id, 'active')" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label clickable">Allow</label>
                                    </div>
                                </div>
                                <div v-if="permission.isEditable">
                                    <div class="custom-control custom-checkbox mb-2 clickable" @click="togglePermission(permission.id, 'edit')">
                                        <input :checked="hasPermission(permission.id, 'edit')" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label clickable">Edit</label>
                                    </div>
                                </div>
                                <div v-if="permission.isCreatable">
                                    <div class="custom-control custom-checkbox mb-2 clickable" @click="togglePermission(permission.id, 'create')">
                                        <input :checked="hasPermission(permission.id, 'create')" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label clickable">Create</label>
                                    </div>
                                </div>
                                <div v-if="permission.isDeletable">
                                    <div class="custom-control custom-checkbox mb-2 clickable" @click="togglePermission(permission.id, 'delete')">
                                        <input :checked="hasPermission(permission.id, 'delete')" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label clickable">Delete</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="card-body border-top" v-if="!role.readOnly">
                <div class="row align-items-center">
                    <div class="col-sm-auto">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item pr-2">
                                <template v-if="edit">
                                    <a @click="save" href="javascript:void(0)" class="text-muted d-inline-block">
                                        Save
                                    </a>
                                </template>
                                <template v-else>
                                    <a @click="edit = true" href="javascript:void(0)" class="text-muted d-inline-block mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                        Edit
                                    </a>
                                    <a href="javascript:void(0)" class="text-muted d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove"
                                       @click="removeRole(role.id)">
                                        Remove
                                    </a>
                                </template>
                            </li>
                        </ul>
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
                edit: false
           }
        },
        created() {

        },
        methods: {
            hasPermission(id, type) {
                let flag = false;

                this.role.permissions.forEach(permission => {
                    if(permission.id === id) flag = permission.permissions[type];
                });

                return flag;
            },
            togglePermission(id, type) {
                const permission = this.permissions.filter(e => e.id === id)[0];
                let permissions = this.role.permissions;

                const updateServerSide = (roleId, permissionId, type, state) => {
                    axios.post('/admin/roles/update', {
                        roleId: roleId,
                        permissionId: permissionId,
                        type: type,
                        state: state
                    }).catch(() => this.$toast.error('Failed to update role permissions'));
                };

                if(!permissions.filter(e => e.id === id)[0]) {
                    let copy = JSON.parse(JSON.stringify(permission));
                    copy.permissions[type] = !copy.permissions[type];
                    permissions.push(copy);

                    updateServerSide(this.role.id, copy.id, type, copy.permissions[type]);
                } else {
                    const e = permissions.filter(e => e.id === id)[0];
                    e.permissions[type] = !e.permissions[type];

                    updateServerSide(this.role.id, e.id, type, e.permissions[type]);
                }

                this.role.permissions = permissions;
            },
            removeRole(id) {
                axios.post('/admin/roles/remove', {
                    id: id
                }).then(() => this.$router.go());
            },
            save() {
                this.edit = false;
            }
        },
        props: ['role', 'permissions']
    }
</script>
