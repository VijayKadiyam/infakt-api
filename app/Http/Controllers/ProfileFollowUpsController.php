<?php

namespace App\Http\Controllers;

use App\ProfileFollowUp;
use Illuminate\Http\Request;

class ProfileFollowUpsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'site']);
    }

    /*
     * To get all ProfileFollowUp
       *
     *@
     */
    public function index(Request $request)
    {
        $profile_follow_ups = $request->site->profile_follow_ups()->get();

        return response()->json([
            'data'     =>  $profile_follow_ups
        ], 200);
    }

    /*
     * To store a new ProfileFollowUp
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    =>  'required',
            'profile_id'    =>  'required',
        ]);

        $profile_follow_up = new ProfileFollowUp($request->all());
        $request->site->profile_follow_ups()->save($profile_follow_up);

        return response()->json([
            'data'    =>  $profile_follow_up
        ], 201);
    }

    /*
     * To view a single ProfileFollowUp
     *
     *@
     */
    public function show(ProfileFollowUp $profile_follow_up)
    {
        return response()->json([
            'data'   =>  $profile_follow_up,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a ProfileFollowUp
     *
     *@
     */
    public function update(Request $request, ProfileFollowUp $profile_follow_up)
    {

        $profile_follow_up->update($request->all());

        return response()->json([
            'data'  =>  $profile_follow_up
        ], 200);
    }

    public function destroy($id)
    {
        $profile_follow_up = ProfileFollowUp::find($id);
        $profile_follow_up->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
