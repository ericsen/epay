let Main = {
    data() {
        return {
            activeNames: [],
            tableData: tableData,
            editTarget: 0,
            loading: false,
            rules: {
                name: [
                    {
                        required: true,
                        message: Lang.get('validation.required', {
                            attribute: Lang.get(
                                'validation.attributes.roles.name'
                            )
                        }),
                        trigger: 'blur'
                    }
                ],
                display_name: [
                    {
                        required: true,
                        message: Lang.get('validation.required', {
                            attribute: Lang.get(
                                'validation.attributes.roles.display_name'
                            )
                        }),
                        trigger: 'blur'
                    }
                ],
                description: [
                    {
                        required: true,
                        message: Lang.get('validation.required', {
                            attribute: Lang.get(
                                'validation.attributes.roles.description'
                            )
                        }),
                        trigger: 'blur'
                    }
                ]
            },
            createRow: {
                description: '',
                display_name: '',
                name: '',
                use_for: 'B',
                use_for_trans: Lang.get('contents.general.use_for_back')
            },
            row: {
                description: '',
                display_name: '',
                id: 0,
                name: '',
                use_for: '',
                use_for_trans: ''
            },
            selectOptions: [
                {
                    value: 'B',
                    label: Lang.get('contents.general.use_for_back')
                },
                {
                    value: 'T',
                    label: Lang.get('contents.general.use_for_trader')
                },
                {
                    value: 'A',
                    label: Lang.get('contents.general.use_for_agent')
                },
                {
                    value: 'C',
                    label: Lang.get('contents.general.use_for_customer')
                }
            ]
        };
    },
    methods: {
        handleEdit(id, row) {
            this.reset();
            this.editTarget = id;
            this.row = JSON.parse(JSON.stringify(row));
        },
        handleCancel() {
            this.reset();
        },
        handleChangeUseForTransForCreate(e) {
            this.createRow.use_for_trans = _.find(this.selectOptions, {
                value: e
            }).label;
        },
        handleChangeUseForTransForUpdate(e) {
            this.row.use_for_trans = _.find(this.selectOptions, {
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
                .post('/roles/store', this.createRow)
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
                    .patch('/roles/' + rowId, this.row)
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
                        .delete('/roles/' + rowId)
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
            o.createRow['use_for'] = 'B';
            o.createRow['use_for_trans'] = Lang.get(
                'contents.general.use_for_back'
            );
            this.editTarget = 0;
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
