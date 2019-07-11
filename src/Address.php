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
     * The mapping between field attribute names and internal use names.
     *
     * @var array
     */
    protected $mapping = [
        'givenName'          => ['first_name', 'text-field'],
        'familyName'         => ['last_name', 'text-field'],
        'organization'       => ['organization', 'text-field'],
        'addressLine1'       => ['address_line_1', 'text-field'],
        'addressLine2'       => ['address_line_2', 'text-field'],
        'locality'           => ['locality', 'text-field'],
        'dependentLocality'  => ['dependent_locality', 'text-field'],
        'administrativeArea' => ['administrative_area', 'text-field'],
        'postalCode'         => ['postal_code', 'text-field'],
        'sortingCode'        => ['sorting_code', 'text-field'],
    ];

    /**
     * The internal use fields that should not be used.
     *
     * @var array
     */
    protected $hiddenFields = [
        'organization',
        'givenName',
        'familyName',
        'additionalName',
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
     * Include the first name and last name fields.
     *
     * @return self
     */
    public function withPerson()
    {
        $this->hiddenFields = array_values(array_diff($this->hiddenFields, ['givenName', 'familyName']));

        return $this;
    }

    /**
     * The attribute to use for the country code field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withCountryCodeAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('countryCode', $attribute, $component);
    }

    /**
     * The attribute to use for the administrative area field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withAdministrativeAreaAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('administrativeArea', $attribute, $component);
    }

    /**
     * The attribute to use for the locality field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withLocalityAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('locality', $attribute, $component);
    }

    /**
     * The attribute to use for the dependent locality field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withDependentLocalityAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('dependentLocality', $attribute, $component);
    }

    /**
     * The attribute to use for the postal code field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withPostalCodeAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('postalCode', $attribute, $component);
    }

    /**
     * The attribute to use for the sorting code field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withSortingCodeAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('sortingCode', $attribute, $component);
    }

    /**
     * The attribute to use for the address line 1 field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withAddressLine1As(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('addressLine1', $attribute, $component);
    }

    /**
     * The attribute to use for the address line 2 field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withAddressLine2As(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('addressLine2', $attribute, $component);
    }

    /**
     * The attribute to use for the organisation field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withOrganizationAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('organization', $attribute, $component)->withOrganization();
    }

    /**
     * The attribute to use for the given name field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withGivenNameAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('givenName', $attribute, $component)->withPerson();
    }

    /**
     * The attribute to use for the family name field.
     *
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    public function withFamilyNameAs(string $attribute = null, string $component = null)
    {
		return $this->setFieldAlias('familyName', $attribute, $component)->withPerson();
    }

    /**
     * Set an alias for the field
     * @param  string $field
     * @param  string|null $attribute
     * @param  string|null $component
     *
     * @return self
     */
    protected function setFieldAlias(string $field, $attribute = null, $component = null)
    {
        if ($attribute) {
            $this->mapping[$field][0] = $attribute;
        }

        if ($component) {
            $this->mapping[$field][1] = $component;
        }

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

    protected function fields()
    {
        $repository = app('address.repository');
        $countryCode = 'AU'; //FIXME

        return collect($this->mapping)
            ->only($repository->addressFormatForField($countryCode)['format'])
            ->except($this->hiddenFields)
            ->map(function($mapping, $field) use ($repository, $countryCode) {
            return [
                'attribute' => 'address.'.$field,
                'name'      => $repository->label($field, $countryCode),
                'component' => $mapping[1],
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
        $repository = app('address.repository');
        $countryCode = 'AU'; //FIXME

        return array_merge([
            'fields' => $this->fields(),
            'countryCode' => [
                'attribute'       => 'address.countryCode',
                'component'       => 'select-field',
                'name'            => $repository->label('country'),
                'nullable'        => true,
                'prefixComponent' => true,
                'value'           => $countryCode,
                'options'         => $repository->countries(true)->map(function ($label, $value) {
                    return is_array($label) ? $label + ['value' => $value] : ['label' => $label, 'value' => $value];
                })->values()->all(),
            ]
        ], parent::jsonSerialize());
    }

}
