<?php

namespace App\Http\Controllers;

use App\UserAssignment;
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
        if ($request->user_id) {
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

        $user_assignment = new UserAssignment(request()->all());
        $request->company->user_assignments()->save($user_assignment);

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
