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
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
