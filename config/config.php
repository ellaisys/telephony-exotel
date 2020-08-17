<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Exotel Integration
     |--------------------------------------------------------------------------
     |
     */
    'configuration' => [
        /*
         |--------------------------------------------------------------------------
         | Setting the exotel_header_key to check in the values in the header of
         | request sent to the endpoint.
         | 
         | For development purpose, set the value as 'HTTP_HOST' in environment file
         |--------------------------------------------------------------------------
         |
         */
        'exotel_header_key' => env('EXOTEL_HEADER_KEY', 'HTTP_X_FORWARDED_FOR'),

        /*
         |--------------------------------------------------------------------------
         | Setting the exotel_header_values as '*' will allow all requests. Other
         | values that can be entered are '127.0.0.1' OR 'localhost' to allow
         | development of API or Hook endpoint.
         |--------------------------------------------------------------------------
         |
         */
        'exotel_header_values' => ['54.251.51.1','54.251.51.3','52.74.5.190','localhost','*'],

        /*
         |--------------------------------------------------------------------------
         | Setting the exotel_date_format as per the data received from Exotel
         | Servers. The exotel server timezone is also India TZ (+5:30 GMT).
         | 
         |--------------------------------------------------------------------------
         |
         */
        'exotel_date_format' => 'Y-m-d H:i:s',
        'exotel_server_timezone' => '+05:30',

        'exotel_subdomain' => env('EXOTEL_SUBDOMAIN', '@api.exotel.com'),
        'exotel_sid' => env('EXOTEL_SID', 'testsid'),
        'exotel_api_key' => env('EXOTEL_API_KEY', 'exotel_api_key'),
        'exotel_api_token' => env('EXOTEL_API_TOKEN', 'exotel_api_token'),

        /*
         |--------------------------------------------------------------------------
         | Exotel Call/Voice Settings
         |--------------------------------------------------------------------------
         |
         */
        'call' => [
            'type' => 'trans',
            'allow_call_recording' => env('EXOTEL_CALL_RECORDING', true),
            'time_out' => env('EXOTEL_CALL_TIMEOUT', 50),
            'max_retries' => env('EXOTEL_MAX_RETRIES', 1),
            'endpoint' => 'https://{exotel_api_key}:{exotel_api_token}{exotel_subdomain}/v1/Accounts/{exotel_sid}/Calls/connect.json',
        ],

        /*
         |--------------------------------------------------------------------------
         | Exotel SMS Settings
         |--------------------------------------------------------------------------
         |
         */
        'sms' => [
            'endpoint' => 'https://{exotel_api_key}:{exotel_api_token}{exotel_subdomain}/v1/Accounts/{exotel_sid}/Sms/send.json',
        ],

        /*
         |--------------------------------------------------------------------------
         | Exotel Bulk SMS Settings
         |--------------------------------------------------------------------------
         |
         */
        'bulk-sms' => [
            'endpoint' => 'https://{exotel_api_key}:{exotel_api_token}{exotel_subdomain}/v1/Accounts/{exotel_sid}/Sms/bulksend.json',
        ],
    ],
];