<?php

namespace App\Http\Controllers;

use App\UserStandard;
use Illuminate\Http\Request;

class UserStandardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $user_standards = request()->company->user_standards()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $user_standards = request()->company->user_standards;
            $count = $user_standards->count();
        }

        return response()->json([
            'data'     =>  $user_standards,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_standard
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_standard = new UserStandard(request()->all());
        // dd($request->company());
        $request->company->user_standards()->save($user_standard);

        return response()->json([
            'data'    =>  $user_standard
        ], 201);
    }

    /*
     * To view a single user_standard
     *
     *@
     */
    public function show(UserStandard $user_standard)
    {
        return response()->json([
            'data'   =>  $user_standard,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_standard
     *
     *@
     */
    public function update(Request $request, UserStandard $user_standard)
    {
        $user_standard->update($request->all());

        return response()->json([
            'data'  =>  $user_standard
        ], 200);
    }

    public function destroy($id)
    {
        $user_standard = UserStandard::find($id);
        $user_standard->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
