let Main = {
    data() {
        return {
            editPath: '/' + editPathPrefix + '/',
            //edidPathSuffix: 'edit/',
            edidPathSuffix: '',
            data: available_traders,
            new_traders: has_traderIds,
            display_name: display_name,
            description: description,
            matchPoolId: matchPool_id,
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
            axios
                .patch(
                    this.editPath + this.edidPathSuffix + this.matchPoolId,
                    this.new_traders
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