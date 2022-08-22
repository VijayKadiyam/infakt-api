<?php

namespace App\Http\Controllers;

use App\UserClasscode;
use Illuminate\Http\Request;

class UserClasscodesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $user_classcodes = request()->company->user_classcodes();
        if ($request->user_id) {
            $user_classcodes = $user_classcodes->where('user_id', '=', $request->user_id);
        }
        if ($request->standard_id) {
            $user_classcodes = $user_classcodes->where('standard_id', '=', $request->standard_id);
        }
        if ($request->section_id) {
            $user_classcodes = $user_classcodes->where('section_id', '=', $request->section_id);
        }
        if ($request->classcode_id) {
            $user_classcodes = $user_classcodes->where('classcode_id', '=', $request->classcode_id);
        }
        $user_classcodes = $user_classcodes->get();

        return response()->json([
            'data'     =>  $user_classcodes,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_classcodes = new UserClasscode(request()->all());
        $request->company->user_classcodes()->save($user_classcodes);

        return response()->json([
            'data'    =>  $user_classcodes
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(UserClasscode $user_classcode)
    {
        return response()->json([
            'data'   =>  $user_classcode,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, UserClasscode $user_classcode)
    {
        $user_classcode->update($request->all());

        return response()->json([
            'data'  =>  $user_classcode
        ], 200);
    }

    public function destroy($id)
    {
        $user_classcodes = UserClasscode::find($id);
        $user_classcodes->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
