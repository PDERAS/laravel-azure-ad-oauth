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
            __DIR__.'/config/azure-oauth.php' => config_path('azure-oauth.php'),
        ], 'azure-oauth');

        $this->mergeConfigFrom(
            __DIR__.'/config/azure-oauth.php', 'azure-oauth'
        );

        $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function($app){
            $provider = $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                'Pderas\AzureSocialite\AzureOauthProvider',
                config('azure-oauth.credentials')
            );

            if (config('azure-oauth.use_pkce', true)) {
                $provider->enablePKCE();
            }

            return $provider;
        });

        $this->app['router']->group(['middleware' => config('azure-oauth.routes.middleware')], function($router){
            $router->get(config('azure-oauth.routes.login'), 'Pderas\AzureSocialite\AuthController@redirectToOauthProvider');
            $router->get(config('azure-oauth.routes.callback'), 'Pderas\AzureSocialite\AuthController@handleOauthResponse');
        });
    }
}
