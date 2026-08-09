<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — Gawe-Qi
    |--------------------------------------------------------------------------
    | Frontend SPA berada di domain berbeda dan memakai token Bearer. Izinkan
    | origin frontend (FRONTEND_URL) untuk rute API. Lihat docs/10-deployment.md.
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // Saat FRONTEND_URL kosong (dev), izinkan semua origin agar mudah dicoba.
    'allowed_origins' => array_filter([
        env('FRONTEND_URL'),
        env('APP_ENV') !== 'production' ? '*' : null,
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Token Bearer (bukan cookie), jadi credentials tidak diperlukan.
    'supports_credentials' => false,

];
