<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavResourceCollection;
use App\Http\Resources\ReportResource;
use App\Http\Resources\ReportResourceCollection;
use App\Models\Fav;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        if (auth()->user()->is_admin){
            return (new ReportResourceCollection(Report::with(['test.questions' ,'user'])->paginate()))->response();
        }else{
            return response()->json()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }
    }

    public function count(){
        if (auth()->user()->is_admin){
            return response()->json(['reportsCount' => Report::count()]);
        }else{
            return response()->json()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }
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
            'subject' => 'required',
            'user' => 'required',
            'test' => 'required',
        ]);

        $report = new Report($request->all());
        $report->save();

        return (new ReportResource($report))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Report $report
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
