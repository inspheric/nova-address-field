<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Arr;

use Laravel\Nova\Http\Requests\NovaRequest;

use Laravel\Nova\Nova;

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

        return app('address.repository')->addressFormatForField($countryCode, $resource, $attribute);
    }
}
