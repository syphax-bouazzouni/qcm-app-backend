<?php

namespace App\Http\Controllers;

use App\Http\Resources\TestResource;
use App\Http\Resources\TestResourceCollection;
use App\Models\Fav;
use App\Models\Quiz;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'sources' => 'array|present' ,
        ]);
        $sources = $request->get('sources');
        $search = $request->get('search');
        $tests = Test::withCount('quizzes')->with('questions.propositions');
        if(sizeof($sources) > 0){
           $tests->whereIn('source',$sources);
        }
        if($search){
            $tests->where(function ($query) use ($search) {
                $query->whereHas('questions', function ($q) use ($search){
                    $q->where('text' ,'LIKE', '%'.$search.'%');
                });
                $query->OrWhere('text' ,'LIKE', '%'.$search.'%' );
            });
        }
        return (new TestResourceCollection($tests->latest()->paginate()))->response();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Test $test)
    {
        return (new TestResource($test->load('questions.propositions')))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Test $test
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Test $test)
    {
        $test->quizzes()->sync([]);
        $test->questions()->sync([]);
        $test->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
