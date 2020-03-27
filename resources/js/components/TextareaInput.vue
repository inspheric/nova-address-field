<template>
    <textarea
        class="w-full form-control form-input form-input-bordered py-3 h-auto min-h-input"
        :id="`${field.attribute}_${subfield.attribute}`"
        :dusk="`${field.attribute}_${subfield.attribute}`"
        v-model="field.value[subfield.attribute]"
        :disabled="disabled"
        rows="3"
      />
    <!-- v-bind="extraAttributes" -->
</template>

<script>
export default {
    props: ['field', 'subfield', 'disabled'],
}
</script>