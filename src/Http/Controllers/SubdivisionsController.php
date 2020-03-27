<?php

namespace Inspheric\Fields\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inspheric\Fields\AddressRepository;

class SubdivisionsController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @param  Request $request
     * @param  string[] $parents
     *
     * @return Response
     */
    public function handle(Request $request, ...$parents)
    {
        /** @var AddressRepository $repository */
        $repository = app('address-field.repository');

        return $repository->subdivisions($parents, true);
    }
}
