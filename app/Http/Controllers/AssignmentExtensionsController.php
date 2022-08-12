<?php

namespace App\Http\Controllers;

use App\AssignmentExtension;
use Illuminate\Http\Request;

class AssignmentExtensionsController extends Controller
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
        $assignment_extensions = request()->company->assignment_extensions()->get();

        return response()->json([
            'data'  =>  $assignment_extensions,
            'count' =>   sizeof($assignment_extensions),
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
            'assignment_id'  =>  'required',
            'user_id'  =>  'required',
        ]);

        $assignment_extension = new AssignmentExtension($request->all());
        $request->company->assignment_extensions()->save($assignment_extension);

        return response()->json([
            'data'  =>  $assignment_extension
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AssignmentExtension $assignment_extension)
    {
        return response()->json([
            'data'  =>  $assignment_extension
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignmentExtension $assignment_extension)
    {
        $request->validate([
            'assignment_id'  =>  'required',
            'user_id'  =>  'required',
        ]);

        $assignment_extension->update($request->all());

        return response()->json([
            'data'  =>  $assignment_extension
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
        $assignment_extension = request()->company->assignment_extensions()
            ->where('id', $id)->first();
        $assignment_extension->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
