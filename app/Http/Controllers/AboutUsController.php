<?php

namespace App\Http\Controllers;

use App\AboutUs;
use Illuminate\Http\Request;

class AboutUsController extends Controller

{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $about_us = AboutUs::all();
        $count = $about_us->count();

        return response()->json([
            'data'     =>  $about_us,
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
            'tagline'        =>  'required',
        ]);

        $about_us = new AboutUs(request()->all());
        $about_us->save();

        return response()->json([
            'data'    =>  $about_us
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(AboutUs $about_us)
    {
        return response()->json([
            'data'   =>  $about_us,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, AboutUs $about_us)
    {
        $about_us->update($request->all());

        return response()->json([
            'data'  =>  $about_us
        ], 200);
    }

    public function destroy($id)
    {
        $about_us = AboutUs::find($id);
        $about_us->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
