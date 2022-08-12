<?php

namespace App\Http\Controllers;

use App\UserAssignmentSelectedAnswer;
use Illuminate\Http\Request;

class UserAssignmentSelectedAnswersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $user_assignment_selected_answers = request()->company->user_assignment_selected_answers()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $user_assignment_selected_answers = request()->company->user_assignment_selected_answers;
            $count = $user_assignment_selected_answers->count();
        }

        return response()->json([
            'data'     =>  $user_assignment_selected_answers,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_assignment_selected_answer
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_assignment_selected_answer = new UserAssignmentSelectedAnswer(request()->all());
        $request->company->user_assignment_selected_answers()->save($user_assignment_selected_answer);

        return response()->json([
            'data'    =>  $user_assignment_selected_answer
        ], 201);
    }

    /*
     * To view a single user_assignment_selected_answer
     *
     *@
     */
    public function show(UserAssignmentSelectedAnswer $user_assignment_selected_answer)
    {
        return response()->json([
            'data'   =>  $user_assignment_selected_answer,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_assignment_selected_answer
     *
     *@
     */
    public function update(Request $request, UserAssignmentSelectedAnswer $user_assignment_selected_answer)
    {
        $user_assignment_selected_answer->update($request->all());

        return response()->json([
            'data'  =>  $user_assignment_selected_answer
        ], 200);
    }

    public function destroy($id)
    {
        $user_assignment_selected_answer = UserAssignmentSelectedAnswer::find($id);
        $user_assignment_selected_answer->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
