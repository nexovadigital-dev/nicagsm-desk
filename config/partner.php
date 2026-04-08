<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License Verification URL (nexovadesk.com)
    | Verifies this installation by domain — no token required.
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
