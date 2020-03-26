<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CountriesController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        return app('address-field.repository')->countries(true);
    }
}
