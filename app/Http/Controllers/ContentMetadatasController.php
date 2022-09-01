<?php

namespace App\Http\Controllers;

use App\ContentMetadata;
use Illuminate\Http\Request;

class ContentMetadatasController extends Controller
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
        $content_metadatas = ContentMetadata::all();
        return response()->json([
            'data'  =>  $content_metadatas,
            'count' =>   sizeof($content_metadatas),
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

        $content_metadata = new ContentMetadata(request()->all());
        $content_metadata->save();

        return response()->json([
            'data'  =>  $content_metadata
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContentMetadata $content_metadata)
    {
        return response()->json([
            'data'  =>  $content_metadata
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentMetadata $content_metadata)
    {
        $request->validate([
            'content_id'  =>  'required',
        ]);

        $content_metadata->update($request->all());

        return response()->json([
            'data'  =>  $content_metadata
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
        $content_metadata = ContentMetadata::find($id);
        $content_metadata->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
