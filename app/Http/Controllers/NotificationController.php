<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationResourceCollection;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\Matcher\Not;

class NotificationController extends Controller
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
    public function index()
    {
        return (new NotificationResourceCollection(Notification::select(['id' ,'title' ,'updated_at'])->paginate()))->response(Response::HTTP_OK);
    }

    public function count(){
        return response()->json(['notificationsCount' => Notification::count()]);
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
            'title' => 'required',
            'text' => 'required',
        ]);

        $notif = new Notification($request->all());
        $notif->save();

        return (new NotificationResource($notif))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function show(Notification $notification)
    {
        return (new NotificationResource($notification))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|Response|object
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required',
            'text' => 'required',
        ]);

        $notification->update($request->all());
        return (new NotificationResource($notification))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Notification $notification
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
