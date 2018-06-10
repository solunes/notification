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

];