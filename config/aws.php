<?php


return [

    'region' => env('AWS_REGION', 'us-west-2'),

    'dynamodb' => [
        'region' => env('DYNAMO_DB_REGION', 'us-west-2'),
        'endpoint' => env('DYNAMO_DB_ENDPOINT'),
        'version' => env('DYNAMO_DB_VERSION', '2012-08-10')
    ]
];
