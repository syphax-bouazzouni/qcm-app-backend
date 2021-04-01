<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ImageController extends Controller
{

    const IMAGES_DIR_PATH = 'images'.DIRECTORY_SEPARATOR;

    public function __construct() {
        // $this->middleware('auth:api');
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
            'image' => 'required'
        ]);

        //store file into document folder
        $base64image = $request->get('image')['source'];
        if(strlen($base64image)>0){
            @list($type, $file_data) = explode(';', $base64image);
            @list(, $file_data) = explode(',', $file_data);
            $type = explode(";", explode("/", $base64image)[1])[0];
            $file_name = Str::random(15) . '.' . $type;
            $path = 'images/' .  $file_name;
            Storage::disk('private')->put($path, base64_decode($file_data));

            //store your file into database
            $document = new Image(['title' => basename($file_name)]);
            $document->save();
             return (new ImageResource($document))->response(Response::HTTP_CREATED);
        }else{
            return null;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Image $image
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Image $image)
    {

        return Storage::disk('private')->download(self::IMAGES_DIR_PATH.$image->title);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        Image::destroy($id);
        Storage::disk('private')->delete(self::IMAGES_DIR_PATH.$id);

        return $this->store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Image  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        Storage::delete('Images'.DIRECTORY_SEPARATOR.$image->title);
        $image->delete();
    }
}
