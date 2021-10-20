let Main = {
    data() {
        return {
            order: order,
            loading: false
        };
    },
    computed: {},
    mounted() {},
    methods: {
        goBack: function goBack() {
            window.history.go(-1);
        },
        handleUpdate(verify) {
            this.order.status = verify;
            axios
                .patch('/traders/payment/' + this.order.id, this.order)
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
                    }).then(() => {
                        window.history.go(-1);
                    });
                })
                .catch(error => {
                    swal.fire({
                        // title: error.response.statusText,
                        title: Lang.get('contents.traders.payment.trader_payment_update_fail'),
                        type: 'error'
                    }).then(() => {
                        window.history.go(-1);
                    });
                });
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
