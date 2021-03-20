<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleResourceCollection;
use App\Models\Image;
use App\Models\Module;
use App\Models\Offer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    private  $imageController;
    public function __construct() {
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
            'module' => 'required'
        ]);
        $types = $request->get('types');
        $module = null;
        if($types){
            $module = Module::where('id' ,$request->get('module'))
                ->with(['quizzes' => function($q) use ($types) {
                    $q->withCount(['tests as nbTests' => function($t) use ($types){
                        $t->whereIn('type' , $types);
                    }]);
                }])->first();

        }

        return (new ModuleResource($module))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|unique:modules',
            'year'=>'required|min:1',
            'image'=>'required',
            'offers' => 'required'
        ]);


        $image = $this->imageController->store($request);
        $module = new Module([
            'id' => Str::snake($request->get('title')),
            'title' => $request->get('title') ,
            'year' => $request->get('year'),
            'image'=> $image->getData('data')['data']['title']
        ]);

        $module->save();
        $module->offers()->sync($this->updateOffers($request));
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
            'year'=>'required|min:1',
            'image'=>'required'
        ];

        if($module->title !== $request->get('title')){
            $validate['title'] = 'required|unique:modules';
        }

        $request->validate($validate);


        $image = $request->get('image')['name'];
        if($image !== $module->image){
            $image = $this->imageController->update($request, $module->image);
            $image = $image->getData('data')['data']['title'];
        }

        $module->offers()->detach();
        $module->update([
            'id' => Str::snake($request->get('title')),
            'title' => $request->get('title'),
            'year' => $request->get('year'),
            'image'=> $image
        ]);
        $module->offers()->sync($this->updateOffers($request));
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


    private function updateOffers(Request $request){
        $offers = $request->get('offers');
        $offers_ids = [];

        foreach ($offers as $offer){
            $offer = Offer::updateOrCreate(['id' => $offer['id']], $offer);
            $offers_ids[] = $offer->id;
        }
        return $offers_ids;
    }
}
