<?php

namespace Inspheric\Fields;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;

use CommerceGuys\Addressing\Country\CountryRepository;

use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('address.repository', function () {
            $locale = $this->app->getLocale() ?: 'en';
            $fallbackLocale = $this->app['config']->get('fallback_locale', 'en');

            $countries = new CountryRepository($locale, $fallbackLocale);
            $format = new AddressFormatRepository();
            $subdivisions = new SubdivisionRepository($format);

            return new AddressRepository($locale, $countries, $subdivisions, $format);
        });
    }
}
