<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizSessionResource;
use App\Models\Fav;
use App\Models\QuestionSession;
use App\Models\Quiz;
use App\Models\QuizSession;
use App\Models\TestSession;
use App\Services\QuizSessionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuizSessionController extends Controller
{


    private QuizSessionService $quizSessionService;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->quizSessionService = new QuizSessionService();
    }

    public function index(){
        return response()->json(QuizSession::with(['progression' ,'quizzes'])->where('user' , auth()->user()->id)->paginate())->setStatusCode(Response::HTTP_ACCEPTED);
    }

    private function session($request , $testContinue , $isContinue = false ){
        $request->validate([
            'quizzes' => 'required',
            'isQuiz' => 'required',
        ]);
        $quizzes =  $request->get('quizzes');
        return $this->create($quizzes, $testContinue , $request->get('isQuiz') , $isContinue);
    }
    public function startSession(Request $request){
       return $this->session($request , true);
    }

    public function startNewSession(Request  $request){
       $request->validate([
           'isContinue' => 'required'
       ]);
       return $this->session($request , false , $request->get('isContinue'));
    }

    public function restartSession(Request  $request){
        $request->validate([
            'quiz' => 'required'
        ]);

        $quizSession =  QuizSession::findOrFail($request->get('quiz'));
        $quizSession = $this->quizSessionService->restart($quizSession);
        return  (new QuizSessionResource($quizSession->load($this->quizSessionService->relations)))->response()->setStatusCode(Response::HTTP_CREATED);
    }



    private function create($quizzes, $testContinue, $isQuiz , $isContinue){
        $quizSession = null;

        if($this->quizSessionService->isBasicSession($quizzes)){
            $quizzes = $this->quizSessionService->findBasicSessionTests($quizzes , $isQuiz , $isContinue);
        }

        if($testContinue){
            $quizSession = $this->quizSessionService->sessionNotFinished($quizzes);
        }
        if(!$quizSession){
            $quizSession =$this->quizSessionService->start($quizzes, $isQuiz);
        }
        return  (new QuizSessionResource($quizSession->load($this->quizSessionService->relations)))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    public function saveSession(Request $request){
        $request->validate([
            'quiz' => 'required'
        ]);

        $newQuizSession = $request->get('quiz');

        $quizSession = QuizSession::findOrFail($newQuizSession['id']);
        $quizSession->progression()->update($newQuizSession['progression']);
        $quizSession->update([
            "state" => $newQuizSession['state'],
            "currentTest" => $newQuizSession['currentTest']
        ]);
        foreach ($newQuizSession['tests'] as $newTest){
            TestSession::findOrFail($newTest['id'])->update([
                "text" =>  $newTest['text'],
                "timer" => $newTest['timer'],
                "state" => $newTest['state'],
                "note" => $newTest['note']
            ]);
            foreach ($newTest['questions'] as $newQuestion){
                $question = QuestionSession::findOrFail($newQuestion['id']);
                $question->update([
                    "state" => $newQuestion["state"] ,
                    "isQrocResponded" => $newQuestion["isQrocResponded"]]);
                $propositions = $question->propositionsState()->get();

                foreach ($newQuestion['propositionsState'] as $k => $newProposition){
                    $propositions[$k]->update(["propositionsState" => $newProposition]);
                }
            }
        }
        return  (new QuizSessionResource($quizSession->load($this->quizSessionService->relations)))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }



    public function show($id){
        $quizSession = QuizSession::findOrFail($id);
        if($quizSession->user === auth()->user()->id){
            return  (new QuizSessionResource($quizSession->load($this->quizSessionService->relations)))->response()->setStatusCode(Response::HTTP_ACCEPTED);
        }else{
            return  response()->json()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param QuizSession $quiz
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {

        $quiz = QuizSession::findOrFail($id);
        if($quiz->user === auth()->user()->id){
            $quiz->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        }else{
            return response(null, Response::HTTP_UNAUTHORIZED);
        }
    }

}
