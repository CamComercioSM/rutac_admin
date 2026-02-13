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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/auth/google/callback'),
        'maps_key' => env('GOOGLE_MAPS_KEY'),
    ],

    'wati' => [
        'api_url' => env('WATI_API_URL', 'https://api.wati.io'),
        'api_token' => env('WATI_API_TOKEN'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL'),
        'api_token' => env('WHATSAPP_API_TOKEN', env('WATI_API_TOKEN')),
    ],

    // API externa para envío de plantillas WhatsApp (rutac.apisicam.net)
    'whatsapp_templates' => [
        'api_url' => env('WHATSAPP_TEMPLATES_API_URL', 'https://rutac.apisicam.net/enviarPlantillaWhatsAPP'),
    ],

    // API para análisis IA de intervenciones (rutac.apisicam.net)
    'analizar_intervenciones_ia' => [
        'api_url' => env('ANALIZAR_INTERVENCIONES_IA_URL', 'https://rutac.apisicam.net/analizarIntervencionesIA'),
    ],

];
