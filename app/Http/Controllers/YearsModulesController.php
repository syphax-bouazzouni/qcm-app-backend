<?php

namespace App\Http\Controllers;

use App\Http\Resources\YearModulesCollection;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YearsModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $year = auth()->user()->year;
        $years = $this->getByYears($this->getModules($year));
        return (new YearModulesCollection($years))->response();
    }


    public function show()
    {
        $year = auth()->user()->year;
        $modules = $this->getModules($year);

        $nbQuiz = 0;
        $nbExam = 0;

        foreach ($modules as $module){
            $nbExam+= $module->nbExam;
            $nbQuiz+= $module->nbQuiz;
        }

        return response()->json(['year' => $year , 'nbQuiz' => $nbQuiz , 'nbExam' => $nbExam])->setStatusCode(Response::HTTP_ACCEPTED);
    }
    private function getModules($year){

        if ($year) {
            $modules = Module::where('year', $year)->withCount(['quizzes as nbQuiz' => function ($query) {
                $query->where('isExam', false);
            },
                'quizzes as nbExam' => function ($query) {
                    $query->where('isExam', true);
                }])->get()->load('offers');
        } else {
            $modules = Module::withCount(['quizzes as nbQuiz' => function ($query) {
                $query->where('isExam', false);
            },
                'quizzes as nbExam' => function ($query) {
                    $query->where('isExam', true);
                }])->get()->load('offers');
        }
        return $modules;
    }
    private function getByYears($modules)
    {
        $years = [];
        foreach ($modules as $module) {
            if (isset($years[$module->year])) {
                $years[$module->year][] = $module;
            } else {
                $years[$module->year] = [$module];
            }
        }
        return $years;
    }
}
