<?php

namespace Inspheric\Fields;

use Laravel\Nova\Fields\Field;

class Address extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'address-field';

    /**
     * The internal use fields that should not be used.
     *
     * @var array
     */
    protected $hiddenFields = [
        'organization',
        'recipient',
    ];

    /**
     * Include the organization field.
     *
     * @return self
     */
    public function withOrganization()
    {
        $this->hiddenFields = array_values(array_diff($this->hiddenFields, ['organization']));

        return $this;
    }

    /**
     * Include the recipient field.
     *
     * @return self
     */
    public function withRecipient()
    {
        $this->hiddenFields = array_values(array_diff($this->hiddenFields, ['recipient']));

        return $this;
    }

    /**
     * Get the fields that are hidden.
     *
     * @return array
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * Set the hidden fields.
     *
     * @param array $fields
     *
     * @return self
     */
    public function setHiddenFields(array $fields)
    {
        $this->hiddenFields = $fields;

        return $this;
    }

    /**
     * Hide a field.
     *
     * @param  string $field
     *
     * @return self
     */
    public function hideField(string $field)
    {
        $this->hiddenFields[] = $field;

        return $this;
    }

    /**
     * Get the fields to be shown.
     *
     * @return array
     */
    protected function fields()
    {
        $repository = app('address-field.repository');

        return collect($repository->addressFormatForField($this->getAddressValue('country_code'))['format'])
            ->except($this->hiddenFields)
            ->map(function($field) use ($repository, $countryCode) {
            return [
                'attribute' => 'address_'.$field,
                'name'      => $repository->label($field, $countryCode),
            ];
        })->values();
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $repository = app('address-field.repository');

        return array_merge([
            'fields' => $this->fields(),
            'country_code' => [
                'attribute'       => $this->attribute.'_country_code',
                'name'            => $repository->label('country'),
                'value'           => $this->getAddressValue('country_code'),
                'options'         => $repository->countries(true)->map(function ($label, $value) {
                    return is_array($label) ? $label + ['value' => $value] : ['label' => $label, 'value' => $value];
                })->values()->all(),
            ]
        ], parent::jsonSerialize());
    }

    /**
     * Get a value from the address.
     * @param  string $attribute
     * @return string
     */
    protected function getAddressValue(string $attribute)
    {
        return 'AU'; //FIXME
    }

}
