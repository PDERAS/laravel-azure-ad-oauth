<?php

use App\Providers\RouteServiceProvider;

return [
    'routes' => [
        // The middleware to wrap the auth routes in.
        // Must contain session handling otherwise login will fail.
        'middleware' => 'web',

        // The url that will redirect to the SSO URL.
        // There should be no reason to override this.
        'login' => 'login/microsoft',

        // The app route that SSO will redirect to.
        // There should be no reason to override this.
        'callback' => 'login/microsoft/callback',

        // Azure URL endpoints.
        // The {tenant} value in the path of the request can be used to control
        // who can sign into the application and can be defined below in `credentials.tenant`.
        // Valid values are `common`, `organizations`, `consumers`, and tenant identifiers
        'authorization_url' => 'https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize',
        'token_url'         => 'https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token',
        'user_token'        => 'https://graph.microsoft.com/v1.0/me/',
        'user_roles'        => 'https://graph.microsoft.com/v1.0/me/appRoleAssignments',
    ],

    'credentials' => [
        'tenant'        => env('AZURE_AD_TENANT_ID', 'common'),
        'client_id'     => env('AZURE_AD_CLIENT_ID', ''),
        'client_secret' => env('AZURE_AD_CLIENT_SECRET', ''),
        'redirect'      => '/login/microsoft/callback'
    ],

    // The route to redirect the user to upon login.
    'redirect_on_login' => RouteServiceProvider::HOME,

    // The User Eloquent class.
    'user_class' => '\\App\\Models\\User',

    // How much time should be left before the access
    // token expires to attempt a refresh.
    'refresh_token_within' => 30,

    // The users table database column to store the user SSO ID.
    'user_id_field' => 'azure_id',

    // Whether to include the user roles in the user object.
    'include_roles' => false,

    // How to map azure user fields to Laravel user fields.
    // Do not include the id field above.
    // AzureUserField => LaravelUserField
    'user_map' => [
        'idToken'           => 'azure_token',
        'givenName'         => 'first_name',
        'surname'           => 'last_name',
        'email'             => 'email',
        'userPrincipalName' => 'user_principal_name',
    ]
];
