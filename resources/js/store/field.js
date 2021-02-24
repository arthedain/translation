export default {
    actions: {
        async fetchTabTranslations({commit}, tab){
            commit('setLoading', true);
            const {data} = await axios.get("/nova-vendor/translation/getByTab/"+tab);
            commit('setTabTranslation', data);
            commit('setLoading', false);
        },
        async updateTranslations({commit, dispatch}, params){
            await axios.post('/nova-vendor/translation/update', params);
            // dispatch('fetchTabTranslations');
        }
    },
    mutations: {
        setLoading: (state, value) => state.loading = value,
        setTabTranslation: (state, {data}) => state.tabTranslation = data,
    },
    state: {
        loading: false,
        tabTranslation: null,
    },
    getters: {
        tabTranslation: state => state.tabTranslation,
        loading: state => state.loading,
    },
}
