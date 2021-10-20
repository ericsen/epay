let Main = {
    data() {
        return {
            user: user,
            editPath: editPathPrefix,
            search: ''
        };
    },
    mounted() {},
    methods: {
        goBack() {
            window.location.href = '/' + this.editPath;
        },
        handleEdit() {
            window.location.href =
                '/' + this.editPath + '/edit/' + this.user.id;
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
