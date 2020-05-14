<template>
    <div>
        <select-control
            v-if="options.length"
            :id="`${field.attribute}_${subfield.attribute}`"
            :dusk="`${field.attribute}_${subfield.attribute}`"
            v-model="field.value[subfield.attribute]"
            :disabled="disabled"
            class="w-full form-control form-select"
            :options="options"
            @change="changeSelect"
        >
            <option value="" selected>{{ __('Choose an option') }}</option>
        </select-control>
        <input
            v-else
            class="w-full form-control form-input form-input-bordered"
            :id="`${field.attribute}_${subfield.attribute}`"
            :dusk="`${field.attribute}_${subfield.attribute}`"
            v-model="field.value[subfield.attribute]"
            :disabled="disabled"
        />
    </div>
</template>

<script>
export default {
    props: ['field', 'subfield', 'disabled', 'depends'],

    data: () => ({
        options: this.subfield.options ? this.subfield.options : []
    }),

    methods: {
        changeSelect(event) {
            console.log(event.target.value)
        }
    }
}
</script>