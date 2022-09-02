<?php

namespace App\Http\Controllers;

use App\ContentDescription;
use Illuminate\Http\Request;

class ContentDescriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $content_descriptions = ContentDescription::all();
        $count = $content_descriptions->count();

        return response()->json([
            'data'     =>  $content_descriptions,
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
            'level'        =>  'required',
        ]);

        $content_descriptions = new ContentDescription(request()->all());
        $content_descriptions->save();

        return response()->json([
            'data'    =>  $content_descriptions
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentDescription $content_description)
    {
        return response()->json([
            'data'   =>  $content_description,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentDescription $content_description)
    {
        $content_description->update($request->all());

        return response()->json([
            'data'  =>  $content_description
        ], 200);
    }

    public function destroy($id)
    {
        $content_descriptions = ContentDescription::find($id);
        $content_descriptions->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
