<?php

namespace Inspheric\Fields;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;

use Laravel\Nova\Http\Requests\NovaRequest;

use Laravel\Nova\Nova;

class AddressRepository
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \CommerceGuys\Addressing\Country\CountryRepository;
     */
    protected $countries;

    /**
     * @var \CommerceGuys\Addressing\Country\SubdivisionRepository;
     */
    protected $subdivisions;

    /**
     * @var \CommerceGuys\Addressing\Country\AddressFormatRepository;
     */
    protected $addressFormats;

    /**
     * @var \Illuminate\Translation\Translator;
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param string                                                          $locale
     * @param \CommerceGuys\Addressing\Country\CountryRepository              $countries
     * @param \CommerceGuys\Addressing\Subdivision\SubdivisionRepository      $subdivisions
     * @param \CommerceGuys\Addressing\AddressFormat\AddressFormatRepository  $addressFormats
     * @param \Illuminate\Translation\Translator                              $translator
     */
    public function __construct(string $locale, CountryRepository $countries, SubdivisionRepository $subdivisions, AddressFormatRepository $addressFormats, Translator $translator)
    {
        $this->locale = $locale;
        $this->countries = $countries;
        $this->subdivisions = $subdivisions;
        $this->addressFormats = $addressFormats;
        $this->translator = $translator;
    }

    /**
     * Get all countries.
     * @param  bool $list
     * @param  string|null  $locale
     * @return \CommerceGuys\Addressing\Country\Country[]
     */
    public function countries(bool $list = false, string $locale = null)
    {
        return collect($list ? $this->countries->getList($locale ?: $this->locale) : $this->countries->getAll($locale ?: $this->locale));
    }

    /**
     * Get a country.
     * @param  string $countryCode
     * @param  string|null $locale
     * @return \CommerceGuys\Addressing\Country\Country|null
     */
    public function country(string $countryCode, string $locale = null)
    {
        return $this->countries->get($countryCode, $locale ?: $this->locale);
    }

    /**
     * Get all subdivisions.
     * @param  string|string[]  $parents
     * @param  bool  $list
     * @param  string|null  $locale
     * @return \CommerceGuys\Addressing\Subdivision\Subdivision[]
     */
    public function subdivisions($parents, bool $list = false, string $locale = null)
    {
        $parents = Arr::wrap($parents);

        return collect($list ? $this->subdivisions->getList($parents, $locale ?: $this->locale) : $this->subdivisions->getAll($parents)); // TODO $locale is redundant
    }

    /**
     * Get a subdivision.
     * @param  string $code
     * @param  string|string[]  $parents
     * @param  string|null $locale
     * @return \CommerceGuys\Addressing\Subdivision\Subdivision|null
     */
    public function subdivision(string $code, $parents, string $locale = null)
    {
        $parents = Arr::wrap($parents);

        return $this->subdivisions->get($code, $parents); // TODO $locale is redundant
    }

    /**
     * Get all address formats.
     * @return \CommerceGuys\Addressing\AddressFormat\AddressFormat
     */
    public function addressFormats()
    {
        return collect($this->addressFormats->getAll());
    }

    /**
     * Get an address format.
     * @param  string $countryCode
     * @return \CommerceGuys\Addressing\AddressFormat\AddressFormat[]
     */
    public function addressFormat(string $countryCode)
    {
        return $this->addressFormats->get($countryCode);
    }

    /**
     * Get the address format for displaying the fields.
     *
     * @param  string $countryCode
     * @param  string|null $resource
     * @param  string|null $attribute
     *
     * @return array
     */
    public function addressFormatForField(string $countryCode, string $resource = null, string $attribute = null)
    {
        $format = $this->addressFormat($countryCode);

        if ($format->getLocalFormat() && substr($this->locale, 0, 2) == substr($format->getLocale(), 0, 2)) {
            $fields = $format->getLocalFormat();
        }
        else {
            $fields = $format->getFormat();
        }

        $fields = $this->addressFieldsToArray($fields);
        $usedFields = $this->toInternalFields($format->getUsedFields());

        $hidden = [];

        $field = $this->getFieldFromResourceAttribute($resource, $attribute);
        $hidden = $field->getHiddenFields();

        $fields = array_values(array_diff($fields, $hidden));
        $usedFields = array_values(array_diff($usedFields, $hidden));

        $labels = collect($usedFields)->mapWithKeys(function($field) use ($countryCode) {
            return [$field => $this->label($field, $countryCode)];
        });

        return [
            'format'      => $fields,
            'labels'      => $labels,
        ];
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
        $addressFields = [];
        $expression = '/\%(' . implode('|', AddressField::getAll()) . ')/';

        preg_match_all($expression, $formatString, $foundTokens);

        return $this->toInternalFields($foundTokens[1]);
    }

    /**
     * Get the label for a subdivision.
     *
     * @param  string $field
     * @param  string|null $countryCode
     * @param  string|null $locale
     *
     * @return string
     */
    public function label(string $field, string $countryCode = null, $locale = null)
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
     * @param  string|null $uriKey
     * @param  string|null $attribute
     *
     * @return \Laravel\Nova\Fields\Field
     */
    public function getFieldFromResourceAttribute(?string $uriKey, ?string $attribute)
    {
        if ($uriKey && $resource = Nova::resourceInstanceForKey($uriKey)) {
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

        return Address::make('Address', 'address');
    }
}
