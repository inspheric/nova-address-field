<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CountriesController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        return app('address.repository')->countries(true);
    }
}
