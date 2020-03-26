<?php

namespace Inspheric\Fields;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

use Illuminate\Support\Facades\Route;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class AddressFieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('address', __DIR__.'/../dist/js/field.js');
            Nova::style('address', __DIR__.'/../dist/css/field.css');
        });

        $this->app->booted(function () {
            $this->routes();
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'address-field');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/address-field'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('address-field.repository', function () {

            $locale = $this->app->getLocale() ?: 'en';

            $countries = new CountryRepository($locale, 'en');
            $format = new AddressFormatRepository();
            $subdivisions = new SubdivisionRepository($format);
            $translator = app('translator');

            return new AddressRepository($locale, $countries, $subdivisions, $format, $translator);
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware('web') //nova
                ->namespace('Inspheric\\Fields\\Http\\Controllers')
                ->prefix('nova-vendor/address-field')
                ->group(__DIR__.'/../routes/api.php');
    }
}
