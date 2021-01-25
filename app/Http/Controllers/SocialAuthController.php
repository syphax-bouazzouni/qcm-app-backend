<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;


class SocialAuthController extends Controller
{

    private AuthService $authService;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['validateToken']);
        $this->authService = new AuthService();
    }


}
