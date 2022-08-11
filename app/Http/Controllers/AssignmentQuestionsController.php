<?php

namespace App\Http\Controllers;

use App\AssignmentQuestion;
use Illuminate\Http\Request;

class AssignmentQuestionsController extends Controller
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
        $assignment_questions = request()->company->assignment_questions()->get();

        return response()->json([
            'data'  =>  $assignment_questions,
            'count' =>   sizeof($assignment_questions),
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
        ]);

        $assignment_question = new AssignmentQuestion($request->all());
        $request->company->assignment_questions()->save($assignment_question);

        return response()->json([
            'data'  =>  $assignment_question
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AssignmentQuestion $assignment_question)
    {
        return response()->json([
            'data'  =>  $assignment_question
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignmentQuestion $assignment_question)
    {
        $request->validate([
            'assignment_id'  =>  'required',
        ]);

        $assignment_question->update($request->all());

        return response()->json([
            'data'  =>  $assignment_question
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
        $assignment_question = request()->company->assignment_questions()
            ->where('id', $id)->first();
        $assignment_question->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
