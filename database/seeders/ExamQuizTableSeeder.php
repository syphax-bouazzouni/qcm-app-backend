<?php

namespace Database\Seeders;

use App\Models\Proposition;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Test;
use Illuminate\Database\Seeder;

class ExamQuizTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = [
            [
                'id' => Quiz::makeId('Généralité Anatomie exam'),
                'label' => 'Généralité Anatomie exam',
                'visible' => true,
                'isExam' => true,
                'module' => 'anatomie',
            ]
        ];

        $test1 = [
            'type' => 0,
            'source' => 'Externat-Alger',
            'questions' => [
                [
                    'text' => 'Les propositions suivantes concernent les os longs des membres.',
                    'type' => 0,
                    'propositions' => [
                        [
                            'proposition' => 'La croissance en longueur dépend d\'un processus d\'ossification périostée.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'La moelle osseuse rouge se rencontre principalement au niveau de la diaphyse.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'L\'évasement de l\'épiphyse d\'un os long permet d’augmenter la composante stabilisatrice de l\'action musculaire.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => ' L\'épiphyse fertile de l\'humérus est l\'épiphise proximale.',
                            'isResponse' => true
                        ],
                        [
                            'proposition' => 'Le cartilage de conjugaison donne naissance au cartilage articulaire.',
                            'isResponse' => false
                        ]
                    ]
                ],
            ]
        ];
        $test2 = [
            'type' => 0,
            'source' => 'Externat-Annaba',
            'questions' => [
                [
                    'text' => 'Les propositions suivantes concernent le radius',
                    'type' => 0,
                    'propositions' => [
                        [
                            'proposition' => 'La fovea (fossette) de la tête radiale s\'articule avec le disque articulaire (ligament triangulaire).',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'La circonférence articulaire (pourtour articulaire) de la tête radiale est articulaire avec l’incisure trochléaire de l’ulna.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'Le col sépare la tubérosité radiale de la diaphyse.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'Le bord ventral de la diaphyse est dit interosseux.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'Le radius est parallèle à l\'ulna lorsque la main est en supination.',
                            'isResponse' => true
                        ]
                    ]
                ],
            ]
        ];
        $test3 = [
            'type' => 1,
            'source' => 'Siamois',
            'questions' => [
                [
                    'text' => 'Les propositions suivantes concernent le radius',
                    'type' => 1,
                    'propositions' => [
                        [
                            'proposition' => 'La fovea (fossette) de la tête radiale s\'articule avec le disque articulaire (ligament triangulaire).',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'La circonférence articulaire (pourtour articulaire) de la tête radiale est articulaire avec l’incisure trochléaire de l’ulna.',
                            'isResponse' => false
                        ],
                        [
                            'proposition' => 'Le col sépare la tubérosité radiale de la diaphyse.',
                            'isResponse' => true
                        ],
                        [
                            'proposition' => 'Le bord ventral de la diaphyse est dit interosseux.',
                            'isResponse' => true
                        ],
                        [
                            'proposition' => 'Le radius est parallèle à l\'ulna lorsque la main est en supination.',
                            'isResponse' => true
                        ]
                    ]
                ],
            ]
        ];
        $test4 = [
            'type' => 2,
            'source' => 'Externat-Alger',
            'questions' => [
                [
                    'text' => 'En dehors des valvulopathies, citez une situation a haut risque de greffe bactérienne nécessitant une antibioprophlaxie ? ',
                    'type' => 2,
                    'propositions' => [
                        [
                            'proposition' => 'Cardiopathies congénitales cyanogènes non opérés',
                            'isResponse' => true
                        ],
                        [
                            'proposition' => 'Antécédents d’endocardite infectieuse',
                            'isResponse' => true
                        ],
                        [
                            'proposition' => 'prothese valvulaire',
                            'isResponse' => true
                        ],
                    ]
                ]
            ]
        ];
        $tests = [
            $test1,
            $test2,
            $test3,
            $test4,
            [
                'type' => 3,
                'source' => 'Siamois',
                'text' => 'Au 2ème jour des règles, une femme de 29 ans présente brutalement une syncope, des frissons, une diarrhée aqueuse, des vomissements et des myalgies : elle est hospitalisée..a l\'examen gynécologique, l\'utérus est de taille normale et les annexes libres. a i examen au spéculum on constate la présence de pus dans le vagin, la patiente utilise des tampons vaginaux qu\'elle change régulièrement. les autres manifestations cliniques du syndrome de choc toxique staphylococcique (toxic shock syndrome) sont observées au cours de l\'hospitalisation.la n.f.s. montre une polynucléose à neutrophiles.la réponse au traitement antibiotique antistaphylococcique est rapidement favorable (apyrexie au 3ème jour)',
                'questions' => [
                    $test1['questions'][0], $test2['questions'][0], $test3['questions'][0]
                ]
            ]
        ];


        foreach ($quizzes as $quiz) {
            $quiz = new Quiz($quiz);
            $quiz->save();
            $tests_ids = [];
            foreach ($tests as $test) {
                $questions = $test['questions'];
                unset($test['questions']);
                $test = Test::updateOrCreate($test);
                $test->questions()->sync($this->createQuestions($questions));
                $tests_ids[] = $test->id;
            }
            $quiz->tests()->sync($tests_ids);
        }
    }

    private function createQuestions($questions)
    {
        $questions_ids = [];
        foreach ($questions as $id => $question) {
            dump($question['text']);
            $propositions = $question['propositions'];
            unset($question['propositions']);
            $question = Question::updateOrCreate(['id' => $id], $question);
            $questions_ids[] = $question->id;
            $this->createPropositions($propositions, $question->id);
        }
        return $questions_ids;
    }

    private function createPropositions($propositions, int $question_id)
    {
        Proposition::where('question', $question_id)->delete();
        foreach ($propositions as $proposition) {
            $proposition['question'] = $question_id;
            $proposition = Proposition::updateOrCreate($proposition);
        }
    }

}
