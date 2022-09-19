<?php

namespace App\Http\Controllers;

use App\ContentGrade;
use Illuminate\Http\Request;

class ContentGradesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $Content_grades = ContentGrade::all();
        $count = $Content_grades->count();

        return response()->json([
            'data'     =>  $Content_grades,
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

        $Content_grades = new ContentGrade(request()->all());
        $Content_grades->save();

        return response()->json([
            'data'    =>  $Content_grades
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentGrade $content_grade)
    {
        return response()->json([
            'data'   =>  $content_grade,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentGrade $content_grade)
    {
        $content_grade->update($request->all());

        return response()->json([
            'data'  =>  $content_grade
        ], 200);
    }

    public function destroy($id)
    {
        $Content_grades = ContentGrade::find($id);
        $Content_grades->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
