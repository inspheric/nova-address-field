Nova.booting((Vue, router, store) => {
    Vue.component('index-address', require('./components/IndexField'))
    Vue.component('detail-address', require('./components/DetailField'))
    Vue.component('form-address', require('./components/FormField'))
})
