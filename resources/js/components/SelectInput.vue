<template>
    <select-control
        :id="`${field.attribute}_${subfield.attribute}`"
        :dusk="`${field.attribute}_${subfield.attribute}`"
        v-model="field.value[subfield.attribute]"
        :disabled="disabled"
        class="w-full form-control form-select"
        :options="[]"
    >
        <!-- @change="updateFormat" -->
        <option value="" selected>{{ __('Choose an option') }}</option>
    </select-control>
</template>

<script>
export default {
    props: ['field', 'subfield', 'disabled'],
}
</script>