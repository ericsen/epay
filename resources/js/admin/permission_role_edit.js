let Main = {
    data() {
        return {
            data: all_permissions,
            new_permissions: has_permissions,
            display_name: display_name,
            description: description,
            roleId: role_id,
            loading: false
        };
    },
    computed: {},
    mounted() {},
    methods: {
        goBack() {
            window.history.go(-1);
        },
        handleCancel() {
            this.goBack();
        },
        handleUpdate() {
            // console.info(this.roleId);
            axios
                .patch(
                    '/permissions_roles/' + this.roleId,
                    this.new_permissions
                )
                .then(response => {
                    // console.log(response.data);
                    if (response.data.error) {
                        swal.fire({
                            title: _.join(_.values(response.data.message), '<br />'),
                            type: 'error'
                        });
                        return false;
                    }
                    swal.fire({
                        title: response.data.message,
                        type: 'success'
                    }).then(() => {
                        window.history.go(-1);
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
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
