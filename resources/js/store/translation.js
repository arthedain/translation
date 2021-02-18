export default {
    actions: {
        async fetchTranslations({commit}){
            commit('setLoading', true);
            const {data} = await axios.get("/nova-vendor/translation/get");
            commit('setTranslation', data);
            commit('setLoading', false);
        },
    },
    mutations: {
        setLoading: (state, value) => state.loading = value,
        setTranslation: (state, {data}) => state.translation = data,
    },
    state: {
        loading: false,
        translation: [],
    },
    getters: {
        translation: state => state.translation,
        loading: state => state.loading,
    },

}
