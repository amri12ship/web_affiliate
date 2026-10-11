<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Seeded Admin Account
    |--------------------------------------------------------------------------
    |
    | These values are used by the database seeder to create the initial
    | administrator account. Override them in production so the default
    | credentials are never used on a live environment.
    |
    */

    'name' => env('ADMIN_NAME', 'Admin'),

    'email' => env('ADMIN_EMAIL', 'admin@affiliate.test'),

    'password' => env('ADMIN_PASSWORD', 'password'),

];
