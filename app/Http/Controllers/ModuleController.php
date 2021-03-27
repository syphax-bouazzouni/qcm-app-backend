<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleResourceCollection;
use App\Http\Resources\QuizResourceCollection;
use App\Models\Image;
use App\Models\Module;
use App\Models\Offer;
use App\Models\Question;
use App\Models\QuestionSession;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class ModuleController extends Controller
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
        return (new ModuleResourceCollection(Module::paginate()->load('offers')))->response();
    }

    public function moduleWithQuizzes(Request $request)
    {
        $request->validate([
            'module' => 'required',
            'types' => 'present|array',
            'withExplication' => 'required',
            'withNote' => 'required',
            'onlyFalse' => 'required',
            'notSeen' => 'required',
        ]);

        $withExplication = $request->get('withExplication');
        $withNote = $request->get('withNote');
        $onlyFalse = $request->get('onlyFalse');
        $notSeen = $request->get('notSeen');
        $types = $request->get('types');
        $module = null;
        $quizzes = [];
        if ($types) {
            $module = Module::where('id', $request->get('module'))->first();
            if ($module) {
                $quizzes = Quiz::select(['id' ,'label'])->where('module', $module->id)->where('isExam', false)->where('visible' ,true)
                    ->with(['tests' => function ($t) use ($types, $withExplication, $withNote, $notSeen, $onlyFalse) {
                        $this->filterTest($t, $types, $withExplication, $withNote, $notSeen, $onlyFalse);
                    }])
                    ->withCount(['tests as nbTests' => function ($t) use ($types, $withExplication, $withNote, $notSeen, $onlyFalse) {
                        $this->filterTest($t, $types, $withExplication, $withNote, $notSeen, $onlyFalse ,false);
                    }])->get()->filter(function ($q){
                        return $q->nbTests > 0;
                    });

            }
        }

        return (new QuizResourceCollection($this->paginateCollection($quizzes)))->response();
    }

    private function paginateCollection($collection, $perPage = 15, $pageName = 'page', $fragment = null)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        if(sizeof($collection) > 0 ){
            $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage);
            $count = $collection->count();
        }else{
            $currentPageItems = [];
            $count = 0;
        }
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $count,
            $perPage,
            $currentPage,
            [
                'pageName' => $pageName,
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
                'fragment' => $fragment
            ]
        );

        return $paginator;
    }

    private function filterTest($t, $types, $withExplication, $withNote, $notSeen, $onlyFalse , $select = true)
    {
        if($select){
            $t->select('id');
        }

        if($onlyFalse || $withNote){
            $t->whereHas('sessions' , function ($session) use ($withNote ,$onlyFalse){
               if ($onlyFalse) {
                   $session->where('state' , 1);
               }
               if ($withNote) {
                   $session->where('note', '!=', '');
                }
            });
        }

        $t->whereIn('type', $types)->whereHas('questions', function ($question) use ($withExplication, $notSeen, $onlyFalse ,$select) {
            if($select){
                $question->select('id');
            }
            if ($withExplication) {
                $question->where('explication', '!=', '');
            }
            if ($notSeen) {
                $question->whereDoesntHave('sessions');
            }
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:modules',
            'year' => 'required|min:1',
            'image' => 'required',
            'offers' => 'required'
        ]);


        $image = $this->imageController->store($request);
        $module = new Module([
            'id' => Str::snake($request->get('title')),
            'title' => $request->get('title'),
            'year' => $request->get('year'),
            'image' => $image->getData('data')['data']['title']
        ]);

        $module->save();
        //$module->offers()->sync($this->updateOffers($request));
        return (new ModuleResource($module->load('offers')))->response()->setStatusCode(Response::HTTP_CREATED);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Module $module
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Module $module)
    {
        $validate = [
            'year' => 'required|min:1',
            'image' => 'required'
        ];

        if ($module->title !== $request->get('title')) {
            $validate['title'] = 'required|unique:modules';
        }

        $request->validate($validate);


        $image = $request->get('image')['name'];
        if ($image !== $module->image) {
            $image = $this->imageController->update($request, $module->image);
            $image = $image->getData('data')['data']['title'];
        }

        $module->offers()->detach();
        $module->update([
            'id' => Str::snake($request->get('title')),
            'title' => $request->get('title'),
            'year' => $request->get('year'),
            'image' => $image
        ]);
        //$module->offers()->sync($this->updateOffers($request));
        return (new ModuleResource($module->load('offers')))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Module $module
     * @return
     * @throws \Exception
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }


    private function updateOffers(Request $request)
    {
        $offers = $request->get('offers');
        $offers_ids = [];

        foreach ($offers as $offer) {
            $offer = Offer::updateOrCreate(['id' => $offer['id']], $offer);
            $offers_ids[] = $offer->id;
        }
        return $offers_ids;
    }
}
