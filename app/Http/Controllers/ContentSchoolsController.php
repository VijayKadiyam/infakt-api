<?php

namespace App\Http\Controllers;

use App\ContentSchool;
use Illuminate\Http\Request;

class ContentSchoolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $Content_schools = ContentSchool::all();
        $count = $Content_schools->count();

        return response()->json([
            'data'     =>  $Content_schools,
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
            'content_id'        =>  'required',
        ]);

        $Content_schools = new ContentSchool(request()->all());
        $Content_schools->save();

        return response()->json([
            'data'    =>  $Content_schools
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentSchool $content_school)
    {
        return response()->json([
            'data'   =>  $content_school,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentSchool $content_school)
    {
        $content_school->update($request->all());

        return response()->json([
            'data'  =>  $content_school
        ], 200);
    }

    public function destroy($id)
    {
        $Content_schools = ContentSchool::find($id);
        $Content_schools->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
