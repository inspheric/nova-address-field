<?php

namespace Tests;

use Inspheric\Fields\Address;

class FieldTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_no_format_without_country_code()
    {
        $field = Address::make('Address', 'address');
        $field->setAddressValue('country_code', null);

        $format = $field->getFormat();

        $this->assertEmpty($format['fields']);
    }

    /**
     * @test
     */
    public function it_can_get_a_field_format()
    {
        $field = Address::make('Address', 'address');
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayHasKey('fields', $format);

        $this->assertArrayContainsFragment([
            'attribute' => 'administrative_area',
            'label' => 'State',
        ], $format['fields']);

        $this->assertArrayNotHasKey('recipient', $format['fields']);

        $this->assertArrayNotHasKey('organization', $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_retrieve_default_subfields()
    {
        $field = Address::make('Address', 'address');
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayHasKey('administrative_area', $format['fields']);

        $this->assertCount(4, $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_hide_a_subfield()
    {
        $field = Address::make('Address', 'address')
            ->hideField('administrative_area');
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayNotHasKey('administrative_area', $format['fields']);

        $this->assertCount(3, $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_hide_many_subfields()
    {
        $field = Address::make('Address', 'address')->setHiddenFields(['administrative_area', 'postal_code']);
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayNotHasKey('administrative_area', $format['fields']);
        $this->assertArrayNotHasKey('postal_code', $format['fields']);

        $this->assertCount(2, $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_hide_many_subfields_with_recipient()
    {
        $field = Address::make('Address', 'address')->withRecipient()->setHiddenFields(['administrative_area']);
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayHasKey('recipient', $format['fields']);
        $this->assertArrayNotHasKey('administrative_area', $format['fields']);

        $this->assertCount(4, $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_add_recipient()
    {
        $field = Address::make('Address', 'address')->withRecipient();
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayHasKey('recipient', $format['fields']);
    }

    /**
     * @test
     */
    public function it_can_add_organization()
    {
        $field = Address::make('Address', 'address')->withOrganization();
        $field->setAddressValue('country_code', 'AU');

        $format = $field->getFormat();

        $this->assertArrayHasKey('organization', $format['fields']);
    }

    /**
     * @test
     */
    public function it_returns_format_for_json()
    {
        $field = Address::make('Address', 'address');
        $field->setAddressValue('country_code', 'AU');

        $format = $field->jsonSerialize();

        $this->assertArrayHasKey('countries', $format);
        $this->assertArrayHasKey('format', $format);
    }

    /**
     * @test
     */
    public function it_retrieves_all_countries_by_default()
    {
        $field = Address::make('Address', 'address');

        $countries = $field->getCountries();

        $this->assertArrayHasKey('GB', $countries);
        $this->assertArrayHasKey('US', $countries);
        $this->assertArrayHasKey('NR', $countries);
        $this->assertArrayHasKey('MA', $countries);
    }

    /**
     * @test
     */
    public function it_can_return_only_selected_countries()
    {
        $field = Address::make('Address', 'address')->onlyCountries(['GB', 'US']);

        $countries = $field->getCountries();

        $this->assertCount(2, $countries);

        $this->assertArrayHasKey('GB', $countries);
        $this->assertArrayHasKey('US', $countries);
        $this->assertArrayNotHasKey('NR', $countries);
        $this->assertArrayNotHasKey('MA', $countries);
    }

    /**
     * @test
     */
    public function it_can_return_except_selected_countries()
    {
        $allCountries = count(Address::make('Address', 'address')->getCountries());
        $field = Address::make('Address', 'address')->exceptCountries(['GB', 'US']);

        $countries = $field->getCountries();

        $this->assertCount($allCountries - 2, $countries);

        $this->assertArrayNotHasKey('GB', $countries);
        $this->assertArrayNotHasKey('US', $countries);
        $this->assertArrayHasKey('NR', $countries);
        $this->assertArrayHasKey('MA', $countries);
    }
}
