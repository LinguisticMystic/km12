<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Social & contact links (About page)
    |--------------------------------------------------------------------------
    |
    | Leave a URL empty in .env to hide that row on the About page.
    |
    */

    'social' => [
        [
            'label' => 'Website',
            'url' => env('KM12_SOCIAL_WEBSITE', 'https://km12.lv'),
            'icon' => 'website',
        ],
        [
            'label' => 'Instagram',
            'url' => env('KM12_SOCIAL_INSTAGRAM'),
            'icon' => 'instagram',
        ],
        [
            'label' => 'Facebook',
            'url' => env('KM12_SOCIAL_FACEBOOK'),
            'icon' => 'facebook',
        ],
    ],

];
