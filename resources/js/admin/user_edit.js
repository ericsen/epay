let Main = {
    data() {
        let checkPassword = (rule, value, callback) => {
            if (value !== this.newUserData.password) {
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
            user: user,
            parents: parents,
            roles: roles,
            permissions: permissions,
            newUserData: _.cloneDeep(user),
            editPath: '/' + editPathPrefix,
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
                password_confirmation: [
                    {
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
                    // console.info(this.roles[rolesId].permissions);
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
    mounted() {
        let userRoles = _.map(this.user.roles, 'id');
        let userPermissions = _.map(this.user.permissions, 'id');
        this.newUserData.roles = userRoles;
        this.newUserData.permissions = userPermissions;

        // this.userTotalRolePermissions = _.forEach(this.user.roles);
        // _.findIndex(this.tableData, { id: rowId });
    },
    methods: {
        goBack() {
            window.location.href = this.editPath;
        },

        handleClear() {
            this.newUserData.parent_id = '';
        },
        handleShowCheck(check) {
            // let check = check;
            if (check.is_checked === 'yes') {
                swal.fire({
                    title: `<div class="font-success">${Lang.get(
                        'contents.accounts.checked'
                    )}</div>`,
                    html: `${check.qrcode_data}
                    <div class="h4 font-primary">
                    ${check.qrcode_nickname}
                    </div>
                    <p class="text-center">${Lang.get(
                        'contents.accounts.inspector'
                    )}：
                    ${check.inspector.name} <br />${check.checked_at}
                    </p>`,
                    showCloseButton: true,
                    showCancelButton: true,
                    showConfirmButton: false
                });
                return false;
            }
            swal.fire({
                title: Lang.get('contents.accounts.qrcode_confirmation'),
                html: `${check.qrcode_data}
                    <div class="h4 font-primary">
                    ${check.qrcode_nickname}
                    </div>`,
                showCloseButton: true,
                showCancelButton: true
            }).then(result => {
                if (result.value) {
                    // console.info(check);
                    // return false;
                    //發送驗證成功
                    axios
                        .patch(this.editPath + '/check/' + check.id, {
                            is_checked: 'yes'
                        })
                        .then(response => {
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
                            });
                            let checkIndex = _.findIndex(
                                this.newUserData.checks,
                                {
                                    id: check.id
                                }
                            );
                            let responseCheckIndex = _.findIndex(
                                response.data.data,
                                {
                                    id: check.id
                                }
                            );
                            this.newUserData.checks[checkIndex].is_checked =
                                response.data.data[
                                    responseCheckIndex
                                ].is_checked;
                            this.newUserData.checks[checkIndex].inspector_id =
                                response.data.data[
                                    responseCheckIndex
                                ].inspector_id;
                            this.newUserData.checks[checkIndex].inspector =
                                response.data.data[
                                    responseCheckIndex
                                ].inspector;
                            this.newUserData.checks[checkIndex].checked_at =
                                response.data.data[
                                    responseCheckIndex
                                ].checked_at;
                        })
                        .catch(error => {
                            // console.log(error.response);
                            swal.fire({
                                title: error.response.statusText,
                                type: 'error'
                            });
                        });
                }
            });
        },
        handleUpdate() {
            // console.info(this.newUserData);
            let checkFormValidate = false;
            this.$refs['updateUserForm'].validate(valid => {
                if (valid) {
                    checkFormValidate = true;
                }
            });
            if (!checkFormValidate) {
                return false;
            }
            axios
                .patch(this.editPath + '/' + this.user.id, this.newUserData)
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
                        window.location.href = this.editPath;
                    });
                })
                .catch(error => {
                    console.log(error.response);
                    if (error.response.data.errors) {
                        swal.fire({
                            title: _.join(
                                _.values(error.response.data.errors),
                                '<br />'
                            ),
                            type: 'error'
                        });
                    } else {
                        swal.fire({
                            title: error.response.statusText,
                            type: 'error'
                        });
                    }
                });
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
