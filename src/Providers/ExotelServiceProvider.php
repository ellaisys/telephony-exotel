<?php

/*
 * This file is part of EllaiSys Cloud Telephony solution 
 * using Exotel (www.exotel.com)
 *
 * (c) EllaiSys <support@ellaisys.com>
 *
 * For the full copyright and license information, please 
 * view the LICENSE file that was distributed with this 
 * source code.
 */

namespace Ellaisys\Exotel\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

use Exception;

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
        //Register Alias
        $this->registerAliases();


        //Register the singletons
        $this->app->singleton(ExotelCall::class, function () {
            return new ExotelCall();
        });
        // $this->app->singleton(ExotelSms::class, function () {
        //     return new ExotelSms();
        // });
    }


    public function boot()
    {
        //Configuration path
        $path = realpath(__DIR__.'/../../config/config.php');

        //Publish config
        $this->publishes([
            $path => config_path('exotel.php'),
        ], 'config');

        //Register configuration
        $this->mergeConfigFrom($path, 'exotel');

    }


    /**
     * Bind some aliases.
     *
     * @return void
     */
    protected function registerAliases()
    {
        $this->app->alias('ellaisys.exotel.call', ExotelCall::class);
        $this->app->alias('ellaisys.exotel.sms', ExotelSms::class);
    }


    /**
     * Register some singletons.
     *
     * @return void
     */
    protected function registerSingletons()
    {
        //Load exotel settings from configuration file
        $exotelSettings = [
            'exotel_subdomain'  => config('exotel.configuration.sms.exotel_subdomain'),
            'exotel_sid'        => config('exotel.configuration.sms.exotel_sid'),
            'exotel_api_key'    => config('exotel.configuration.sms.exotel_api_key'),
            'exotel_api_token'  => config('exotel.configuration.sms.exotel_api_token'),
        ];

        //Call Manager
        $this->app->singleton('ellaisys.exotel.call', function (Application $app, array $exotelSettings) {
            return (new ExotelCall(
                $exotelSettings
            ));
        });

        //SMS Manager
    }
    
} //Class ends