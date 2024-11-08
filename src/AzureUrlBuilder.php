<?php

namespace Pderas\AzureSocialite;

class AzureUrlBuilder
{
    public static function buildAuthUrl($state)
    {
        $base_url = config('azure-oath.routes.authorization_url');

        return str_replace('{tenant}', config('azure-oath.credentials.tenant'), $base_url);
    }

    public static function buildTokenUrl()
    {
        $url = config('azure-oath.routes.token_url');

        return str_replace('{tenant}', config('azure-oath.credentials.tenant'), $url);
    }

    public static function buildUserByToken()
    {
       return config('azure-oath.routes.user_token', 'https://graph.microsoft.com/v1.0/me/');
    }
}