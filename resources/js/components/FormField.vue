<template>
    <default-field :field="field" :errors="errors" :fullWidthContent="true">
        <template slot="field">
            <div class="flex border-b border-40"
                :class="{ 'remove-bottom-border': formatLoaded(false) }"
            >
                <div class="w-1/5 pr-8"
                    :class="{
                        'pb-0': formatLoaded(false),
                        'pb-4': formatLoaded(true)
                    }"
                >
                        <form-label
                            :label-for="`${field.attribute}_country_code`"
                            :class="{ 'mb-2': showHelpText && field.helpText }"
                        >
                            {{ field.format.country_label }}
                        </form-label>
                </div>
                <div class="w-2/5 pl-8"
                    :class="{
                        'pb-0': formatLoaded(false),
                        'pb-4': formatLoaded(true)
                    }"
                >
                    <select-control
                        :id="`${field.attribute}_country_code`"
                        :dusk="`${field.attribute}_country_code`"
                        v-model="field.value.country_code"
                        class="w-full form-control form-select"
                        :options="field.countries"
                        @change="updateFormat"
                        :disabled="isReadonly"
                    >
                        <option value="" selected>{{ __('Choose an option') }}</option>
                    </select-control>
                    <!-- :class="errorClasses"
                     -->

                    <!-- <help-text class="error-text mt-2 text-danger" v-if="showErrors && hasError">
                        {{ firstError }}
                    </help-text> -->

                    <help-text class="help-text mt-2" v-if="showHelpText"> {{ field.helpText }} </help-text>
                </div>
            </div>

            <div v-if="formatLoaded(true)">
                <div class="flex border-b border-40"
                    :class="{ 'remove-bottom-border': index == field.format.fields.length - 1 }"

                    v-for="(subfield, index) in field.format.fields" v-bind:key="index"
                >
                    <div class="w-1/5 py-4 pr-8"
                        :class="{ 'pb-0': index == field.format.fields.length - 1 }"
                    >
                            <form-label
                                :label-for="`${field.attribute}_${subfield.attribute}`"
                            >
                            <!-- :class="{ 'mb-2': showHelpText && subfield.helpText }" -->
                                {{ subfield.label }}
                                <span v-if="subfield.required" class="text-danger text-sm">{{
                                    __('*')
                                }}</span>
                            </form-label>
                    </div>
                    <div class="w-2/5 py-4 pl-8"
                        :class="{ 'pb-0': index == field.format.fields.length - 1 }"
                    >
                        <!-- <input
                            class="w-full form-control form-input form-input-bordered"
                            :id="`${field.attribute}_${subfield.attribute}`"
                            :dusk="`${field.attribute}_${subfield.attribute}`"
                            v-model="field.value[subfield.attribute]"
                            :disabled="isReadonly"
                        /> -->
                        <component :is="`form-address-field-${subfield.component}-input`" :field="field" :subfield="subfield" :disabled="isReadOnly" />
                        <!-- v-bind="extraAttributes" -->
                        <!-- :class="errorClasses" -->

                        <!-- <help-text class="error-text mt-2 text-danger" v-if="showErrors && hasError">
                            {{ firstError }}
                        </help-text> -->

                        <!-- <help-text class="help-text mt-2" v-if="showHelpText"> {{ subfield.helpText }} </help-text> -->
                    </div>
                </div>
            </div>

            <!-- <component
                :class="{ 'remove-bottom-border': index == field.fields.length - 1 }"
                v-if="formatLoaded(false)"
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

        fill(formData) {
            formData.append(this.field.attribute, JSON.stringify(this.value || {}))
        },

        formatLoaded(loaded) {
            return (this.field.format.fields.length == 0) != loaded
        },

        async updateFormat(event) {
            const country_code = event.target.value

            if (country_code) {
                Nova.request()
                    .get(
                        `/nova-vendor/address-field/formats/${country_code}`,
                        {
                            params: {
                                resource: this.resourceName,
                                attribute: this.field.attribute
                            }
                        }
                    )
                    .then(response => {
                        this.field.format = response.data
                    })
            }
            else {
                this.field.format.fields = []
            }

        }
    },
}
</script>
