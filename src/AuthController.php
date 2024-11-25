<?php

namespace Pderas\AzureSocialite;

use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Azure OAuth provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToOauthProvider()
    {
        return Socialite::driver('azure-oauth')->redirect();
    }

    /**
     * Callback function for the Azure OAuth provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleOauthResponse()
    {
        $user = Socialite::driver('azure-oauth')->user();

        $authUser = $this->findOrCreateUser($user);

        auth()->login($authUser, true);

        return redirect(
            config('azure-oauth.redirect_on_login')
        );
    }

    /**
     * Find or create a user in the database.
     *
     * @param  \Laravel\Socialite\Two\User  $user
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function findOrCreateUser($user)
    {
        $user_class = config('azure-oauth.user_class');
        $auth_user = $user_class::where(config('azure-oauth.user_id_field'), $user->id)->first();

        if ($auth_user) {
            return $auth_user;
        }

        $user_factory = new UserFactory();

        return $user_factory->convertAzureUser($user);
    }
}
