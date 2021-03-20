<?php


namespace App\Services;


use App\Models\PropositionState;
use App\Models\QuestionSession;
use App\Models\Quiz;
use App\Models\QuizCopy;
use App\Models\QuizSession;
use App\Models\QuizSessionProgression;
use App\Models\Test;
use App\Models\TestSession;

class QuizSessionService
{

    public array $relations = ['quizzes', 'tests.questions.propositionsState', 'progression'];
    private function setTests(QuizSession  $quizSession, $quiz){
        $tests = $quiz->tests;
        foreach ($tests as $test){
            $questions = $test->questions;
            if(sizeof($questions)){
                $testSession = new TestSession([
                    'quiz' => $quizSession->id,
                    'timer'=> 0 ,
                    'test'=> $test->id ,
                    'text' => $this->getTestText($test , $questions),
                    'state' => 4 ,
                    'source' => $test->source,
                    'type' => $test->type,
                    'quizLabel' => $quiz->label
                ]);
                $testSession->save();
                $this->setQuestions($testSession , $questions);
            }

        }
    }

    private function  setQuestions(TestSession $test , $questions){
        foreach ($questions as $question){
            $questionSession = new QuestionSession([
                'test' => $test->id,
                'type' => $question->type ,
                'text' => $question->text,
                'explication' => $question->explication,
            ]);
            $questionSession->save();
            $propositions =$question->propositions()->get();
            foreach ($propositions as $proposition){
                $propositionSession = new PropositionState([
                    'question' => $questionSession->id ,
                    'proposition'=>  $proposition->proposition,
                    'isResponse'=> $proposition->isResponse
                ]);
                $propositionSession->save();
            }
        }
    }

    public function getTestText( Test $test , $questions){
        if($test->text){
            return $test->text;
        }else{
            if(sizeof($questions)){
                return $questions[0]->text;
            }
        }
    }

    public function sessionNotFinished($quizzes){
        $quizSession = null;
        if($quizzes){
            $query = QuizSession::with($this->relations);
            foreach ($quizzes as $quiz){
                $id = $quiz->id;
                $query->whereHas('quizzes', function($q) use ($id){
                    $q->where('quiz_id', $id);
                });
            }
            $query->where('user',auth()->user()->id);
            $query->where('state', '!=' ,2);

            $quizSession = $query->first();
        }
        return $quizSession;
    }

    public function start($quizzes , $isQuiz){
        $quizSession = null;
        if($quizzes){

            $quizSession = new QuizSession([
                'user' => auth()->user()->id,
                'isQuiz' => $isQuiz,
            ]);

            $quizSession->save();
            $quizSession->refresh();
            $progression = $this->startProgression($quizSession->id);
            foreach ($quizzes as $quiz){
                QuizCopy::create([
                    'label' => $quiz->label,
                    'quiz_id' => $quiz->id,
                    'quiz' => $quizSession->id,
                ]);
                $this->setTests($quizSession , $quiz);
            }
        }
        return $quizSession;
    }

    public function restart(QuizSession $quizSession){

        $quizSession->update(['currentTest' => 0]);
        $quizSession->progression()->update([
            'success' => 0,
            'error' => 0,
            'notRespond' => 0,
            'rest' => 0,
            'quiz' => $quizSession->id
        ]);

        $tests = $quizSession->tests()->get();
        foreach ($tests as $test){
            $test->update(['timer'=> 0 , 'state' => 4]);
            $questions = $test->questions()->get();
            foreach ($questions as $question){
                $question->update(['state' => 4 ,'isQrocResponded' => false]);
                $question->propositionsState()->update(['propositionsState' => false]);
            }
        }
        return $quizSession;
    }
    private function startProgression($quiz){
        $progression = new QuizSessionProgression([
            'success' => 0,
            'error' => 0,
            'notRespond' => 0,
            'rest' => 0,
            'quiz' => $quiz
        ]);
        $progression->save();
        return $progression;
    }
}
