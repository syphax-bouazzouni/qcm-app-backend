<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavResource;
use App\Http\Resources\FavResourceCollection;
use App\Http\Resources\TestResourceCollection;
use App\Models\Fav;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavsController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user =  $request->get('user');
        return (new FavResourceCollection(Fav::where([
            ['user', '=', $user],
        ])->with('tests')->paginate()))->response();
    }

    public function indexOfFav(Fav $fav)
    {
        if($fav->user === auth()->user()->id){
            return (new TestResourceCollection($fav->tests()->with('questions')->paginate()))->response();
        }else {
            return response()->json(['message' => 'unauthorized'])->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'user' => 'required',
        ]);

        $fav = new Fav([
            'label' => $request->get('label'),
            'user' => $request->get('user'),
        ]);

        $fav->save();

        return (new FavResource($fav))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Fav $fav
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fav $fav)
    {
        $validate = [
            'label' => 'required',
            'user' => 'required'
        ];

        $request->validate($validate);

        $fav->update([
            'label' => $request->get('label'),
            'user' => $request->get('user'),
        ]);

        return (new FavResource($fav))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Fav $fav
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|void
     */
    public function destroy(Fav $fav){
        $fav->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function addTests(Request $request){

        $request->validate([
            'test' => 'required',
            'user' => 'required',
            'ids' => 'required',
        ]);

        $user = $request->get('user');
        $test_id = $request->get('test');
        $fav_ids = $request->get('ids');

        $allFavsOfTest = Fav::where([
            ['user', '=', $user],
        ])->whereHas('tests', function($q) use($test_id) {
            $q->where('id', $test_id);
        })->get();

        foreach ($allFavsOfTest as $fav) {
            if(!collect($fav_ids)->contains($fav->id)) {
                $fav->tests()->detach($test_id);
            }
        }

        $favs = Fav::whereIn('id', $fav_ids)->get();
        foreach ($favs as $fav){
                $fav->tests()->syncWithoutDetaching($test_id);
        }
        return response()->json(['message' => 'favs updated'])->setStatusCode(Response::HTTP_OK);
    }

    public function removeTest(Request $request){
        $request->validate([
            'fav' => 'required',
            'test' => 'required',
        ]);

        $fav = Fav::findOrFail($request->get('fav'));

        $fav->tests()->detach($request->get('test'));
        return response(null, Response::HTTP_ACCEPTED);
    }
}
