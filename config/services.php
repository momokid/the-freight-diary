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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'groq' => [
        'key'   => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.2-90b-vision-preview'),
    ],

    'google_ai' => [
        'key'   => env('GOOGLE_AI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    ],

    'manifest' => [
        'hbl_prefix' => env('MANIFEST_HBL_PREFIX', 'PSIL'),
    ],

    'arkesel' => [
        'api_key'   => env('ARKESEL_API_KEY'),
        'sender_id' => env('ARKESEL_SENDER_ID', 'PSIL'),
        'sms_url'   => env('ARKESEL_SMS_URL'),
        'sandbox'   => env('ARKESEL_SANDBOX', false),
    ],

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'model'    => env('OLLAMA_MODEL', 'moondream'),
    ],

    'bl_parser' => [
        'provider' => env('BL_PARSER_PROVIDER', 'claude'),
    ],

    'anthropic' => [
        'key'   => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-haiku-4-5'),
    ],
];
