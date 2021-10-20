let Main = {
    data() {
        return {
            activeNames: [],
            editPath: '/' + editPathPrefix + '/',
            searchPathPrefix: 'search',
            tableData: tableData,
            editTarget: 0,
            loading: false,
            search: search,
        };
    },
    methods: {
        handleSearch() {
            if (this.search == '') {
                return false;
            }
            axios
                .post(this.editPath + this.searchPathPrefix, {search: this.search})
                .then(response => {
                    // console.log(response.data);
                    this.tableData = response.data.data;
                });
        },
        handleEdit(rowId) {
            window.location.href = '/match_pools/mapping/edit/' + rowId;
            return false;
        },
        handleSortChange(e) {},
        reset() {
            let o = this;
            _.forEach(this.createRow, function(v, k) {
                o.createRow[k] = '';
            });
            o.createRow['enable'] = 'on';
            o.createRow['enable_trans'] = Lang.get(
                'contents.match_pools.read.enable_on'
            );
            this.editTarget = 0;
        }
    }
};

let App = Vue.extend(Main);
new App().$mount('#app');
