<?php

namespace App\Http\Controllers;

use App\AssignmentQuestionOption;
use Illuminate\Http\Request;

class AssignmentQuestionOptionsController extends Controller
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
        $assignment_question_options = request()->company->assignment_question_options()->get();

        return response()->json([
            'data'  =>  $assignment_question_options,
            'count' =>   sizeof($assignment_question_options),
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
            'assignment_question_id'  =>  'required',
        ]);

        $assignment_question_option = new AssignmentQuestionOption($request->all());
        $request->company->assignment_question_options()->save($assignment_question_option);

        return response()->json([
            'data'  =>  $assignment_question_option
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AssignmentQuestionOption $assignment_question_option)
    {
        return response()->json([
            'data'  =>  $assignment_question_option
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignmentQuestionOption $assignment_question_option)
    {
        $request->validate([
            'assignment_question_id'  =>  'required',
        ]);

        $assignment_question_option->update($request->all());

        return response()->json([
            'data'  =>  $assignment_question_option
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
        $assignment_question_option = request()->company->assignment_question_options()
            ->where('id', $id)->first();
        $assignment_question_option->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
