<?php

namespace App\Http\Controllers;

use App\UserAssignment;
use App\UserAssignmentSelectedAnswer;
use Illuminate\Http\Request;

class UserAssignmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id && $request->assignment_id) {
            $user_assignments = request()->company->user_assignments()
                ->where('user_id', '=', $request->user_id)
                ->where('assignment_id', '=', $request->assignment_id)
                ->get();
        } else if ($request->user_id) {
            $user_assignments = request()->company->user_assignments()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {
            $user_assignments = request()->company->user_assignments;
            $count = $user_assignments->count();
        }

        return response()->json([
            'data'     =>  $user_assignments,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_assignment
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        if ($request->id == null || $request->id == '') {
            // Save User Assignment
            $user_assignment = new UserAssignment(request()->all());
            $request->company->user_assignments()->save($user_assignment);

            // ---------------------------------------------------

            // Save User Assignment Selected Answer
            if (isset($request->user_assignment_selected_answers))
                foreach ($request->user_assignment_selected_answers as $selectedAnswer) {
                    $user_assignment_selected_answer = new UserAssignmentSelectedAnswer($selectedAnswer);
                    $user_assignment->user_assignment_selected_answers()->save($user_assignment_selected_answer);
                }
        } else {
            // Update Assignmnet
            $user_assignment = UserAssignment::find($request->id);
            $user_assignment->update($request->all());

            // Check if User Assignment Selected Answer deleted
            if (isset($request->user_assignment_selected_answers)) {
                $userAssignmentSelectedAnswerIdResponseArray = array_pluck($request->user_assignment_selected_answers, 'id');
            } else
                $userAssignmentSelectedAnswerIdResponseArray = [];
            $userAssignmentId = $user_assignment->id;
            $userAssignmentSelectedAnswerIdArray = array_pluck(UserAssignmentSelectedAnswer::where('user_assignment_id', '=', $userAssignmentId)->get(), 'id');
            $differenceUserAssignmentSelectedAnswerIds = array_diff($userAssignmentSelectedAnswerIdArray, $userAssignmentSelectedAnswerIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceUserAssignmentSelectedAnswerIds)
                foreach ($differenceUserAssignmentSelectedAnswerIds as $differenceAssignmentClasscodeId) {
                    $userAssignmentSelectedAnswer = UserAssignmentSelectedAnswer::find($differenceAssignmentClasscodeId);
                    $userAssignmentSelectedAnswer->delete();
                }

            // ---------------------------------------------------

            // Update User Assignment Selected Answer
            if (isset($request->user_assignment_selected_answers))
                foreach ($request->user_assignment_selected_answers as $selectedAnswer) {
                    if (!isset($selectedAnswer['id'])) {
                        $user_assignment_selected_answer = new UserAssignmentSelectedAnswer($selectedAnswer);
                        $user_assignment->user_assignment_selected_answers()->save($user_assignment_selected_answer);
                    } else {
                        $user_assignment_selected_answer = UserAssignmentSelectedAnswer::find($selectedAnswer['id']);
                        $user_assignment_selected_answer->update($selectedAnswer);
                    }
                }
        }

        $user_assignment->user = $user_assignment->user;
        $user_assignment->assignment = $user_assignment->assignment;
        $user_assignment->user_assignment_selected_answers = $user_assignment->user_assignment_selected_answers;

        return response()->json([
            'data'    =>  $user_assignment
        ], 201);
    }

    /*
     * To view a single user_assignment
     *
     *@
     */
    public function show(UserAssignment $user_assignment)
    {
        $user_assignment->user = $user_assignment->user;
        $user_assignment->assignment = $user_assignment->assignment;
        $user_assignment->user_assignment_selected_answers = $user_assignment->user_assignment_selected_answers;

        return response()->json([
            'data'   =>  $user_assignment,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_assignment
     *
     *@
     */
    public function update(Request $request, UserAssignment $user_assignment)
    {
        $user_assignment->update($request->all());

        return response()->json([
            'data'  =>  $user_assignment
        ], 200);
    }

    public function destroy($id)
    {
        $user_assignment = UserAssignment::find($id);
        $user_assignment->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
