<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inspheric\Fields\AddressRepository;

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
        /** @var AddressRepository $repository */
        $repository = app('address-field.repository');

        return $repository->countries(true);
    }
}
