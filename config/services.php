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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Orange Money Web Payment API — Guinée (GNF)
    |--------------------------------------------------------------------------
    | Inscription & clés : https://developer.orange.com
    | country : 'GN' pour Guinée Conakry
    | currency : 'GNF' (Franc Guinéen)
    | Les URLs notif/return/cancel doivent être HTTPS en production
    */
    'orange_money' => [
        'client_id'     => env('OM_CLIENT_ID', ''),
        'client_secret' => env('OM_CLIENT_SECRET', ''),
        'merchant_key'  => env('OM_MERCHANT_KEY', ''),
        'base_url'      => env('OM_BASE_URL', 'https://api.orange.com'),
        'country'       => env('OM_COUNTRY', 'GN'),
        'currency'      => env('OM_CURRENCY', 'GNF'),
        // URLs de callback — doivent être HTTPS (obligatoire par Orange)
        'notif_url'     => env('OM_NOTIF_URL'),
        'return_url'    => env('OM_RETURN_URL'),
        'cancel_url'    => env('OM_CANCEL_URL'),
    ],

];
