<?php

namespace App\Http\Controllers;

use App\Assignment;
use Illuminate\Http\Request;

class AssignmentsController extends Controller
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
        $assignments = request()->company->assignments()->get();
        return response()->json([
            'data'  =>  $assignments,
            'count' =>   sizeof($assignments),
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
            'assignment_type'  =>  'required'
        ]);

        $assignment = new Assignment($request->all());
        $request->company->assignments()->save($assignment);

        return response()->json([
            'data'  =>  $assignment
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        return response()->json([
            'data'  =>  $assignment
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'assignment_type'  =>  'required',
        ]);

        $assignment->update($request->all());

        return response()->json([
            'data'  =>  $assignment
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
        $assignment = request()->company->assignments()
            ->where('id', $id)->first();
        $assignment->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
