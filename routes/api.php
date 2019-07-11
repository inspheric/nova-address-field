<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Field API Routes
|--------------------------------------------------------------------------
*/

Route::get('/countries', 'CountriesController@handle');

Route::get('/subdivisions/{countryCode}/{parent1?}/{parent2?}', 'SubdivisionsController@handle');

Route::get('/formats/{country}', 'AddressFormatsController@handle');
