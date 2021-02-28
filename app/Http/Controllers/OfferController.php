<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleResourceCollection;
use App\Http\Resources\OfferCollection;
use App\Http\Resources\OfferResource;
use App\Models\Module;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class OfferController extends Controller
{

    private $imageController;

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
        return (new OfferCollection(Offer::paginate()))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|unique:offers',
            'price'=>'required|min:0',
            'state'=>'required|min:0|max:1',
            'image'=>'required'
        ]);


        $image = $this->imageController->store($request);
        $offer = new Offer([
            'id' => Offer::makeId($request->get('title')) ,
            'title' => $request->get('title') ,
            'price' => $request->get('price'),
            'image'=> $image->getData('data')['data']['title'],
            'state'=> $request->get('state')
        ]);

        $offer->save();
        return (new OfferResource($offer))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Offer  $offer)
    {
        $validate = [
            'price'=>'required|min:0',
            'state'=>'required|min:0|max:1',
            'image'=>'required'
        ];

        if($offer->title !== $request->get('title')){
            $validate['title'] = 'required|unique:offers';
        }

        $request->validate($validate);


        $image = $request->get('image')['name'];
        if($image !== $offer->image){
            $image = $this->imageController->update($request, $offer->image);
            $image = $image->getData('data')['data']['title'];
        }

        $offer->update([
            'id' => Offer::makeId($request->get('title')),
            'title' => $request->get('title') ,
            'price' => $request->get('year'),
            'image'=> $image,
            'state'=> $request->get('state')
        ]);

        return (new OfferResource($offer))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
