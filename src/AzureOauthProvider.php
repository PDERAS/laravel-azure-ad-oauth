<?php

namespace Pderas\AzureSocialite;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\InvalidStateException;

class AzureOauthProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['.default'];

    /**
     * The scope separator.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        $url = AzureUrlBuilder::buildAuthUrl($state);

        return $this->buildAuthUrlFromBase($url, $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return AzureUrlBuilder::buildTokenUrl();
    }

    /**
     * Get the headers for the access token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenHeaders($code)
    {
        return [
            'Accept' => 'application/json',
            'Origin' => $this->redirectUrl,
        ];
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $url = AzureUrlBuilder::buildUserByToken();

        $response = $this->getHttpClient()->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        logger('user response', ['response' => $response->getBody()]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \Laravel\Socialite\Contracts\User
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        $user->idToken = Arr::get($response, 'id_token');
        $user->expiresAt = time() + Arr::get($response, 'expires_in');

        return $user->setToken($token)
                    ->setRefreshToken(Arr::get($response, 'refresh_token'));
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'                => $user['id'],
            'name'              => $user['displayName'],
            'email'             => $user['mail'],

            'businessPhones'    => $user['businessPhones'],
            'displayName'       => $user['displayName'],
            'givenName'         => $user['givenName'],
            'jobTitle'          => $user['jobTitle'],
            'mail'              => $user['mail'],
            'mobilePhone'       => $user['mobilePhone'],
            'officeLocation'    => $user['officeLocation'],
            'preferredLanguage' => $user['preferredLanguage'],
            'surname'           => $user['surname'],
            'userPrincipalName' => $user['userPrincipalName'],
        ]);
    }
}
