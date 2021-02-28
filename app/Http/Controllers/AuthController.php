<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except(['login', 'notLogged','register','validateSocialToken']);
        $this->authService = new AuthService();
    }


    public function notLogged(){
        return response()->json(['success'=>false ,'message'=>'not connected'],Response::HTTP_BAD_REQUEST);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request){
        if (! $token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        if(!$user->hasVerifiedEmail() && $user->social_id==-1){
            return response()->json(['error' => 'Email not verified', 'unverified'=> true], 401);
        }
        return response()->json($this->authService->
            createNewToken($token,auth()->factory()->getTTL()*60,auth()->user()));
    }


    /**
     * Register a User.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request) {

        $user = User::create(array_merge(
            $request->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'user' => $user,
        ], 201);
    }




    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh() {
        return response()->json($this->authService->createNewToken(
            auth()->refresh(),
            auth()->factory()->getTTL()*60,
            auth()->user()));
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    // Callback du provider
    public function validateSocialToken ($provider,Request $request) {
        $social = $this->authService->getSocialProvider($provider);
        if($social){
            $data = $social->stateless()->userFromToken($request->token);

            # Social login - register
            $finduser = User::where('social_id', $data->id)->first();

            if($finduser){
                //if the user exists, login and show dashboard
                $user = $finduser;
            }else{
                //user is not yet created, so create first
                $newUser = User::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'social_id'=> $data->id,
                ]);
                $newUser->save();
                $user = $newUser;
            }

            $token= auth()->login($user);
            $user = auth()->user();
            $user->markEmailAsVerified();

            return response()->json($this->authService->
            createNewToken($token,auth()->factory()->getTTL()*60,$user));
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
