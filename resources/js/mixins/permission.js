import Vue from 'vue';
import { mapGetters } from 'vuex';

Vue.mixin({
    computed: {
        ...mapGetters(['user', 'isGuest'])
    },
    created() {
        window.$permission = this;
    },
    methods: {
        /**
         * @param permissionId
         * @param checkType active / create / delete / view
         */
        $checkPermission(permissionId, checkType = 'active') {
            if(this.isGuest) return false;
            if(this.user.user.roles.filter(e => e.id === '*').length > 0) return true;

            let flag = false;

            this.user.user.permissions.forEach(role => {
                role.permissions.forEach(permission => {
                    if(!flag && permission.id === permissionId) {
                        flag = permission.permissions[checkType];
                    }
                });
            });

            return flag;
        }
    }
});
