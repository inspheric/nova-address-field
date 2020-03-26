<?php

namespace Inspheric\Fields\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inspheric\Fields\AddressFieldServiceProvider;
use Laravel\Nova\Resource;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Inspheric\Fields\Address;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AddressFieldServiceProvider::class
        ];
    }
}

class DefaultResource extends Resource
{
    protected static $model = DefaultModel::class;

    public function __construct()
    {
        parent::__construct(new DefaultModel());
    }

    public function fields(Request $request)
    {
        return [
            Address::make('Home Address', 'home_address'),
        ];
    }

    public static function uriKey()
    {
        return 'contact_addresses';
    }
}

class DefaultModel extends Model
{
    //
}
