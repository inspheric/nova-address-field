<?php

namespace Inspheric\Fields;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

use Illuminate\Support\Arr;

class AddressRepository
{
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
    protected $format;

    /**
     * @var string
     */
    protected $locale;

    public function __construct(string $locale, CountryRepository $countries, SubdivisionRepository $subdivisions, AddressFormatRepository $format)
    {
        $this->locale = $locale;
        $this->countries = $countries;
        $this->subdivision = $subdivision;
        $this->format = $format;
    }

    /**
     * Get all countries.
     * @param  bool $list
     * @param  string|null  $locale
     * @return \CommerceGuys\Addressing\Country\Country[]
     */
    public function countries(bool $list = false, string $locale = null)
    {
        return $list ? $this->countries->getList($locale) : $this->countries->getAll($locale);
    }

    /**
     * Get a country.
     * @param  string $countryCode
     * @param  string|null $locale
     * @return \CommerceGuys\Addressing\Country\Country|null
     */
    public function country(string $countryCode, string $locale = null)
    {
        return $this->countries->get($countryCode, $locale);
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

        return $list ? $this->subdivisions->getList($parents, $locale) : $this->subdivisions->getAll($parents); // TODO $locale is redundant
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

}
