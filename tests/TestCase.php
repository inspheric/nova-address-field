<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inspheric\Fields\AddressFieldServiceProvider;
use Laravel\Nova\Resource;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Inspheric\Fields\Address;
use SebastianBergmann\Exporter\Exporter;

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

    protected function assertArrayContainsKeyValue(array $expected, array $array, bool $not = false)
    {
        $filtered = Arr::where($array, function ($item) use ($expected) {
            $found = true;
            foreach ($expected as $key => $value) {
                $found &= isset($item[$key]) && $item[$key] == $value;
            }
            return $found;
        });

        $found = count($filtered) > 0;

        $expectedFormat = (new Exporter())->export($expected);

        if ($not) {
            $this->assertFalse($found, "Failed asserting that the array does not contain $expectedFormat.");
        } else {
            $this->assertTrue($found, "Failed asserting that the array contains $expectedFormat.");
        }
    }

    protected function assertArrayNotContainsKeyValue(array $expected, array $array)
    {
        $this->assertArrayContainsKeyValue($expected, $array, true);
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

            Address::make('Work Address', 'work_address')
                ->withOrganization()
                ->withRecipient(),
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
