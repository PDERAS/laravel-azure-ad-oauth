<?php

namespace Pderas\AzureSocialite;

class UserFactory
{
    protected $config;
    protected static $user_callback;

    public function __construct()
    {
        $this->config = config('azure-oauth');
    }

    public function convertAzureUser($azure_user)
    {
        $user_class = $this->config['user_class'];
        $user_map = $this->config['user_map'];
        $id_field = $this->config['user_id_field'];

        $new_user = new $user_class;
        $new_user->$id_field = $azure_user->id;

        foreach($user_map as $azure_field => $user_field){
            $new_user->$user_field = $azure_user->$azure_field;
        }

        $callback = static::$user_callback;

        if($callback && is_callable($callback)){
            $roles = $this->config['include_roles'] ? $azure_user->roles : [];
            $callback($new_user, $roles);
        }

        $new_user->save();

        return $new_user;
    }

    public static function userCallback($callback)
    {
        if(! is_callable($callback)){
            throw new \Exception("Must provide a callable.");
        }

        static::$user_callback = $callback;
    }
}
