<?php

return [

    /*
    |--------------------------------------------------------------------------
    | About page
    |--------------------------------------------------------------------------
    */

    'about' => 'This platform is a digital companion for KM12 — created to support the people who use the space every day.',

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
