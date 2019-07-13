<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubdivisionsController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string[] $parents
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, ...$parents)
    {
        return app('address-field.repository')->subdivisions($parents, true);
    }
}
