<template>
    <default-field :field="field" :errors="errors" :fullWidthContent="true">
        <template slot="field">
            <div class="flex border-b border-40"
                :class="{ 'remove-bottom-border': field.countryCode.value == '' }"
            >
                <div class="w-1/5 pr-8"
                    :class="{
                        'pb-0': field.countryCode.value == '',
                        'pb-4': field.countryCode.value != ''
                    }"
                >
                        <form-label
                            :label-for="field.countryCode.attribute"
                        >
                        <!-- :class="{ 'mb-2': showHelpText && field.countryCode.helpText }" -->
                            {{ field.countryCode.name }}
                        </form-label>
                </div>
                <div class="w-2/5 pl-8"
                    :class="{
                        'pb-0': field.countryCode.value == '',
                        'pb-4': field.countryCode.value != ''
                    }"
                >
                    <select-control
                        :id="field.countryCode.attribute"
                        :dusk="field.countryCode.attribute"
                        v-model="field.countryCode.value"
                        class="w-full form-control form-select"
                        :options="field.countryCode.options"
                    >
                        <option value="" selected>{{ __('Choose an option') }}</option>
                    </select-control>
                    <!-- :class="errorClasses"
                    :disabled="isReadonly" -->

                    <!-- <help-text class="error-text mt-2 text-danger" v-if="showErrors && hasError">
                        {{ firstError }}
                    </help-text> -->

                    <!-- <help-text class="help-text mt-2" v-if="showHelpText"> {{ field.countryCode.helpText }} </help-text> -->
                </div>
            </div>

            <div class="flex border-b border-40"
                :class="{ 'remove-bottom-border': index == field.fields.length - 1 }"
                v-if="field.countryCode.value != ''"
                v-for="(subfield, index) in field.fields"
            >
                <div class="w-1/5 py-4 pr-8"
                    :class="{ 'pb-0': index == field.fields.length - 1 }"
                >
                        <form-label
                            :label-for="subfield.attribute"
                        >
                        <!-- :class="{ 'mb-2': showHelpText && subfield.helpText }" -->
                            {{ subfield.name }}
                        </form-label>
                </div>
                <div class="w-2/5 py-4 pl-8"
                    :class="{ 'pb-0': index == field.fields.length - 1 }"
                >
                    <input
                        class="w-full form-control form-input form-input-bordered"
                        :id="subfield.attribute"
                        :dusk="subfield.attribute"
                        v-model="subfield.value"
                    />
                    <!-- :disabled="isReadonly" -->
                    <!-- v-bind="extraAttributes" -->
                    <!-- :class="errorClasses"
                    :disabled="isReadonly" -->

                    <!-- <help-text class="error-text mt-2 text-danger" v-if="showErrors && hasError">
                        {{ firstError }}
                    </help-text> -->

                    <!-- <help-text class="help-text mt-2" v-if="showHelpText"> {{ subfield.helpText }} </help-text> -->
                </div>
            </div>

            <!-- <component
                :class="{ 'remove-bottom-border': index == field.fields.length - 1 }"
                v-if="field.countryCode.value != ''"
                v-for="(subfield, index) in field.fields"
                :key="index"
                :is="`form-${subfield.component}`"
                :errors="validationErrors"
                :resource-id="resourceId"
                :resource-name="resourceName"
                :field="subfield"
                :via-resource="viaResource"
                :via-resource-id="viaResourceId"
                :via-relationship="viaRelationship"
            /> -->
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },
}
</script>
