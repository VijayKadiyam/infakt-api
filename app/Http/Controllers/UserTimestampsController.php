<?php

namespace App\Http\Controllers;

use App\UserTimestamp;
use Illuminate\Http\Request;

class UserTimestampsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $user_timestamps = $request->company->user_timestamps;
        $count = $user_timestamps->count();

        return response()->json([
            'data'     =>  $user_timestamps,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_timestamp
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_timestamps = new UserTimestamp(request()->all());
        $request->company->user_timestamps()->save($user_timestamps);

        return response()->json([
            'data'    =>  $user_timestamps
        ], 201);
    }

    /*
     * To view a single user_timestamp
     *
     *@
     */
    public function show(UserTimestamp $user_timestamp)
    {
        return response()->json([
            'data'   =>  $user_timestamp,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_timestamp
     *
     *@
     */
    public function update(Request $request, UserTimestamp $user_timestamp)
    {
        $user_timestamp->update($request->all());

        return response()->json([
            'data'  =>  $user_timestamp
        ], 200);
    }

    public function destroy($id)
    {
        $user_timestamps = UserTimestamp::find($id);
        $user_timestamps->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
