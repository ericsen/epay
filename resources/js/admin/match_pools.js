let Main = {
    data() {
        return {
            activeNames: [],
            editPath: '/' + editPathPrefix + '/',
            createPathPrefix: 'store',
            tableData: tableData,
            search: search,
            editTarget: 0,
            loading: false,
            rules: {
                pool_display_name: [
                    {
                        required: true,
                        message: Lang.get('validation.required', {
                            attribute: Lang.get(
                                'validation.attributes.match_pools.pool_display_name'
                            )
                        }),
                        trigger: 'blur'
                    }
                ],
            },
            createRow: {
                pool_display_name: '',
                note: '',
                enable: 'on',
                enable_trans: Lang.get('contents.match_pools.enable_on'),
            },
            row: {
                pool_display_name: '',
                id: 0,
                note: '',
                enable: '',
                enable_trans: ''
            },
            selectOptions: [
                {
                    value: 'on',
                    label: Lang.get('contents.general.enable')
                },
                {
                    value: 'off',
                    label: Lang.get('contents.general.disable')
                }
            ]
        };
    },
    methods: {
        handleEdit(id, row) {
            this.reset();
            this.editTarget = id;
            this.row = _.cloneDeep(row);
        },
        handleCancel() {
            this.reset();
        },
        handleChangeEnableTransForCreate(e) {
            this.createRow.enable_trans = _.find(this.selectOptions, {
                value: e
            }).label;
        },
        handleChangeEnableTransForUpdate(e) {
            this.row.enable_trans = _.find(this.selectOptions, {
                value: e
            }).label;
        },
        handleStore() {
            let o = this;
            let chk = true;
            _.forEach(this.createRow, function(v, k) {
                if (o.createRow[k] == '') {
                    chk = false;
                }
            });
            if (!chk) {
                return false;
            }
            axios
                .post(this.editPath + this.createPathPrefix, this.createRow)
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
                    this.tableData.unshift(response.data.data);
                    this.reset();
                    swal.fire({
                        title: response.data.message,
                        type: 'success'
                    });
                })
                .catch(error => {
                    // console.log(error.response);
                    this.reset();
                    swal.fire({
                        title: error.response.statusText,
                        type: 'error'
                    });
                });
        },
        handleUpdate(rowId) {
            let index = _.findIndex(this.tableData, { id: rowId });
            let o = this;
            let chk = true;
            let message_id = '';

            //前端阻擋，若不寫，需要後端阻檔
            _.forEach(this.row, function(v, k) {
                if (o.row[k] == '') {
                    chk = false;
                    message_id = _.findIndex(o.rules[k], {
                        required: true
                    });
                    swal.fire({
                        title: o.rules[k][message_id]['message'],
                        type: 'warning'
                    });
                    return false;
                }
            });

            if (index >= 0 && chk) {
                axios
                    .patch(this.editPath + rowId, this.row)
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
                        
                        this.tableData.splice(index, 1, this.row);
                        this.reset();
                        swal.fire({
                            title: response.data.message,
                            type: 'success'
                        });
                    })
                    .catch(error => {
                        // console.log(error.response);
                        this.reset();
                        swal.fire({
                            title: error.response.statusText,
                            type: 'error'
                        });
                    });
            }
        },
        handleDelete(rowId) {
            swal.fire({
                title: Lang.get('contents.general.delete_confirm'),
                text: Lang.get('contents.general.delete_confirm_note'),
                type: 'warning',
                showCancelButton: true
                // confirmButtonColor: '#3085d6',
                // cancelButtonColor: '#d33',
                // confirmButtonText: Lang.get('contents.general.confirm'),
                // cancelButtonText: Lang.get('contents.general.cancel'),
            }).then(result => {
                if (result.value) {
                    //true
                    axios
                        .delete(this.editPath + rowId)
                        .then(response => {
                            this.tableData.splice(
                                _.findIndex(this.tableData, { id: rowId }),
                                1
                            );
                            this.reset();
                            swal.fire({
                                title: response.data.message,
                                type: 'success'
                            });
                        })
                        .catch(error => {
                            // console.log(error.response);
                            this.reset();
                            swal.fire({
                                title: error.response.statusText,
                                type: 'error'
                            });
                        });
                }
            });
        },
        handleSortChange(e) {},
        reset() {
            let o = this;
            _.forEach(this.createRow, function(v, k) {
                o.createRow[k] = '';
            });
            o.createRow['enable'] = 'on';
            o.createRow['enable_trans'] = Lang.get(
                'contents.match_pools.enable_on'
            );
            this.editTarget = 0;
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
