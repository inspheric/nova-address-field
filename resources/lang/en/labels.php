<?php

return [

    /*
     * General terminology
     */
    'country'        => 'Country',
    'sortingCode'    => 'Sorting Code',
    'addressLine1'   => 'Address Line 1',
    'addressLine2'   => 'Address Line 2',
    'organization'   => 'Organization',
    'givenName'      => 'First Name',
    'additionalName' => 'Middle Name',
    'familyName'     => 'Last Name',

    /*
     * Terminology for different types
     */
    'administrativeArea' => [
        'area'       => 'Area',
        'county'     => 'County',
        'department' => 'Department',
        'district'   => 'District',
        'do_si'      => 'Do/Si',
        'emirate'    => 'Emirate',
        'island'     => 'Island',
        'oblast'     => 'Oblast',
        'parish'     => 'Parish',
        'prefecture' => 'Prefecture',
        'province'   => 'Province',
        'state'      => 'State',
    ],

    'dependentLocality' => [
        'district'         => 'District',
        'neighborhood'     => 'Neighborhood',
        'suburb'           => 'Suburb',
        'townland'         => 'Townland',
        'village_township' => 'Village/Township',
    ],

    'locality' => [
        'city'      => 'City',
        'district'  => 'District',
        'post_town' => 'Post Town',
        'suburb'    => 'Suburb',
    ],

    'postalCode' => [
        'eircode' => 'Eircode',
        'pin'     => 'PIN',
        'postal'  => 'Postal Code',
        'zip'     => 'ZIP Code',
    ],

    /*
     * Country-specific terminology
     */

    'AU' => [
        'postalCode' => [
            'postal'  => 'Postcode',
        ],
    ],

    'GB' => [
        'postalCode' => [
            'postal'  => 'Postcode',
        ],
    ],

];
