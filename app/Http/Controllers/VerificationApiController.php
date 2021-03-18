<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationApiController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api')->except(['verify' ,'resend']);
    }

    /**
     * Verify email
     *
     * @param $user_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(['error'=> true,'message' => 'Invalid signature']);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['success'=> true,'message' => 'Email verified']);
    }

    /**
     * Resend email verification link
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resend(Request $request) {
        $user = User::where('email' , $request->get('email'))->first();

        if($user){
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return response()->json(['success'=> true,'message' => 'Email verification resended']);
            }else{
                return response()->json(['error'=> true,'message' => 'Email verification resended'])->setStatusCode(401);
            }
        }else{
            return response()->json(['error'=> true,'message' => 'Email not found'])->setStatusCode(401);
        }
    }
}

