<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Http\Resources\QuizResourceCollection;
use App\Models\Proposition;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    private TestsController $testsController;
    private QuizService  $quizService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->testsController = new TestsController();
        $this->quizService = new QuizService();
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $module = $request->get('module');
        $isExam = $request->get('isExam');
        return (new QuizResourceCollection(Quiz::where([
            ['module', '=', $module],
            ['isExam', '=', $isExam],
        ])->withCount('tests as nbTests')->paginate()))->response();
    }


    public function show(Quiz $quiz)
    {
        return (new QuizResource($quiz->load('tests.questions.propositions')))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|unique:quizzes',
            'visible' => 'required',
            'isExam' => 'required',
            'module' => 'required',
            'order' => 'required',
        ]);
        $order = $request->get('order');
        $isExam = $request->get('isExam');
        $this->quizService->insertOrder($order , $isExam);

        $quiz = new Quiz([
            'id' => Str::snake($request->get('label')),
            'label' => $request->get('label'),
            'visible' => $request->get('visible'),
            'isExam' => $isExam,
            'order' => $order,
            'module' => $request->get('module'),
        ]);

        $quiz->save();
        $this->quizService->updateTests($request, $quiz);
        return (new QuizResource($quiz->load('tests.questions.propositions')))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Quiz $quiz
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Quiz $quiz)
    {

        $validate = [
            'visible' => 'required',
            'isExam' => 'required',
            'module' => 'required'
        ];

        $order = $request->get('order');
        $isExam = $request->get('isExam');
        if($quiz->order != $order){
            $this->quizService->insertOrder($order , $isExam);
        }
        if ($quiz->label !== $request->get('label')) {
            $validate['label'] = 'required|unique:quizzes';
        }

        $request->validate($validate);

        $quiz->update([
            'id' => Str::snake($request->get('label')),
            'label' => $request->get('label'),
            'visible' => $request->get('visible'),
            'isExam' => $isExam,
            'module' => $request->get('module'),
            'order' => $order,
        ]);
        $this->quizService->updateTests($request, $quiz);

        return (new QuizResource($quiz->load('tests.questions.propositions')))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Quiz $quiz
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

   public function updateOrder(Request $request){
        $request->validate([
            'first' => 'required',
            'second' => 'required'
        ]);
        $first = Quiz::findOrFail($request->get('first'));
        $second = Quiz::findOrFail($request->get('second'));

        $this->quizService->updateOrder($first , $second);

        return response()->json(['message' => 'orders updated'])->setStatusCode(Response::HTTP_ACCEPTED);
   }

}
