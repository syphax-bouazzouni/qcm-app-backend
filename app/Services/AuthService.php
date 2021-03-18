<?php


namespace App\Services;


use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{

    protected $providers = [ "google", "facebook" ];



    public function getSocialProvider(string $providerName)
    {

        if (in_array($providerName, $this->providers)) {
            return Socialite::driver($providerName);
        }
        return null;
    }

    /**
     * Get the token array structure.
     */
    public function createNewToken($token,$ttl,$user){
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60 * 60 * 1000,
            'user' => $user
        ];
    }

}
