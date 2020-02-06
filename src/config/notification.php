<?php

return [

	// NOTIFICATION PARAMETERS
	'bank-deposit' => true,

	// INTEGRATION PARAMETERS
    'aws' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_REGION'),
    ],
    'ses' => [
        'key'    => env('AWS_SES_KEY'),
        'secret' => env('AWS_SES_SECRET'),
        'region' => env('AWS_REGION'),
    ],
    'twilo' => [
        'sid'    => env('TWILO_SID'),
        'token' => env('TWILO_TOKEN'),
        'whatsapp_number' => env('TWILO_WHATSAPP_NUMBER'),
        'sms_number' => env('TWILO_SMS_NUMBER'),
    ],
];