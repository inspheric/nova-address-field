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
    protected function getFormat()
    {
        $repository = app('address-field.repository');
        $countryCode = $this->getAddressValue('country_code');

        if ($countryCode) {
            return [$countryCode => $repository->addressFormatForField($countryCode, $this)];
        }

        return [];
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
            'country_code' => [
                'attribute'       => 'country_code',
                'name'            => $repository->label('country'),
                'options'         => $repository->getOptionsList($repository->countries(true)),
            ],
            'format' => $this->getFormat(),
        ], parent::jsonSerialize());
    }

    /**
     * Get a value from the address.
     * @param  string $attribute
     * @return string
     */
    protected function getAddressValue(string $attribute)
    {
        return $this->value[$attribute] ?? null;
    }

}
