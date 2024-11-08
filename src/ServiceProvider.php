<?php

namespace Pderas\AzureSocialite;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        // 
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/azure-oath.php' => config_path('azure-oath.php'),
        ], 'azure-oauth');

        $this->mergeConfigFrom(
            __DIR__.'/config/azure-oath.php', 'azure-oath'
        );

        $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function($app){
            $provider = $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                'Pderas\AzureSocialite\AzureOauthProvider',
                config('azure-oath.credentials')
            );

            if (config('azure-oath.use_pkce', true)) {
                $provider->enablePKCE();
            }

            return $provider;
        });

        $this->app['router']->group(['middleware' => config('azure-oath.routes.middleware')], function($router){
            $router->get(config('azure-oath.routes.login'), 'Pderas\AzureSocialite\AuthController@redirectToOauthProvider');
            $router->get(config('azure-oath.routes.callback'), 'Pderas\AzureSocialite\AuthController@handleOauthResponse');
        });
    }
}
