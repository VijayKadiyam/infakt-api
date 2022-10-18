<?php

namespace App\Http\Controllers;

use App\UserAssignmentTiming;
use Illuminate\Http\Request;

class UserAssignmentTimingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $user_assignment_timings = $request->company->user_assignment_timings;
        $count = $user_assignment_timings->count();

        return response()->json([
            'data'     =>  $user_assignment_timings,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_assignment_timing
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_assignment_timings = new UserAssignmentTiming(request()->all());
        $request->company->user_assignment_timings()->save($user_assignment_timings);

        return response()->json([
            'data'    =>  $user_assignment_timings
        ], 201);
    }

    /*
     * To view a single user_assignment_timing
     *
     *@
     */
    public function show(UserAssignmentTiming $user_assignment_timing)
    {
        return response()->json([
            'data'   =>  $user_assignment_timing,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_assignment_timing
     *
     *@
     */
    public function update(Request $request, UserAssignmentTiming $user_assignment_timing)
    {
        $user_assignment_timing->update($request->all());

        return response()->json([
            'data'  =>  $user_assignment_timing
        ], 200);
    }

    public function destroy($id)
    {
        $user_assignment_timings = UserAssignmentTiming::find($id);
        $user_assignment_timings->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
