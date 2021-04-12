<?php

namespace App\Providers;

use App\Services\DI\YaCloud;
use Illuminate\Support\ServiceProvider;

class YaCloudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(YaCloud::class, function ($app) {
            $yaCloud = new YaCloud();
            $yaCloud->registerServiceProvider();
            return $yaCloud;
        });
    }

    public function provides()
    {
        return [YaCloud::class];
    }
}
