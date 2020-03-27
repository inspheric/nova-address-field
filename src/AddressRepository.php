<?php

namespace Inspheric\Fields;

use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Translation\Translator;
use InvalidArgumentException;
use Inspheric\Fields\Address;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

class AddressRepository
{
    /**
     * @var int[]
     */
    const SUBDIVISION_DEPTHS = [
        AddressField::ADMINISTRATIVE_AREA => 1,
        AddressField::LOCALITY            => 2,
        AddressField::DEPENDENT_LOCALITY  => 3,
    ];

    /**
     * @var string[]
     */
    const FIELD_COMPONENTS = [
        AddressField::ADDRESS_LINE1 => 'textarea',
        AddressField::POSTAL_CODE   => 'small-text',
        AddressField::SORTING_CODE  => 'small-text',
    ];

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var CountryRepositoryInterface
     */
    protected $countries;

    /**
     * @var SubdivisionRepositoryInterface
     */
    protected $subdivisions;

    /**
     * @var AddressFormatRepositoryInterface
     */
    protected $addressFormats;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param string  $locale
     * @param CountryRepositoryInterface  $countries
     * @param SubdivisionRepositoryInterface  $subdivisions
     * @param AddressFormatRepositoryInterface  $addressFormats
     * @param Translator  $translator
     */
    public function __construct(string $locale, CountryRepositoryInterface $countries, SubdivisionRepositoryInterface $subdivisions, AddressFormatRepositoryInterface $addressFormats, Translator $translator)
    {
        $this->locale = $locale;
        $this->countries = $countries;
        $this->subdivisions = $subdivisions;
        $this->addressFormats = $addressFormats;
        $this->translator = $translator;
    }

    /**
     * Get all countries.
     *
     * @param  bool $list
     * @param  string|null  $locale
     *
     * @return Collection|Country[]
     */
    public function countries(bool $list = false, string $locale = null)
    {
        return collect($list ? $this->countries->getList($locale ?: $this->locale) : $this->countries->getAll($locale ?: $this->locale));
    }

    /**
     * Get a country.
     *
     * @param  string $countryCode
     * @param  string|null $locale
     *
     * @return Country|null
     */
    public function country(string $countryCode, string $locale = null)
    {
        return $this->countries->get($countryCode, $locale ?: $this->locale);
    }

    /**
     * Get all subdivisions for a country or parent subdivision(s).
     *
     * @param  string|string[]  $parents
     * @param  bool  $list
     * @param  string|null  $locale
     *
     * @return Collection|Subdivision[]
     */
    public function subdivisions($parents, bool $list = false, string $locale = null)
    {
        $parents = Arr::wrap($parents);

        return collect($list ? $this->subdivisions->getList($parents, $locale ?: $this->locale) : $this->subdivisions->getAll($parents)); // TODO $locale is redundant
    }

    /**
     * Get a subdivision.
     *
     * @param  string $code
     * @param  string|string[]  $parents
     * @param  string|null $locale
     *
     * @return Subdivision|null
     */
    public function subdivision(string $code, $parents, string $locale = null)
    {
        $parents = Arr::wrap($parents);

        return $this->subdivisions->get($code, $parents); // TODO $locale is redundant
    }

    /**
     * Get all address formats.
     *
     * @return Collection|AddressFormat[]
     */
    public function addressFormats()
    {
        return collect($this->addressFormats->getAll());
    }

    /**
     * Get an address format.
     *
     * @param  string $countryCode
     *
     * @return AddressFormat
     */
    public function addressFormat(string $countryCode)
    {
        return $this->addressFormats->get($countryCode);
    }

    /**
     * Get the address format for a given locale.
     *
     * @param  string $countryCode
     * @param  string|null $locale
     *
     * @return array
     */
    public function addressFormatForLocale(string $countryCode, string $locale = null)
    {
        $format = $this->addressFormat($countryCode);

        if ($format->getLocalFormat() && substr($locale ?: $this->locale, 0, 2) == substr($format->getLocale(), 0, 2)) {
            $fields = $format->getLocalFormat();
        } else {
            $fields = $format->getFormat();
        }

        return collect($this->addressFieldsToArray($fields))->map(function ($key) use ($format, $countryCode) {

            if ($attribute = $this->toInternalField($key)) {
                $component = $this->componentForSubfield($key, $format);
                $required = $this->requiredForSubfield($key, $format);
                $label = $this->label($attribute, $countryCode);

                return [
                    'attribute' => $attribute,
                    'component' => $component,
                    'required'  => $required,
                    'label'     => $label,
                ];
            }

            return false;

        })->filter()->keyBy('attribute')->all();
    }

    /**
     * Get the Vue field component for a subfield.
     *
     * @param  string $subfield
     * @param  AddressFormat $format
     * @return string
     */
    public function componentForSubfield(string $subfield, AddressFormat $format)
    {
        if ($component = static::FIELD_COMPONENTS[$subfield] ?? null) {
            return $component;
        }

        if (static::SUBDIVISION_DEPTHS[$subfield] ?? 0 >= $format->getSubdivisionDepth()) {
            return 'select';
        }

        return 'text';
    }

