<?php

namespace App\Http\Controllers;

use App\Section;
use App\Standard;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Standard $standard)
    {
        $sections = $standard->sections()
            ->where('is_active', true)->get();

        return response()->json([
            'data'  =>  $sections,
            'count' =>   sizeof($sections),
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
            'standard_id'  =>  'required',
            'name'  =>  'required',
        ]);

        $section = new Section($request->all());
        $request->company->sections()->save($section);

        return response()->json([
            'data'  =>  $section
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        $section->standard = $section->standard;
        return response()->json([
            'data'  =>  $section
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'standard_id'  =>  'required',
            'name'  =>  'required',
        ]);

        $section->update($request->all());

        return response()->json([
            'data'  =>  $section
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
        $section = request()->company->sections()
            ->where('id', $id)->first();
        $section->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
