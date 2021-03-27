<?php


namespace App\Services;


use App\Models\Fav;
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

    private function setTests(QuizSession $quizSession, $quiz)
    {
        $tests_ids = [];
        foreach ($quiz['tests'] as $test) {
            $tests_ids[] = $test['id'];
            array_unique(array_merge($tests_ids, [$test['id']]));
        }

        foreach ($tests_ids as $test_id) {
            $test = Test::findOrFail($test_id);
            $questions = $test->questions;
            if (sizeof($questions)) {
                $testSession = new TestSession([
                    'quiz' => $quizSession->id,
                    'timer' => 0,
                    'test' => $test->id,
                    'text' => $this->getTestText($test, $questions),
                    'state' => 4,
                    'source' => $test->source,
                    'type' => $test->type,
                    'quizLabel' => $quiz['label']
                ]);
                $testSession->save();
                $this->setQuestions($testSession, $questions);
            }

        }
    }

    private function setQuestions(TestSession $test, $questions)
    {
        foreach ($questions as $question) {
            $questionSession = new QuestionSession([
                'test' => $test->id,
                'type' => $question->type,
                'text' => $question->text,
                'explication' => $question->explication,
                'question' => $question->id,
            ]);
            $questionSession->save();
            $propositions = $question->propositions()->get();
            foreach ($propositions as $proposition) {
                $propositionSession = new PropositionState([
                    'question' => $questionSession->id,
                    'proposition' => $proposition->proposition,
                    'isResponse' => $proposition->isResponse
                ]);
                $propositionSession->save();
            }
        }
    }

    public function getTestText(Test $test, $questions)
    {
        if ($test->text) {
            return $test->text;
        } else {
            if (sizeof($questions)) {
                return $questions[0]->text;
            }
        }
    }

    public function sessionNotFinished($quizzes)
    {
        $quizzes_ids = [];
        $tests_ids = [];
        $quizSession = null;

        foreach ($quizzes as $quiz) {
            $quizzes_ids[] = $quiz['id'];
            foreach ($quiz['tests'] as $test) {
                $tests_ids[] = $test['id'];
                array_unique(array_merge($tests_ids, [$test['id']]));
            }
        }

        $query = QuizSession::with($this->relations);
        foreach ($quizzes_ids as $quiz_id) {
            $query->whereHas('quizzes', function ($q) use ($quiz_id) {
                $q->where('quiz_id', $quiz_id);
            })->withCount('quizzes as nbQuiz');
        }
        foreach ($tests_ids as $t_id) {
            $query->whereHas('tests', function ($t) use ($t_id) {
                $t->where('test', $t_id);
            })->withCount('tests as nbTest');
        }
        $query->where('user', auth()->user()->id);
        $query->where('state', '!=', 2);

        $quizSession = $query->get()->filter(function ($q) use($quizzes_ids , $tests_ids){
            return $q->nbQuiz == sizeof($quizzes_ids) && $q->nbTest == sizeof($tests_ids);
        })->first;

        return ($quizSession ? $quizSession : null);
    }

    public function start($quizzes, $isQuiz)
    {
        $quizSession = null;
        $quizSession = new QuizSession([
            'user' => auth()->user()->id,
            'isQuiz' => $isQuiz,
        ]);

        $quizSession->save();
        $quizSession->refresh();
        $progression = $this->startProgression($quizSession->id);

        foreach ($quizzes as $quiz) {
            QuizCopy::create([
                'label' => $quiz['label'],
                'quiz_id' => $quiz['id'],
                'quiz' => $quizSession->id,
            ]);
            $this->setTests($quizSession, $quiz);
        }

        return $quizSession;
    }

    public function restart(QuizSession $quizSession)
    {

        $quizSession->update(['currentTest' => 0 , 'state' => 0]);
        $quizSession->progression()->update([
            'success' => 0,
            'error' => 0,
            'notRespond' => 0,
            'rest' => 0,
            'quiz' => $quizSession->id
        ]);

        $tests = $quizSession->tests()->get();
        foreach ($tests as $test) {
            $test->update(['timer' => 0, 'state' => 4]);
            $questions = $test->questions()->get();
            foreach ($questions as $question) {
                $question->update(['state' => 4, 'isQrocResponded' => false]);
                $question->propositionsState()->update(['propositionsState' => false]);
            }
        }
        return $quizSession;
    }

    private function startProgression($quiz)
    {
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




    /*
     * Basic session , is a session with only one quiz or fav
     */
    public function isBasicSession($quizzes)
    {
        return sizeof($quizzes) == 1 && !array_key_exists('tests',$quizzes[0]);
    }

    public function findBasicSessionTests($quizzes, $isQuiz , $isContinue): array
    {
        $out = [];
        if(!$isContinue){
            $quiz = $quizzes[0];
            if ($isQuiz) {
                $quiz = Quiz::where('id', $quiz['id'])->with('tests', function ($t) {
                    $t->select(['id']);
                })->first();
            } else {
                $quiz = Fav::where('id', $quiz['id'])->with('tests', function ($t) {
                    $t->select(['id']);
                })->first();
            }

            if ($quiz) {
                $out = [["id" => $quiz->id, "label" => $quiz->label,
                    "tests" => array_map(function ($e) {
                        return [
                            "id" => $e['id']
                        ];
                    }, $quiz->tests()->get()->toArray())]];
            }
        }else{
            $out = $this->startNewFromSession(QuizSession::findOrFail($quizzes[0]['id']));
        }

        return $out;
    }
    public function startNewFromSession(QuizSession $quizSession)
    {
        $quizzes = [];
        $quizLabels = $quizSession->quizzes()->get()->toArray();

        foreach ($quizSession->tests()->get() as $test) {
            $i = $this->finInArray($test->quizLabel, 'label' , $quizLabels);
            if ($i) {
                $quiz_id = $quizLabels[$i]['quiz_id'];
                $i = $this->finInArray($quiz_id, 'id' ,$quizzes);
                if ($i) {
                    $quizzes[$i]['tests'][] = $test->id;
                } else {
                    $quizzes[] = [
                        'id' => $quiz_id,
                        'label' => $test->quizLabel,
                        'tests' => array(['id' => $test->id])
                    ];
                }
            }
        }
        return $quizzes;
    }

    private function finInArray($value, $col, $array)
    {
        return array_search($value, array_column($array, $col));
    }
}
