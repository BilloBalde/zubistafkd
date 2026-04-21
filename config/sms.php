<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pilote d'envoi SMS
    |--------------------------------------------------------------------------
    |
    | log     : enregistre dans storage/logs (aucun SMS réel)
    | http    : POST vers SMS_HTTP_URL (voir ci-dessous)
    | twilio  : API Twilio (variables TWILIO_*)
    |
    */
    'driver' => env('SMS_DRIVER', 'log'),

    /*
    | Afficher le code sur l'écran de vérification (jamais en production).
    */
    'show_code_flash' => env('APP_ENV') !== 'production'
        && filter_var(env('SMS_SHOW_CODE', true), FILTER_VALIDATE_BOOLEAN),

    'otp_message' => env('SMS_OTP_MESSAGE', 'Votre code FBK : %s. Valide 5 minutes.'),

    /*
    | Envoi générique (adapter selon votre opérateur SMS en Guinée / Afrique)
    */
    'http' => [
        'url' => env('SMS_HTTP_URL'),
        'method' => env('SMS_HTTP_METHOD', 'post'),
        'timeout' => 15,
        'headers' => array_filter([
            'Authorization' => env('SMS_HTTP_AUTH_HEADER'),
            'Content-Type' => env('SMS_HTTP_CONTENT_TYPE', 'application/json'),
        ]),
        'query' => [], // optionnel
        'body_template' => [
            'to' => '{{phone}}',
            'message' => '{{message}}',
        ],
    ],

    /*
    | Twilio (https://www.twilio.com/docs/sms)
    */
    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM_NUMBER'),
    ],
];
