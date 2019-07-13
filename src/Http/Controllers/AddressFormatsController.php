<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Laravel\Nova\Http\Requests\NovaRequest;

class AddressFormatsController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $countryCode
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, string $countryCode)
    {
        $resource = $request->get('resource');
        $attribute = $request->get('attribute');

        $field = \Inspheric\Fields\Address::make('Address', 'home_address');
        $field->value = ['country_code' => $countryCode];
        return response(json_encode($field, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'text/plain']);

        return app('address-field.repository')->addressFormatForResourceAttribute($countryCode, $resource, $attribute);
    }
}
