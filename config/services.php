<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'google' => [
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    'default_location' => [
        'lat' => env('DEFAULT_MAP_LAT', 40.7128), // Fallback to NYC if not set
        'lng' => env('DEFAULT_MAP_LNG', -74.0060),
    ],
],
'mapbox' => [
    'access_token' => env('MAPBOX_ACCESS_TOKEN'),
    'style' => env('MAPBOX_STYLE', 'mapbox://styles/mapbox/streets-v11'),
],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'key' => env('SUPABASE_KEY'),
        'bucket' => env('SUPABASE_BUCKET', 'onlinebucket'),
    ],

];
