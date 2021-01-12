<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationApiController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api')->except(['verify']);
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return  response()->json(['error'=> true,'message' => 'User mail is verified']);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(['success'=> true,'message' => 'Email verification resended']);
    }
}

