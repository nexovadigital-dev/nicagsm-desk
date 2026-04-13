<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | IMPORTANTE: El widget se embebe en sitios de terceros, por lo que
    | 'allowed_origins' DEBE permanecer en '*' — es inevitable por diseño.
    | Lo que sí restringimos son los métodos y headers para reducir la superficie.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'widget.js', 'build/*'],

    // Solo los métodos que realmente usa la API pública del widget
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],

    // Abierto — necesario para el widget embebido en sitios de clientes
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Solo los headers que realmente necesita el widget
    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-XSRF-TOKEN',
        'X-Requested-With',
    ],

    'exposed_headers' => [],

    // Cachea el preflight 1 hora — reduce requests OPTIONS innecesarios
    'max_age' => 3600,

    'supports_credentials' => false,

];
