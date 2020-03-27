Nova.booting((Vue, router, store) => {
    Vue.config.devtools = true
    Vue.component('index-address-field', require('./components/IndexField'))
    Vue.component('detail-address-field', require('./components/DetailField'))
    Vue.component('form-address-field', require('./components/FormField'))

    Vue.component('form-address-field-text-input', require('./components/TextInput'))
    Vue.component('form-address-field-small-text-input', require('./components/SmallTextInput'))
    Vue.component('form-address-field-textarea-input', require('./components/TextareaInput'))
    Vue.component('form-address-field-select-input', require('./components/SelectInput'))
})
