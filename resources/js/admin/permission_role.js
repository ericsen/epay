let Main = {
    data() {
        return {
            activeNames: '',
            tableData: tableData,
            editTarget: 0,
            loading: false
        };
    },
    methods: {
        handleEdit(rowId) {
            window.location.href = '/permissions_roles/edit/' + rowId;
            return false;
        },
        // handleToEmpty(rowId) {
        //     console.info(rowId);
        //     return false;
        //     swal.fire({
        //         title: Lang.get('contents.general.delete_confirm'),
        //         text: Lang.get('contents.general.delete_confirm_note'),
        //         type: 'warning',
        //         showCancelButton: true
        //     }).then(result => {
        //         if (result.value) {
        //             //true
        //             axios
        //                 .delete('/roles/' + rowId)
        //                 .then(response => {
        //                     this.tableData.splice(_.findIndex(this.tableData, { id: rowId }), 1);
        //                     this.reset();
        //                     swal.fire({
        //                         title: response.data.message,
        //                         type: 'success'
        //                     });
        //                 })
        //                 .catch(error => {
        //                     // console.log(error.response);
        //                     this.reset();
        //                     swal.fire({
        //                         title: error.response.statusText,
        //                         type: 'error'
        //                     });
        //                 });
        //         }
        //     });
        // }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
