<?php

namespace Tests;

use Illuminate\Http\Request;
use Inspheric\Fields\Http\Controllers\AddressFormatsController;
use Inspheric\Fields\Http\Controllers\CountriesController;
use Inspheric\Fields\Http\Controllers\SubdivisionsController;
use Laravel\Nova\Nova;

class ControllersTest extends TestCase
{
    protected $requestParams = [];

    public function mockGet(string $controller, ...$params)
    {
        $request = new Request();

        $return = (new $controller)->handle($request->merge($this->requestParams), ...$params);

        return collect($return)->all();
    }

    /**
     * @test
     */
    public function the_controller_returns_a_list_of_countries()
    {
        // 'nova-vendor/address-field/countries'
        $countries = $this->mockGet(CountriesController::class);

        $this->assertArrayHasKey('BR', $countries);
        $this->assertSame('Brazil', $countries['BR']);
    }

    /**
     * @test
     */
    public function the_controller_returns_a_list_of_subdivisions_for_a_country()
    {
        // 'nova-vendor/address-field/subdivisions/BR'
        $subdivisions = $this->mockGet(SubdivisionsController::class, 'BR');

        $this->assertArrayHasKey('RJ', $subdivisions);
        $this->assertSame('Rio de Janeiro', $subdivisions['RJ']);
    }

    /**
     * @test
     */
    public function the_controller_returns_a_list_of_subdivisions_for_a_subdivision()
    {
        // 'nova-vendor/address-field/subdivisions/BR/RJ'
        $subdivisions = $this->mockGet(SubdivisionsController::class, 'BR', 'RJ');

        $this->assertArrayHasKey('Rio de Janeiro', $subdivisions);
        $this->assertSame('Rio de Janeiro', $subdivisions['Rio de Janeiro']);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_country()
    {
        // 'nova-vendor/address-field/formats/AU'
        $format = $this->mockGet(AddressFormatsController::class, 'AU');

        $fields = [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ];

        foreach ($fields as $field) {
            $this->assertArrayContainsFragment(['attribute' => $field], $format['fields']);
        }

        $this->assertCount(4, $format['fields']);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_attribute()
    {
        Nova::resources([DefaultResource::class]);

        // 'nova-vendor/address-field/formats/AU?resource=contact_addresses&attribute=home_address'
        $this->requestParams = [
            'resource'  => 'contact_addresses',
            'attribute' => 'home_address',
        ];

        $format = $this->mockGet(AddressFormatsController::class, 'AU');

        $fields = [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ];

        foreach ($fields as $field) {
            $this->assertArrayContainsFragment(['attribute' => $field], $format['fields']);
        }

        $this->assertCount(4, $format['fields']);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_without_attribute()
    {
        Nova::resources([DefaultResource::class]);

        // 'nova-vendor/address-field/formats/AU?resource=contact_addresses'
        $this->requestParams = [
            'resource'  => 'contact_addresses',
        ];

        $format = $this->mockGet(AddressFormatsController::class, 'AU');

        $fields = [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ];

        foreach ($fields as $field) {
            $this->assertArrayContainsFragment(['attribute' => $field], $format['fields']);
        }

        $this->assertCount(4, $format['fields']);
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_attribute_with_organization_and_recipient()
    {
        Nova::resources([DefaultResource::class]);

        // 'nova-vendor/address-field/formats/AU?resource=contact_addresses&attribute=work_address'
        $this->requestParams = [
            'resource'  => 'contact_addresses',
            'attribute' => 'work_address',
        ];

        $format = $this->mockGet(AddressFormatsController::class, 'AU');

        $fields = [
            'organization', 'recipient', 'address_line', 'locality', 'administrative_area', 'postal_code',
        ];

        foreach ($fields as $field) {
            $this->assertArrayContainsFragment(['attribute' => $field], $format['fields']);
        }

        $this->assertCount(6, $format['fields']);
    }
}
