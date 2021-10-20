let Main = {
    data() {
        let checkPassword = (rule, value, callback) => {
            if (value === '') {
                callback(
                    new Error(Lang.get('validation.user.password_confirmation'))
                );
            } else if (value !== this.newUserData.password) {
                callback(
                    new Error(
                        Lang.get('validation.user.two_times_password_diff')
                    )
                );
            } else {
                callback();
            }
        };
        return {
            roles: roles,
            permissions: permissions,
            parents: parents,
            newUserData: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                parent_id: '',
                roles: [],
                permissions: [],
                extra_data: extraData,
            },
            createPath: '/' + createPathPrefix,
            rules: {
                name: [
                    {
                        required: true,
                        message: Lang.get('validation.user.name'),
                        trigger: 'blur'
                    }
                ],
                nickname: [
                    {
                        required: true,
                        message: Lang.get('validation.user.nickname'),
                        trigger: 'blur'
                    }
                ],
                email: [
                    {
                        required: true,
                        message: Lang.get('validation.user.email'),
                        trigger: 'blur'
                    },
                    {
                        type: 'email',
                        message: Lang.get('validation.user.email_format_need'),
                        trigger: 'blur'
                    }
                ],
                password: [
                    {
                        required: true,
                        message: Lang.get('validation.user.password'),
                        trigger: 'blur'
                    }
                ],
                password_confirmation: [
                    {
                        required: true,
                        validator: checkPassword,
                        trigger: 'blur'
                    }
                ],
                roles: [
                    {
                        type: 'array',
                        required: true,
                        message: Lang.get('validation.user.one_role_need'),
                        trigger: 'change'
                    }
                ]
            }
        };
    },
    computed: {
        userTotalRolePermissions() {
            let totalRolePermissions = [];
            _.forEach(this.newUserData.roles, element => {
                // console.info('element:' + element);
                let rolesId = _.findIndex(this.roles, { id: element });
                // console.info(this.roles[rolesId]);
                if (rolesId >= 0) {
                    // console.info('rolesId:' + rolesId);
                    totalRolePermissions = _.unionBy(
                        totalRolePermissions,
                        this.roles[rolesId].permissions
                    );
                }
            });
            // console.info(aa);
            totalRolePermissions = _.uniqBy(totalRolePermissions, function(e) {
                return e.id;
            });
            return totalRolePermissions;
        },
        userTotalPermissions() {
            let totalPermissions = [];
            _.forEach(this.newUserData.permissions, element => {
                // console.info('element:' + element);
                let permissionId = _.findIndex(this.permissions, {
                    id: element
                });
                // console.info(this.roles[rolesId]);
                if (permissionId >= 0) {
                    // console.info('rolesId:' + rolesId);
                    totalPermissions = _.unionBy(totalPermissions, [
                        this.permissions[permissionId]
                    ]);
                }
            });
            // console.info(aa);
            totalPermissions = _.uniqBy(totalPermissions, function(e) {
                return e.id;
            });
            return totalPermissions;
        }
    },
    mounted() {},
    methods: {
        goBack() {
            window.location.href = this.createPath;
        },
        handleClear() {
            this.newUserData.parent_id = '';
        },
        handleCreate() {
            // console.info(this.newUserData);
            let checkFormValidate = false;
            this.$refs['createUserForm'].validate(valid => {
                if (valid) {
                    checkFormValidate = true;
                }
            });
            if (!checkFormValidate) {
                return false;
            }
            axios
                .post(this.createPath + '/create', this.newUserData)
                .then(response => {
                    // console.log(response.data);
                    if (response.data.error) {
                        swal.fire({
                            title: _.join(
                                _.values(response.data.message),
                                '<br />'
                            ),
                            type: 'error'
                        });
                        return false;
                    }
                    swal.fire({
                        title: response.data.message,
                        type: 'success'
                    }).then(() => {
                        window.location.href = this.createPath;
                    });
                })
                .catch(error => {
                    // console.log(error.response);
                    swal.fire({
                        title: error.response.statusText,
                        type: 'error'
                    });
                });
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
