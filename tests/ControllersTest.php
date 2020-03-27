<?php

namespace Tests;

use Laravel\Nova\Nova;

class ControllersTest extends TestCase
{
    /**
     * @test
     */
    public function the_controller_returns_a_list_of_countries()
    {
        $countries = $this->get('nova-vendor/address-field/countries');

        $countries->assertJsonFragment(['BR' => 'Brazil']);
    }

    /**
     * @test
     */
    public function the_controller_returns_a_list_of_subdivisions_for_a_country()
    {
        $subdivisions = $this->get('nova-vendor/address-field/subdivisions/BR');

        $subdivisions->assertJsonFragment(['RJ' => 'Rio de Janeiro']);
    }

    /**
     * @test
     */
    public function the_controller_returns_a_list_of_subdivisions_for_a_subdivision()
    {
        $subdivisions = $this->get('nova-vendor/address-field/subdivisions/BR/RJ');

        $subdivisions->assertJsonFragment(['Rio de Janeiro' => 'Rio de Janeiro']);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_country()
    {
        $format = $this->get('nova-vendor/address-field/formats/AU');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'administrative_area', 'locality', 'postal_code'
        ]]);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU?resource=contact_addresses&attribute=home_address');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'administrative_area', 'locality', 'postal_code'
        ]]);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_without_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU?resource=contact_addresses');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'administrative_area', 'locality', 'postal_code'
        ]]);
    }

    /**
     * @test
     */
    public function the_controller_returns_a_defualt_address_format_without_resource_or_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'administrative_area', 'locality', 'postal_code'
        ]]);
    }
}
