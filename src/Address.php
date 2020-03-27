<?php

namespace Inspheric\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * Limited countries.
     *
     * @var string[]
     */
    protected $limitCountries = [];

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
        $this->hiddenFields = array_merge($fields, array_intersect($this->hiddenFields, ['recipient', 'organization']));

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
     * @param string|null $countryCode
     *
     * @return array
     */
    public function getFormat(string $countryCode = null)
    {
        $countryCode = $countryCode ?: $this->getAddressValue('country_code');

        $repository = app('address-field.repository');

        if ($countryCode) {
            return $repository->addressFormatForField($countryCode, $this);
        }

        return [
            // 'country_code' => '',
            // 'locale'       => '',
            'fields'       => [],
            'labels'       => [
                'country_code' => $repository->label('country'),
            ],
        ];
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $repository = app('address-field.repository');

        return array_merge(parent::jsonSerialize(), [
            'countries' => $repository->getOptionsList($this->getCountries()),
            'format' => $this->getFormat(),
            'value' => $this->value ?: [],
        ]);
    }

    /**
     * Get a value from the address.
     *
     * @param  string $attribute
     *
     * @return string
     */
    public function getAddressValue(string $attribute)
    {
        return $this->value[$attribute] ?? null;
    }

    /**
     * Set a value in the address.
     *
     * @param  string $attribute
     * @param  string|null $value
     *
     * @return string
     */
    public function setAddressValue(string $attribute, ?string $value)
    {
        if (!is_array($this->value)) {
            $this->value = [];
        }

        $this->value[$attribute] = $value;

        return $this;
    }

    /**
     * Get the countries used by this field.
     *
     * @return string[]
     */
    public function getCountries()
    {
        $repository = app('address-field.repository');

        $countries = $repository->countries(true)->all();

        if (isset($this->limitCountries['only'])) {
            return Arr::only($countries, $this->limitCountries['only']);
        } elseif (isset($this->limitCountries['except'])) {
            return Arr::except($countries, $this->limitCountries['except']);
        }

        return $countries;
    }

    /**
     * Limit the field to these specific countries only.
     *
     * @param  string[] $countries
     * @return $this
     */
    public function onlyCountries(array $countries)
    {
        $this->limitCountries['only'] = $countries;

        return $this;
    }

    /**
     * Limit the field to specific countries except these.
     *
     * @param  string[] $countries
     * @return $this
     */
    public function exceptCountries(array $countries)
    {
        $this->limitCountries['except'] = $countries;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = json_decode($request[$requestAttribute], true);

            if ($countryCode = $value['country_code'] ?? null) {
                $fields = $this->getFormat($countryCode);
                $fields = $fields['fields'];
                $fields[] = 'country_code';

                $value = Arr::only($value, $fields);
            } else {
                $value = [];
            }

            $model->{$attribute} = $value;
            //TODO Split into separate fields if configured
            //TODO Hydrate from separate fields if configured
        }
    }

}
