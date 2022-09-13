<?php

namespace App\Http\Controllers;

use App\Feature;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $features = Feature::all();
        $count = $features->count();

        return response()->json([
            'data'     =>  $features,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        =>  'required',
        ]);

        $features = new Feature(request()->all());
        $features->save();

        return response()->json([
            'data'    =>  $features
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Feature $feature)
    {
        return response()->json([
            'data'   =>  $feature,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Feature $feature)
    {
        $feature->update($request->all());

        return response()->json([
            'data'  =>  $feature
        ], 200);
    }

    public function destroy($id)
    {
        $features = Feature::find($id);
        $features->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
