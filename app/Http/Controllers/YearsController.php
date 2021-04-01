<?php

namespace App\Http\Controllers;

use App\Http\Resources\YearModulesCollection;
use App\Http\Resources\YearResource;
use App\Models\Module;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YearsController extends Controller
{
    private $imageController;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->imageController = new ImageController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $years = Year::with(['modules' => function ($m) {
            $m->withCount(['quizzes as nbQuiz' => function ($query) {
                $query->where('isExam', false);
            },
                'quizzes as nbExam' => function ($query) {
                    $query->where('isExam', true);
                }])->get()->load('offers');
        }])->get();
        return (new YearModulesCollection($years))->response();
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
            'title' => 'required|unique:years|min:1',
            'image' => 'required',
            'order' => 'required| min: 1'
        ]);
        $order = $request->get('order');
        $this->insertOrder($order);
        $image = $this->imageController->store($request);
        if($image != null){
            $image =  $image->getData('data')['data']['title'];
        }
        $year = new Year([
            'title' => $request->get('title'),
            'image' => $image,
            'order' => $order
        ]);

        $year->save();

        return (new YearResource($year))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function show($id)
    {
        $year = Year::where('id' , $id)->with(['modules' => function ($m) {
            $m->withCount(['quizzes as nbQuiz' => function ($query) {
                $query->where('isExam', false);
            },
                'quizzes as nbExam' => function ($query) {
                    $query->where('isExam', true);
                }])->get()->load('offers');
        }])->first();
        return (new YearResource($year))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function infos()
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
        $modules = null;
        if ($year) {
            $modules = Module::where('year', $year)->withCount(['quizzes as nbQuiz' => function ($query) {
                $query->where('isExam', false);
            },
                'quizzes as nbExam' => function ($query) {
                    $query->where('isExam', true);
                }])->get()->load('offers');
        }
        return $modules;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Year $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Year $year)
    {
        $validate = [
            'title' => 'required',
            'image' => 'required',
            'order' => 'required | min:1'
        ];

        $request->validate($validate);

        $order = $request->get('order');
        if($year->order != $order){
            $this->insertOrder($order);
        }

        $image = $request->get('image')['name'];
        if($year->image == null){
            $image = $this->imageController->store($request);
        } else if ($image !== $year->image) {
            $image = $this->imageController->update($request, $year->image);
        }

        if($image != null){
            $image = $image->getData('data')['data']['title'];
        }

        $year->update([
            'title' => $request->get('title'),
            'image' => $image,
            'order' => $order
        ]);

        return (new YearResource($year))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Year $year
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|void
     */
    public function destroy(Year $year)
    {
        $year->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function insertOrder($order){
        $years = Year::where('order' ,'>=',$order)->get();
        foreach ($years as $k => $year){
            $year->update(['order' => $order + $k+1]);
        }
    }
}
