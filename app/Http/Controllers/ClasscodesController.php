<?php

namespace App\Http\Controllers;

use App\Classcode;
use App\Section;
use Illuminate\Http\Request;

class ClasscodesController extends Controller
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
    public function index(Section $section)
    {
        $classcodes = [];
        if (request()->company) {
            $classcodes = $section->classcodes()
                ->where('is_deleted', false);
            if (request()->classcode_id) {
                $classcodes = $classcodes->where('id', request()->classcode_id);
            }
            $classcodes = $classcodes->get();
        }
        return response()->json([
            'data'  =>  $classcodes,
            'count' =>   sizeof($classcodes),
            'success' =>  true,
        ], 200);
    }

    public function all_classcodes()
    {
        $classcodes = [];
        $classcodes = request()->company->classcodes;
        return response()->json([
            'data'  =>  $classcodes,
            'count' =>   sizeof($classcodes),
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
            'section_id'  =>  'required',
            'subject_name'  =>  'required',
        ]);

        $classcode = new Classcode($request->all());
        $request->company->classcodes()->save($classcode);

        if ($classcode) {
            $section = $classcode->section;
            $standard = $classcode->section->standard;
            $standard_name = $standard->name;
            $section_name = $section->name;
            $classcode_id = $classcode->id;

            $classcode->classcode = $standard_name . "" . $section_name . "/" . $classcode->subject_name . "/" . $classcode_id;
            $classcode->update();
        }
        return response()->json([
            'data'  =>  $classcode
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Classcode $classcode)
    {
        $classcode->section = $classcode->section;
        $classcode->standard = $classcode->standard;
        return response()->json([
            'data'  =>  $classcode
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classcode $classcode)
    {
        $request->validate([
            'standard_id'  =>  'required',
            'section_id'  =>  'required',
            'classcode'  =>  'required',
        ]);

        $classcode->update($request->all());

        return response()->json([
            'data'  =>  $classcode
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
        $classcode = request()->company->classcodes()
            ->where('id', $id)->first();
        $classcode->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
