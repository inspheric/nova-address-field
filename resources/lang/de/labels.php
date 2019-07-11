<?php

return [

    /*
     * General terminology
     */
    'country'        => 'Land',
    'sortingCode'    => 'Sortiercode',
    'addressLine1'   => '1. Adresszeile',
    'addressLine2'   => '2. Adresszeile',
    'organization'   => 'Firma',
    'givenName'      => 'Vorname',
    'additionalName' => 'Zweiter Vorname',
    'familyName'     => 'Nachname',

    /*
     * Terminology for different types
     */
    'administrativeArea' => [
        'area'       => 'Bereich',
        'county'     => 'Bezirk',
        'department' => 'Departement',
        'district'   => 'Kreis',
        'emirate'    => 'Emirat',
        'island'     => 'Insel',
        'parish'     => 'Gemeinde',
        'prefecture' => 'Präfektur',
        'province'   => 'Provinz',
        'state'      => 'Staat',
    ],

    'dependentLocality' => [
        'district'         => 'Kreis',
        'neighborhood'     => 'Gegend',
        'suburb'           => 'Ort',
        'village_township' => 'Dorf',
    ],

    'locality' => [
        'city'      => 'Stadt',
        'district'  => 'Kreis',
        'post_town' => 'Stadt',
        'suburb'    => 'Ort',
    ],

    'postalCode' => [
        'pin'     => 'Postleitzahl',
        'postal'  => 'Postleitzahl',
        'zip'     => 'ZIP-Code',
    ],

    /*
     * Country-specific terminology
     */

    'DE' => [
        'administrativeArea' => [
            'state' => 'Bundesland',
        ],
    ],

    'AT' => [
        'administrativeArea' => [
            'state' => 'Bundesland',
        ],
    ],

];
