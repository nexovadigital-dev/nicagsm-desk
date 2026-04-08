<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Partner Token
    |--------------------------------------------------------------------------
    | The unique token assigned by Nexova HQ to this partner installation.
    | Set PARTNER_TOKEN in .env. This token is verified daily against
    | nexovadesk.com to confirm the partner license is still active.
    */
    'token' => env('PARTNER_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | License Verification URL
    |--------------------------------------------------------------------------
    | Base URL of the Nexova SaaS that issues and verifies partner licenses.
    | Do not change unless you know what you are doing.
    */
    'license_url' => env('NEXOVA_LICENSE_URL', 'https://nexovadesk.com'),

    /*
    |--------------------------------------------------------------------------
    | Partner Name
    |--------------------------------------------------------------------------
    | Display name for this partner installation. Used in the UI and emails.
    */
    'name' => env('PARTNER_NAME', 'Nexova Desk'),
];
