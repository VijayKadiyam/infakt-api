<?php

namespace App\Http\Controllers;

use App\AssignmentClasscode;
use Illuminate\Http\Request;

class AssignmentClasscodesController extends Controller
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
        $assignment_classcodes = request()->company->assignment_classcodes()->get();

        return response()->json([
            'data'  =>  $assignment_classcodes,
            'count' =>   sizeof($assignment_classcodes),
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
            'classcode_id'  =>  'required',
        ]);

        $assignment_classcode = new AssignmentClasscode($request->all());
        $request->company->assignment_classcodes()->save($assignment_classcode);

        return response()->json([
            'data'  =>  $assignment_classcode
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AssignmentClasscode $assignment_classcode)
    {
        return response()->json([
            'data'  =>  $assignment_classcode
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignmentClasscode $assignment_classcode)
    {
        $request->validate([
            'classcode_id'  =>  'required',
        ]);

        $assignment_classcode->update($request->all());

        return response()->json([
            'data'  =>  $assignment_classcode
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
        $assignment_classcode = request()->company->assignment_classcodes()
            ->where('id', $id)->first();
        $assignment_classcode->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
