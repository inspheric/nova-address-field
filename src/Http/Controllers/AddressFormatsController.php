<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AddressFormatsController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  Request $request
     * @param  string $countryCode
     *
     * @return Response
     */
    public function handle(Request $request, string $countryCode)
    {
        $resource = $request->get('resource');
        $attribute = $request->get('attribute');

        return app('address-field.repository')->addressFormatForResourceAttribute($countryCode, $resource, $attribute);
    }
}
