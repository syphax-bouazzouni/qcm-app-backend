<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Http\Resources\QuizResourceCollection;
use App\Models\Proposition;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    private TestsController $testsController;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->testsController = new TestsController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return (new QuizResourceCollection(Quiz::latest()->paginate()))->response();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexModule(Request $request)
    {
        $module = $request->get('module');
        $isExam = $request->get('isExam');
        return (new QuizResourceCollection(Quiz::where([
            ['module', '=', $module],
            ['isExam', '=', $isExam],
        ])->latest()->paginate()))->response();
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
        ]);

        $quiz = new Quiz([
            'id' => Str::snake($request->get('label')),
            'label' => $request->get('label'),
            'visible' => $request->get('visible'),
            'isExam' => $request->get('isExam'),
            'module' => $request->get('module'),
        ]);

        $quiz->save();
        $this->updateTests($request, $quiz);
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

        if ($quiz->label !== $request->get('label')) {
            $validate['label'] = 'required|unique:quizzes';
        }

        $request->validate($validate);

        $quiz->update([
            'id' => Str::snake($request->get('label')),
            'label' => $request->get('label'),
            'visible' => $request->get('visible'),
            'isExam' => $request->get('isExam'),
            'module' => $request->get('module'),
        ]);
        $this->updateTests($request, $quiz);

        return (new QuizResource($quiz->load('tests.questions.propositions')))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function updateTests(Request $request, Quiz $quiz)
    {
        $tests = [];
        foreach ($request->get('tests') as $test) {
            $questions = $test['questions'];
            unset($test['questions']);
            $test = Test::updateOrCreate($test);
            $this->updateQuestions($questions, $test);
            $tests[] = $test->id;
        }

        $quiz->tests()->sync($tests);
    }

    private function updateQuestions($questions, Test $test)
    {
        $questions_ids = [];
        foreach ($questions as $question) {
            $propositions = $question['propositions'];
            $question_id = $question['id'];
            unset($question['propositions']);
            unset($question['isQROC']);
            unset($question['id']);
            if ($question_id){
                $question = Question::updateOrCreate(['id' => $question_id], $question);
            }else{
                $question = Question::create($question);
            }
            $questions_ids[] = $question->id;
            $this->updatePropositions($propositions, $question->id);
        }
        $test->questions()->sync($questions_ids);
    }

    private function updatePropositions($propositions, $question_id)
    {
        if ($question_id){
            Proposition::where('question', $question_id)->delete();
        }
        foreach ($propositions as $proposition) {
            $proposition['question'] = $question_id;
            $proposition = Proposition::updateOrCreate($proposition);
        }
    }
}
