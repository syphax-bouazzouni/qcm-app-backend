<?php


namespace App\Services;


use App\Models\Proposition;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use Illuminate\Http\Request;

class QuizService
{

    function insertOrder($order , $isExam){
        $quizzes = Quiz::where('isExam' , $isExam)->where('order' ,'>=',$order)->get();
        foreach ($quizzes as $k => $quiz){
            $quiz->update(['order' => $order + $k +1]);
        }
    }
    function updateOrder(Quiz $first , Quiz $second){
        $old = $second->order;
        $second->update(['order' => $first->order]);
        $first->update(['order' => $old]);
    }

    function updateTests(Request $request, Quiz $quiz)
    {
        $tests_ids = [];
        foreach ($request->get('tests') as $k=>$test) {
            $test_id = $test['id'];
            $questions = $test['questions'];
            unset($test['questions']);
            unset($test['id']);

            if ($test_id){
                $test = Test::updateOrCreate(['id' => $test_id],$test);
            }else{
                $test = Test::create($test);
            }
        
            $tests_ids[$test->id] = ['order' => $k+1];
            $this->updateQuestions($questions, $test);
        }

        $quiz->tests()->sync($tests_ids);
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
