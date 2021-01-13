<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CaptchaController extends Controller
{
    const GOOGLE_URL = "https://www.google.com/recaptcha/api/siteverify";
    private string $captcha_key;

    public function __construct()
    {
        $this->captcha_key = env('APP_CAPTCHA_KEY','');
    }

    public function apiUrl(string $request_token ,string $remoteip):string{
        return self::GOOGLE_URL ."?secret={$this->captcha_key}&response={$request_token}&remoteip={$remoteip}";
    }

    public function validateCaptcha(Request $request)
    {
        return  Http::get(self::GOOGLE_URL,[
            'secret'=>$this->captcha_key,
            'response'=>$request['captcha'],
            'remoteip'=>$request->ip()
        ]);
    }
}
