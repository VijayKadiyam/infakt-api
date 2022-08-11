<?php

namespace App\Http\Controllers;

use App\ContentSubject;
use Illuminate\Http\Request;

class ContentSubjectsController extends Controller
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
        $content_subjects = ContentSubject::where('is_active', true)->get();
        return response()->json([
            'data'  =>  $content_subjects,
            'count' =>   sizeof($content_subjects),
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

        $content_subject = new ContentSubject(request()->all());
        $content_subject->save();

        return response()->json([
            'data'  =>  $content_subject
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContentSubject $content_subject)
    {
        return response()->json([
            'data'  =>  $content_subject
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentSubject $content_subject)
    {
        $request->validate([
            'content_id'  =>  'required',
        ]);

        $content_subject->update($request->all());

        return response()->json([
            'data'  =>  $content_subject
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
        $content_subject = ContentSubject::find($id);
        $content_subject->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
