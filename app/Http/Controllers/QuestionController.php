<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionResourceCollection;
use App\Http\Resources\TestResource;
use App\Http\Resources\TestResourceCollection;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'types' => 'array|present' ,
        ]);
        $types = $request->get('types');
        $search = $request->get('search');
        $questions = Question::withCount('tests')->with('propositions');

        if(sizeof($types) > 0){
            $questions->whereIn('type',$types);
        }
        if($search){
            $questions->where('text' ,'LIKE', '%'.$search.'%');
        }
        return (new QuestionResourceCollection($questions->latest()->paginate()))->response();
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
        return (new QuestionResource($test->load('propositions')))->response();
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
     * @param Question $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        $question->tests()->sync([]);
        $question->propositions()->delete();
        $question->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
