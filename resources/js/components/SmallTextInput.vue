<template>
    <input
        class="w-1/2 form-control form-input form-input-bordered"
        :id="`${field.attribute}_${subfield.attribute}`"
        :dusk="`${field.attribute}_${subfield.attribute}`"
        v-model="field.value[subfield.attribute]"
        :disabled="disabled"
    />
</template>

<script>
export default {
    props: ['field', 'subfield', 'disabled'],
}
</script>