    /**
     * Whether a subfield is required by the format.
     *
     * @param  string $subfield
     * @param  AddressFormat $format
     * @return bool
     */
    public function requiredForSubfield(string $subfield, AddressFormat $format)
    {
        $required = $format->getRequiredFields();

        return in_array($subfield, $required, true);
    }

    /**
     * Get the address format for a given field.
     *
     * @param  string $countryCode
     * @param  Address $field
     * @param  string|null $locale
     *
     * @return array
     */
    public function addressFormatForField(string $countryCode, Address $field, string $locale = null)
    {
        $fields = $this->addressFormatForLocale($countryCode, $locale);

        $hidden = $field->getHiddenFields();

        $fields = collect($fields)->filter(function ($field) use ($hidden) {
            return !in_array($field['attribute'], $hidden, true);
        })->all();

        return [
            // 'country_code' => $countryCode,
            // 'locale'       => $this->locale,
            'fields'        => $fields,
            'country_label' => $this->label('country'),
        ];
    }

    /**
     * Get the address format for displaying the fields.
     *
     * @param  string $countryCode
     * @param  Resource|string|null $resource
     * @param  string|null $attribute
     * @param  string|null $locale
     *
     * @return array
     */
    public function addressFormatForResourceAttribute(string $countryCode, $resource = null, string $attribute = null, string $locale = null)
    {
        $field = $this->getFieldFromResourceAttribute($resource, $attribute);

        return $this->addressFormatForField($countryCode, $field, $locale);
    }

    /**
     * Get the list of fields as an array from the format string.
     *
     * @param  string $formatString
     *
     * @return array
     */
    protected function addressFieldsToArray(string $formatString)
    {
        $expression = '/\%(' . implode('|', AddressField::getAll()) . ')/';

        preg_match_all($expression, $formatString, $foundTokens);

        return $foundTokens[1];
    }

    /**
     * Get the label for an address field.
     *
     * @param  string $field
     * @param  string|null $countryCode
     * @param  string|null $locale
     *
     * @return string
     */
    public function label(string $field, string $countryCode = null, string $locale = null)
    {
        $locale = $locale ?: $this->locale;

        if (is_null($countryCode) && $field == 'country') {
            return $this->translator->get('address-field::fields.country');
        }

        $internalField = $this->toInternalField($field);
        $line = "address-field::fields.$internalField";

        if (!is_null($countryCode)) {

            $format = $this->addressFormat($countryCode, $locale);
            $method = 'get'.ucfirst($this->toExternalField($field)).'Type';

            if (method_exists($format, $method)) {
                $type = $format->$method();

                if (in_array($type, ['zip', 'pin', 'postal'], true)) {
                    $type .= '_code';
                }

                $line = "address-field::fields.$type";
            }
        }

        if ($this->translator->has($line)) {
            return $this->translator->get($line);
        }

        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Rename the field from commerceguys/addressing for internal use.
     *
     * @param  string $field
     *
     * @return string
     */
    public function toInternalField(string $field)
    {
        switch ($field) {
            case 'givenName':
            case 'addressLine2':
                return;
                break;
            case 'addressLine1':
                return 'address_line';
            case 'familyName':
                return 'recipient';
        }

        return Str::snake($field);
    }

    /**
     * Rename all fields from for internal use.
     *
     * @param  string[] $fields
     *
     * @return string[]
     */
    public function toInternalFields(array $fields)
    {
        $internalFields = [];

        foreach ($fields as $field) {
            if ($field = $this->toInternalField($field)) {
                $internalFields[] = $field;
            }
        }

        return $internalFields;
    }

    /**
     * Rename an internal field to commerceguys/addressing for external use.
     *
     * @param  string $field
     *
     * @return string
     */
    public function toExternalField(string $field)
    {
        switch ($field) {
            case 'address_line':
                return 'addressLine1';
            case 'recipient':
                return 'familyName';
        }

        return Str::camel($field);
    }

    /**
     * Get a field definition from a resource and attribute.
     *
     * @param  Resource|string|null $resource
     * @param  string|null $attribute
     *
     * @return Address
     * @throws InvalidArgumentException
     */
    public function getFieldFromResourceAttribute($resource = null, string $attribute = null)
    {
        if ($resource && is_string($resource)) {
            $resource = Nova::resourceInstanceForKey($resource);
        }

        if ($resource instanceof Resource) {
            if ($attribute) {
                if ($field = $resource->availableFields(new NovaRequest())->findFieldByAttribute($attribute)) {
                    return $field;
                }
            }

            if ($field = $resource->availableFields(new NovaRequest())->first(function ($field) {
                return $field instanceof Address;
            })) {
                return $field;
            }
        }

        return new Address('Address', 'address');
    }

    /**
     * Get the select field options.
     *
     * @param  iterable $options
     * @return array
     */
    public function getOptionsList(iterable $options)
    {
        return collect($options)->map(function ($label, $value) {
            return is_array($label) ? $label + ['value' => $value] : ['label' => $label, 'value' => $value];
        })->values()->all();
    }
}
