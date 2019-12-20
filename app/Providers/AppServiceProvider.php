<?php

namespace App\Providers;

use App\Support\LaravelCacheAdapter;
use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DynamoDbClient::class, function (Container $app) {
            $dynamoDbClient = new DynamoDbClient([
                'endpoint' => config('aws.dynamodb.endpoint'),
                'region'  => config('aws.dynamodb.region'),
                'version' => config('aws.dynamodb.version'),
                'credentials' => new LaravelCacheAdapter($app->make('cache')),
            ]);

            return $dynamoDbClient;
        });
    }

//    public function provides()
//    {
//        return [
//            DynamoDbClient::class
//        ];
//    }
}
