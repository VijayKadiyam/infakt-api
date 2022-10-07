<?php

namespace App\Http\Controllers;

use App\UserSubject;
use Illuminate\Http\Request;

class UserSubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
        $user_subjects = request()->company->user_subjects()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

        $user_subjects = request()->company->user_subjects();
            $count = $user_subjects->count();
        }
//    dd($user_subjects);
        return response()->json([
            'data'     =>  $user_subjects,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_subject
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $user_subject = new UserSubject(request()->all());
        $request->company->user_subjects()->save($user_subject);

        return response()->json([
            'data'    => $user_subject
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(UserSubject $user_subject)
    {
        return response()->json([
            'data'   =>  $user_subject,

        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, UserSubject $user_subject)
    {
        $user_subject->update($request->all());

        return response()->json([
            'data'  =>  $user_subject
        ], 200);
    }

    public function destroy($id)
    {
        $user_subject = UserSubject::find($id);
        $user_subject->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}