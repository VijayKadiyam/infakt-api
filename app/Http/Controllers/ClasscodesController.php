<?php

namespace App\Http\Controllers;

use App\Classcode;
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
    public function index()
    {
        $classcodes = request()->company->classcodes()
            ->where('is_active', true)->get();
        // dd($classcodes);
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
            'classcode'  =>  'required',
        ]);

        $classcode = new Classcode($request->all());
        $request->company->classcodes()->save($classcode);

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