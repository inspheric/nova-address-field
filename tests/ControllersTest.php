<?php

namespace Tests;

use Illuminate\Auth\Authenticatable;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaCoreServiceProvider;
use Laravel\Nova\Tests\TestServiceProvider;
use Mockery;

class ControllersTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            NovaCoreServiceProvider::class,
            TestServiceProvider::class,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /**
     * Authenticate as an anonymous user.
     *
     * @return $this
     */
    protected function authenticate()
    {
        $user = Mockery::mock(Authenticatable::class);

        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getKey')->andReturn(1);

        $this->app['auth']->guard(null)->setUser($user);
        $this->app['auth']->shouldUse(null);

        return $this;
    }

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
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ]]);

        $format->assertJsonCount(4, 'fields');
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU?resource=contact_addresses&attribute=home_address');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ]]);

        $format->assertJsonCount(4, 'fields');
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_without_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU?resource=contact_addresses');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ]]);

        $format->assertJsonCount(4, 'fields');
    }

    /**
     * @test
     */
    public function the_controller_returns_a_defualt_address_format_without_resource_or_attribute()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU');

        $format->assertJsonFragment(['fields' => [
            'address_line', 'locality', 'administrative_area', 'postal_code',
        ]]);

        $format->assertJsonCount(4, 'fields');
    }

    /**
     * @test
     */
    public function the_controller_returns_an_address_format_for_a_resource_attribute_with_organization_and_recipient()
    {
        Nova::resources([DefaultResource::class]);

        $format = $this->get('nova-vendor/address-field/formats/AU?resource=contact_addresses&attribute=work_address');

        $format->assertJsonFragment(['fields' => [
            'organization', 'recipient', 'address_line', 'locality', 'administrative_area', 'postal_code',
        ]]);

        $format->assertJsonCount(6, 'fields');
    }
}
