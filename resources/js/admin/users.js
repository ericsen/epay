let Main = {
    data() {
        return {
            users: users,
            editPathPrefix: editPathPrefix,
            search: ''
        };
    },
    mounted() {},
    methods: {
        tableRowClassName({ row, rowIndex }) {
            if (row.inspector_id === null || row.passed_at === null) {
                return 'warning-row';
            }
            if (row.enable === 'off') {
                return 'danger-row';
            }
            return '';
        },
        handleEdit(rowId) {
            window.location.href = this.editPathPrefix + '/edit/' + rowId;
        },
        handleDelete(rowId) {
            // console.info(rowId);
            swal.fire({
                title: Lang.get('contents.general.delete_confirm'),
                text: Lang.get('contents.general.delete_confirm_note'),
                type: 'warning',
                showCancelButton: true
            }).then(result => {
                if (result.value) {
                    //true
                    axios
                        .delete('/accounts/admins/' + rowId)
                        .then(response => {
                            this.users.splice(
                                _.findIndex(this.users, { id: rowId }),
                                1
                            );
                            swal.fire({
                                title: response.data.message,
                                type: 'success'
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
            });
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
