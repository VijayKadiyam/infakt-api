<?php

namespace App\Http\Controllers;

use App\ContentMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentMediasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $content_medias = ContentMedia::all();
        return response()->json([
            'data'  =>  $content_medias,
            'count' =>   sizeof($content_medias),
            'success' =>  true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'content_id'  =>  'required'
        ]);

        $content_media = new ContentMedia(request()->all());
        $content_media->save();

        if ($request->hasFile('mediapath')) {
            $file = $request->file('mediapath');
            $name = $request->filename ?? 'photo.jpg';
            // $name = $name . $file->getClientOriginalExtension();;
            $mediapath = 'infakt/content_medias/' . $name;
            Storage::disk('s3')->put($mediapath, file_get_contents($file), 'public');
            $content_media->mediapath = $mediapath;
            $content_media->update();
          }
      
        return response()->json([
            'data'  =>  $content_media
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContentMedia $content_media)
    {
        return response()->json([
            'data'  =>  $content_media
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentMedia $content_media)
    {
        $request->validate([
            'content_id'  =>  'required',
        ]);

        $content_media->update($request->all());

        return response()->json([
            'data'  =>  $content_media
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content_media = ContentMedia::find($id);
        $content_media->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
