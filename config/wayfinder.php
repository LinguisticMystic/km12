<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public asset path
    |--------------------------------------------------------------------------
    |
    | URL prefix for wayfinder static files (JSON, SVG, JS). Must not be "wayfinder"
    | because that path is reserved for the Laravel page route.
    |
    */

    'assets_path' => 'assets/wayfinder',

    'floors' => [
        [
            'id' => '1',
            'label' => '1st floor',
            'json' => 'floor-plan.json',
            'image' => '1st-floor.jpg',
        ],
        [
            'id' => '2',
            'label' => '2nd floor',
            'json' => '2nd-floor.json',
            'image' => '2nd-floor.jpg',
        ],
    ],

];
