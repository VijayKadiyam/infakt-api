<?php

namespace App\Http\Controllers;

use App\Standard;
use Illuminate\Http\Request;

class StandardsController extends Controller
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
        $standards = request()->company->standards()
            ->where('is_active', true)->get();

        return response()->json([
            'data'  =>  $standards,
            'count' =>   sizeof($standards),
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
            'name'  =>  'required'
        ]);

        $standard = new Standard($request->all());
        $request->company->standards()->save($standard);

        return response()->json([
            'data'  =>  $standard
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Standard $standard)
    {
        return response()->json([
            'data'  =>  $standard
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Standard $standard)
    {
        $request->validate([
            'name'  =>  'required',
        ]);

        $standard->update($request->all());

        return response()->json([
            'data'  =>  $standard
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
        $standard = request()->company->standards()
            ->where('id', $id)->first();
        $standard->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
