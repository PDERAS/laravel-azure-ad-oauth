<?php

namespace Pderas\AzureSocialite;

class AzureUrlBuilder
{
    /**
     * Build the authorization URL.
     *
     * @param string $state
     * @return string
     */
    public static function buildAuthUrl($state)
    {
        $base_url = config('azure-oath.routes.authorization_url');

        return str_replace('{tenant}', config('azure-oath.credentials.tenant'), $base_url);
    }

    /**
     * Build the token URL.
     *
     * @return string
     */
    public static function buildTokenUrl()
    {
        $url = config('azure-oath.routes.token_url');

        return str_replace('{tenant}', config('azure-oath.credentials.tenant'), $url);
    }

    /**
     * Build the user URL.
     *
     * @return string
     */
    public static function buildUserByToken()
    {
       return config('azure-oath.routes.user_token', 'https://graph.microsoft.com/v1.0/me/');
    }
}