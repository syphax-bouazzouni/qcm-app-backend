<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    public function index()
    {
        if(auth()->user()->is_admin){
            return (new UserCollection(User::paginate()))->response();
        }else{
            return  response(null , Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function update(Request $request, int $id)
    {
        $auth = auth()->user();
        if($auth->is_admin || $auth->id = $id){
            $user = User::findOrFail($id);

            if(isset($request->get('user')['is_admin'])){
                $user->is_admin = $request->get('user')['is_admin'];
            }
            if(key_exists('year',$request->get('user'))){
                $user->year = $request->get('user')['year'];
            }
            if(isset($request->get('user')['university'])){
                $user->university = $request->get('user')['university'];
            }
            if(isset($request->get('user')['name'])){
                $user->name = $request->get('user')['name'];
            }

            $user->save();
            return \response()->json($user)->setStatusCode(Response::HTTP_ACCEPTED);
        }else{
            return \response(null , Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
