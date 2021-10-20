let Main = {
    data() {
        return {
            tableData: tableData,
            search: '',
            select: '',
            loading: false
        };
    },
    computed: {},
    mounted() {},
    methods: {
        tableRowClassName({ row, rowIndex }) {
            switch (row.status) {
                case 'success':
                    return 'success-row';
                    break;
                case 'pending':
                    return 'warning-row';
                    break;
                case 'fail':
                    return 'danger-row';
                    break;
            }
            return '';
        },
        handleEdit(order_id) {
            window.location.href = '/traders/payment/edit/' + order_id;
        },
        handleShow(order_id) {
            window.location.href = '/traders/payment/show/' + order_id;
        },
        handleUpdate(verify) {

            // axios
            //     .patch(
            //         '/permissions_roles/' + this.roleId,
            //         this.new_permissions
            //     )
            //     .then(response => {
            //         if (response.data.error) {
            //             swal.fire({
            //                 title: _.join(
            //                     _.values(response.data.message),
            //                     '<br />'
            //                 ),
            //                 type: 'error'
            //             });
            //             return false;
            //         }
            //         swal.fire({
            //             title: response.data.message,
            //             type: 'success'
            //         }).then(() => {
            //             window.history.go(-1);
            //         });
            //     })
            //     .catch(error => {
            //         this.reset();
            //         swal.fire({
            //             title: error.response.statusText,
            //             type: 'error'
            //         });
            //     });
        },
        handleSortChange(e) {},
        handleReload() {
            window.location.href = '/traders/payment';
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
