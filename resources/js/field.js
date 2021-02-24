import field from "./store/field";

Nova.booting((Vue, router, store) => {
    Vue.component('index-translation-field', require('./components/Field/IndexField'))
    Vue.component('detail-translation-field', require('./components/Field/DetailField'))
    Vue.component('form-translation-field', require('./components/Field/FormField'))

    store.registerModule('/', field);
})
