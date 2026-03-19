<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | AfterShip API Key
    |--------------------------------------------------------------------------
    |
    | Your AfterShip API key used for authenticating requests.
    |
    */
    'api_key' => env('AFTERSHIP_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | The driver used for communicating with the AfterShip API.
    | Supported: "sdk", "http"
    |
    */
    'driver' => env('AFTERSHIP_DRIVER', 'sdk'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the AfterShip Tracking API.
    |
    */
    'base_url' => env('AFTERSHIP_BASE_URL', 'https://api.aftership.com'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The API version prefix used by the HTTP driver.
    | The SDK driver ignores this as it handles versioning internally.
    |
    */
    'api_version' => env('AFTERSHIP_API_VERSION', 'tracking/2026-01'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | The request timeout in seconds.
    |
    */
    'timeout' => env('AFTERSHIP_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The webhook signing secret used to verify incoming webhook payloads.
    |
    */
    'webhook_secret' => env('AFTERSHIP_WEBHOOK_SECRET', ''),

];
