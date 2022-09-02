<?php

namespace App\Http\Controllers;

use App\Grade;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $grades = Grade::all();
        $count = $grades->count();

        return response()->json([
            'data'     =>  $grades,
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
            'name'        =>  'required',
        ]);

        $grades = new Grade(request()->all());
        $grades->save();

        return response()->json([
            'data'    =>  $grades
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Grade $grade)
    {
        return response()->json([
            'data'   =>  $grade,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Grade $grade)
    {
        $grade->update($request->all());

        return response()->json([
            'data'  =>  $grade
        ], 200);
    }

    public function destroy($id)
    {
        $grades = Grade::find($id);
        $grades->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
