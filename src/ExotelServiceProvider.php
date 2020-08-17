<?php

namespace Ellaisys\Exotel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

/**
 * Class ExotelServiceProvider.
 */
class ExotelServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/config.php'; //base_path('packages/ellaisys/exotel/config/config.php');

        //Register configuration
        $this->mergeConfigFrom($configPath, 'ellaisys-exotel'); 

        //Register the singletons
        $this->app->singleton(ExotelCall::class, function () {
            return new ExotelCall();
        });
        // $this->app->singleton(ExotelSms::class, function () {
        //     return new ExotelSms();
        // });

        //Bind Facades
        $this->app->alias(ExotelCall::class, 'exotel-call');
        // $this->app->bind('exotel-call', function($app) {
        //     return new ExotelCall();
        // });
    }

    public function boot()
    {
        dd('Exotel booted');

        // $configPath = base_path('packages/ellaisys/exotel/config/config.php');

        // //Over-ride configuration
        // $this->publishes([
        //     $configPath => config_path('ellaisys-exotel.php'),
        // ], 'config');
    }
    
} //Class ends