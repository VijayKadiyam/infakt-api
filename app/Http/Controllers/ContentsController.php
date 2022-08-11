<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;

class ContentsController extends Controller
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
        $contents = Content::where('is_deleted', false)->get();
        return response()->json([
            'data'  =>  $contents,
            'count' =>   sizeof($contents), 
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
            'content_name'  =>  'required'
        ]);

        $content = new Content(request()->all());
        $content->save();

        return response()->json([
            'data'  =>  $content
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        return response()->json([
            'data'  =>  $content
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        $request->validate([
            'content_name'  =>  'required',
        ]);

        $content->update($request->all());

        return response()->json([
            'data'  =>  $content
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
        $content = Content::find($id);
        $content->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
