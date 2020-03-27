<?php

namespace Tests;

use Illuminate\Support\Arr;
use Inspheric\Fields\Address;
use Inspheric\Fields\AddressRepository;
use Laravel\Nova\Nova;

class AddressRepositoryTest extends TestCase
{
    /**
     * Address Repository
     *
     * @var AddressRepository
     */
    protected $repository;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app('address-field.repository');
    }

    /**
     * @test
     */
    public function it_retrieves_a_list_of_countries()
    {
        $countries = $this->repository->countries(true);

        $this->assertArrayHasKey('BR', $countries);
    }

    /**
     * @test
     */
    public function it_retrieves_a_country()
    {
        $country = $this->repository->country('BR');

        $this->assertObjectHasAttribute('countryCode', $country);
        $this->assertEquals($country->getCountryCode(), 'BR');
    }

    /**
     * @test
     */
    public function it_retrieves_a_list_of_subdivisions_for_a_country()
    {
        $subdivisions = $this->repository->subdivisions('BR', true);

        $this->assertArrayHasKey('RJ', $subdivisions);
    }

    /**
     * @test
     */
    public function it_retrieves_a_list_of_subdivisions_for_a_subdivision()
    {
        $subdivisions = $this->repository->subdivisions(['BR', 'RJ'], true);

        $this->assertArrayHasKey('Rio de Janeiro', $subdivisions);
    }

    /**
     * @test
     */
    public function it_retrieves_a_subdivision()
    {
        $subdivision = $this->repository->subdivision('RJ', 'BR');

        $this->assertObjectHasAttribute('name', $subdivision);
        $this->assertEquals($subdivision->getName(), 'Rio de Janeiro');
    }

    /**
     * @test
     */
    public function it_retrieves_all_address_formats()
    {
        $formats = $this->repository->addressFormats();

        $this->assertArrayHasKey('AU', $formats);
    }

    /**
     * @test
     */
    public function it_retrieves_an_address_format_for_a_country()
    {
        $format = $this->repository->addressFormat('CN');

        $this->assertObjectHasAttribute('localFormat', $format);

        $localFormat = "%postalCode\n%administrativeArea%locality%dependentLocality\n%addressLine1\n%addressLine2\n%organization\n%familyName %givenName";

        $this->assertEquals($format->getLocalFormat(), $localFormat);
    }

    /**
     * @test
     */
    public function it_retrieves_an_address_format_for_a_field()
    {
        $field = Address::make('Home Address', 'home_address');

        $format = $this->repository->addressFormatForField('CN', $field);

        $this->assertArrayHasKey('fields', $format);
        $this->assertArrayHasKey('country_label', $format);

        $this->assertArrayContainsFragment(['attribute' => 'dependent_locality'], $format['fields']);
        $this->assertArrayNotContainsFragment(['attribute' => 'recipient'], $format['fields']);

        $field = Address::make('Home Address', 'home_address')->withRecipient();

        $format = $this->repository->addressFormatForField('CN', $field);

        $this->assertArrayContainsFragment(['attribute' => 'recipient'], $format['fields']);
    }

    /**
     * @test
     */
    public function it_retrieves_a_local_address_format_for_a_field()
    {
        $field = Address::make('Home Address', 'home_address');

        $format = $this->repository->addressFormatForField('CN', $field);

        $this->assertEquals('address_line', head($format['fields'])['attribute']);

        $localFormat = $this->repository->addressFormatForField('CN', $field, 'zh');

        $this->assertEquals('address_line', last($localFormat['fields'])['attribute']);
    }

    /**
     * @test
     */
    public function it_retrieves_an_address_format_for_a_resource_attribute()
    {
        $resource = new DefaultResource();

        $format = $this->repository->addressFormatForResourceAttribute('CN', $resource, 'home_address');

        $this->assertArrayNotContainsFragment(['attribute' => 'organization'], $format['fields']);

        $format = $this->repository->addressFormatForResourceAttribute('CN', $resource, 'work_address');

        $this->assertArrayContainsFragment(['attribute' => 'organization'], $format['fields']);
    }

    /**
     * @test
     */
    public function it_retrieves_a_local_address_format_for_a_resource_attribute()
    {
        $resource = new DefaultResource();

        $format = $this->repository->addressFormatForResourceAttribute('CN', $resource, 'home_address');

        $this->assertEquals('address_line', head($format['fields'])['attribute']);

        $localFormat = $this->repository->addressFormatForResourceAttribute('CN', $resource, 'home_address', 'zh');

        $this->assertEquals('address_line', last($localFormat['fields'])['attribute']);
    }

    /**
     * @test
     */
    public function it_retrieves_an_address_format_for_a_resource_attribute_by_urikey()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->repository->addressFormatForResourceAttribute('CN', 'contact_addresses', 'home_address');

        $this->assertEquals('address_line', head($format['fields'])['attribute']);
    }

    /**
     * @test
     */
    public function it_retrieves_an_address_format_for_a_resource_without_attribute()
    {
        $resource = new DefaultResource();

        $format = $this->repository->addressFormatForResourceAttribute('CN', $resource);

        $this->assertEquals('address_line', head($format['fields'])['attribute']);
    }

    /**
     * @test
     */
    public function it_retrieves_a_default_address_format_without_resource_or_attribute()
    {
        $format = $this->repository->addressFormatForResourceAttribute('CN');

        $this->assertEquals('address_line', head($format['fields'])['attribute']);
    }

    /**
     * Field labels data provider.
     *
     * @return array
     */
    public function fieldLabelsDataProvider()
    {
        return [
            'address_line'     => ['address_line', 'Street Address'],
            'area'             => ['area', 'Area'],
            'city'             => ['city', 'City'],
            'country'          => ['country', 'Country/Region'],
            'county'           => ['county', 'County'],
            'department'       => ['department', 'Department'],
            'district'         => ['district', 'District'],
            'do_si'            => ['do_si', 'Do/Si'],
            'eircode'          => ['eircode', 'Eircode'],
            'emirate'          => ['emirate', 'Emirate'],
            'island'           => ['island', 'Island'],
            'neighborhood'     => ['neighborhood', 'Neighborhood'],
            'oblast'           => ['oblast', 'Oblast'],
            'organization'     => ['organization', 'Organization'],
            'parish'           => ['parish', 'Parish'],
            'pin_code'         => ['pin_code', 'PIN Code'],
            'post_town'        => ['post_town', 'Post Town'],
            'postal_code'      => ['postal_code', 'Postal Code'],
            'prefecture'       => ['prefecture', 'Prefecture'],
            'province'         => ['province', 'Province'],
            'recipient'        => ['recipient', 'Recipient'],
            'sorting_code'     => ['sorting_code', 'Sorting Code'],
            'state'            => ['state', 'State'],
            'suburb'           => ['suburb', 'Suburb'],
            'townland'         => ['townland', 'Townland'],
            'village_township' => ['village_township', 'Village/Township'],
            'zip_code'         => ['zip_code', 'ZIP Code'],
            'unexpected_field' => ['unexpected_field', 'Unexpected Field'],
        ];
    }

    /**
     * @test
     * @dataProvider fieldLabelsDataProvider
     */
    public function it_retrieves_labels_for_all_fields(string $field, string $expected)
    {
        $label = $this->repository->label($field);

        $this->assertEquals($expected, $label);
    }

    /**
     * @test
     */
    public function it_retrieves_the_option_list()
    {
        $countries = $this->repository->countries(true);

        $countries = $this->repository->getOptionsList($countries);

        $this->assertContains([
            'label' => 'Zambia',
            'value' => 'ZM',
        ], $countries);
    }
}
