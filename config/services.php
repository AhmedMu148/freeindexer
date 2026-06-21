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

  'resend' => [
    'key' => env('RESEND_KEY'),
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

  'freeindexer' => [
    'url' => env('FREEINDEXER_APP_URL'),
    'key' => env('FREEINDEXER_APP_API_KEY'),
  ],

  'cms' => [
    'api_token' => env('CMS_API_TOKEN'),
  ],

  'ticket_system' => [
    'url' => env('TICKET_SYSTEM_URL') ?: ('https://' . env('TICKET_SUPPORT_DOMAIN', 'support.yourwebsite.com')),
    'domain' => env('TICKET_SUPPORT_DOMAIN') ?: parse_url(env('TICKET_SYSTEM_URL', 'https://support.yourwebsite.com'), PHP_URL_HOST),
    'sso_secret' => env('TICKET_SSO_SECRET'),
    'api_key' => env('TICKET_API_KEY'),
    'api_secret' => env('TICKET_API_SECRET'),
  ],

  'central_payment' => [
    'base_url' => env('CENTRAL_PAYMENT_BASE_URL', 'https://billing.flare99.com'),
    'api_key' => env('CENTRAL_PAYMENT_API_KEY'),
    'secret_key' => env('CENTRAL_PAYMENT_SECRET_KEY'),
    'webhook_secret' => env('CP_WEBHOOK_SECRET', env('CENTRAL_PAYMENT_WEBHOOK_SECRET', env('CENTRAL_PAYMENT_SECRET_KEY'))),
    'api_version' => env('CENTRAL_PAYMENT_API_VERSION', 'v1'),
    'timeout' => env('CENTRAL_PAYMENT_TIMEOUT', 30),
    'verify_ssl' => env('CENTRAL_PAYMENT_VERIFY_SSL', true),
  ],

];
